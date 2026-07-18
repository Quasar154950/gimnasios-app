<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReservaTurno;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileReservaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cliente = $request->user()->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado.',
            ], 422);
        }

        $this->generarTurnosProximosDias(
            (int) $cliente->abogado_id
        );

        $reservas = ReservaTurno::with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) use ($cliente) {
                $query->where(
                    'abogado_id',
                    $cliente->abogado_id
                );
            })
            ->get()
            ->filter(function ($reserva) {
                if (! $reserva->turno) {
                    return false;
                }

                $inicioTurno = Carbon::parse(
                    $reserva->turno->fecha
                    . ' '
                    . $reserva->turno->hora_inicio
                );

                return $inicioTurno->isFuture();
            })
            ->sortBy(function ($reserva) {
                return $reserva->turno->fecha
                    . ' '
                    . $reserva->turno->hora_inicio;
            })
            ->map(function ($reserva) {
                $turno = $reserva->turno;

                $inicioTurno = Carbon::parse(
                    $turno->fecha
                    . ' '
                    . $turno->hora_inicio
                );

                return [
                    'id' => $reserva->id,
                    'turno_id' => $turno->id,
                    'actividad' => $turno->actividad,
                    'profesor' => $turno->profesor,
                    'fecha' => $turno->fecha,
                    'hora_inicio' => $turno->hora_inicio,
                    'hora_fin' => $turno->hora_fin,
                    'estado' => 'Reservada',
                    'puede_cancelar' => $inicioTurno->greaterThan(
                        now()->copy()->addHour()
                    ),
                ];
            })
            ->values();

        $turnosReservadosIds = $reservas
            ->pluck('turno_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $turnos = Turno::withCount('reservas')
            ->where(
                'abogado_id',
                $cliente->abogado_id
            )
            ->where('activo', true)
            ->whereDate(
                'fecha',
                '>=',
                now()->toDateString()
            )
            ->whereDate(
                'fecha',
                '<=',
                now()->copy()->addDays(7)->toDateString()
            )
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->orderBy('actividad')
            ->get()
            ->filter(function ($turno) {
                $inicioTurno = Carbon::parse(
                    $turno->fecha
                    . ' '
                    . $turno->hora_inicio
                );

                return $inicioTurno->isFuture();
            })
            ->map(function ($turno) use (
                $turnosReservadosIds
            ) {
                $reservado = in_array(
                    (int) $turno->id,
                    $turnosReservadosIds,
                    true
                );

                $reservados = (int) $turno->reservas_count;
                $cupoMaximo = (int) $turno->cupo_maximo;

                $cuposDisponibles = max(
                    0,
                    $cupoMaximo - $reservados
                );

                $completo = $cuposDisponibles === 0;

                return [
                    'id' => $turno->id,
                    'actividad' => $turno->actividad,
                    'profesor' => $turno->profesor,
                    'fecha' => $turno->fecha,
                    'hora_inicio' => $turno->hora_inicio,
                    'hora_fin' => $turno->hora_fin,
                    'cupo_maximo' => $cupoMaximo,
                    'reservados' => $reservados,
                    'cupos_disponibles' => $cuposDisponibles,
                    'reservado' => $reservado,
                    'completo' => $completo,
                    'puede_reservar' => ! $reservado
                        && ! $completo,
                ];
            })
            ->values();

        return response()->json([
            'reservas' => $reservas,
            'turnos' => $turnos,
        ]);
    }

    public function reservar(
        Request $request,
        Turno $turno
    ): JsonResponse {
        $cliente = $request->user()->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado.',
            ], 422);
        }

        if (
            (int) $turno->abogado_id
            !== (int) $cliente->abogado_id
        ) {
            return response()->json([
                'message' => 'No tenés permiso para reservar este turno.',
            ], 403);
        }

        if (! $turno->activo) {
            return response()->json([
                'message' => 'Este turno no se encuentra activo.',
            ], 422);
        }

        $inicioTurno = Carbon::parse(
            $turno->fecha
            . ' '
            . $turno->hora_inicio
        );

        if ($inicioTurno->isPast()) {
            return response()->json([
                'message' => 'Este turno ya comenzó o ya pasó.',
            ], 422);
        }

        $reservasDelDia = ReservaTurno::with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) use (
                $turno,
                $cliente
            ) {
                $query->where(
                    'abogado_id',
                    $cliente->abogado_id
                )
                    ->whereDate(
                        'fecha',
                        $turno->fecha
                    );
            })
            ->get();

        foreach ($reservasDelDia as $reserva) {
            if (! $reserva->turno) {
                continue;
            }

            if (
                (int) $reserva->turno_id
                === (int) $turno->id
            ) {
                return response()->json([
                    'message' => 'Ya tenés reservado este turno.',
                ], 422);
            }

            if (
                $reserva->turno->actividad
                === $turno->actividad
            ) {
                return response()->json([
                    'message' => 'Ya tenés una reserva de '
                        . $turno->actividad
                        . ' para este día.',
                ], 422);
            }

            if (
                $reserva->turno->hora_inicio
                    < $turno->hora_fin
                &&
                $reserva->turno->hora_fin
                    > $turno->hora_inicio
            ) {
                return response()->json([
                    'message' => 'Ya tenés otra reserva en un horario que se superpone.',
                ], 422);
            }
        }

        $reservados = $turno->reservas()->count();

        if ($reservados >= $turno->cupo_maximo) {
            return response()->json([
                'message' => 'El turno está completo.',
            ], 422);
        }

        ReservaTurno::create([
            'cliente_id' => $cliente->id,
            'turno_id' => $turno->id,
            'estado' => 'reservado',
        ]);

        return response()->json([
            'message' => 'Turno reservado correctamente.',
        ], 201);
    }

    public function cancelar(
        Request $request,
        ReservaTurno $reserva
    ): JsonResponse {
        $cliente = $request->user()->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado.',
            ], 422);
        }

        if (
            (int) $reserva->cliente_id
            !== (int) $cliente->id
        ) {
            return response()->json([
                'message' => 'No tenés permiso para cancelar esta reserva.',
            ], 403);
        }

        $reserva->load('turno');

        if (! $reserva->turno) {
            return response()->json([
                'message' => 'No se encontró el turno asociado a esta reserva.',
            ], 404);
        }

        if (
            (int) $reserva->turno->abogado_id
            !== (int) $cliente->abogado_id
        ) {
            return response()->json([
                'message' => 'No tenés permiso para cancelar esta reserva.',
            ], 403);
        }

        $inicioTurno = Carbon::parse(
            $reserva->turno->fecha
            . ' '
            . $reserva->turno->hora_inicio
        );

        if (
            $inicioTurno->lessThanOrEqualTo(
                now()->copy()->addHour()
            )
        ) {
            return response()->json([
                'message' => 'No se puede cancelar una reserva dentro de la hora previa o cuando la actividad ya comenzó.',
            ], 422);
        }

        $reserva->delete();

        return response()->json([
            'message' => 'Reserva cancelada correctamente.',
        ]);
    }

    private function generarTurnosProximosDias(
        int $abogadoId
    ): void {
        $actividades = [
            'Spinning' => [
                'profesor' => 'A confirmar',
                'cupo_maximo' => 10,
            ],
            'Pilates' => [
                'profesor' => 'A confirmar',
                'cupo_maximo' => 10,
            ],
        ];

        $horarios = [
            ['08:00', '09:00'],
            ['09:00', '10:00'],
            ['14:00', '15:00'],
            ['15:00', '16:00'],
            ['18:00', '19:00'],
            ['19:00', '20:00'],
            ['20:00', '21:00'],
        ];

        for ($i = 0; $i <= 7; $i++) {
            $fecha = now()->copy()->addDays($i);

            if ($fecha->isWeekend()) {
                continue;
            }

            foreach (
                $actividades as $actividad => $datos
            ) {
                foreach (
                    $horarios as [$horaInicio, $horaFin]
                ) {
                    Turno::updateOrCreate(
                        [
                            'abogado_id' => $abogadoId,
                            'actividad' => $actividad,
                            'fecha' => $fecha->toDateString(),
                            'hora_inicio' => $horaInicio,
                        ],
                        [
                            'abogado_id' => $abogadoId,
                            'hora_fin' => $horaFin,
                            'profesor' => $datos['profesor'],
                            'cupo_maximo' => $datos['cupo_maximo'],
                            'activo' => true,
                        ]
                    );
                }
            }
        }
    }
}
