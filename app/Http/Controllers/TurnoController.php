<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Cliente;
use App\Models\ReservaTurno;
use App\Models\Asistencia;
use Carbon\Carbon;

class TurnoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'cliente') {
            $cliente = Cliente::where('user_id', $user->id)->first();

            if (!$cliente) {
                return redirect()->route('cliente.dashboard')
                    ->with('error', 'No existe cliente asociado.');
            }

            $abogadoId = $cliente->abogado_id;
        } else {
            $abogadoId = $user->id;
        }

        $this->generarTurnosProximosDias($abogadoId);

        $fechaSeleccionada = request('fecha', now()->toDateString());
        $fecha = Carbon::parse($fechaSeleccionada);
        $cerradoFinDeSemana = $fecha->isSaturday() || $fecha->isSunday();

        $turnos = Turno::with('reservas.cliente')
            ->where('abogado_id', $abogadoId)
            ->where('activo', true)
            ->whereDate('fecha', $fechaSeleccionada)
            ->orderBy('hora_inicio')
            ->orderBy('actividad')
            ->get();

        $presentesAhora = Asistencia::where('presente', true)
            ->whereNull('hora_salida')
            ->whereHas('cliente', function ($query) use ($abogadoId) {
                $query->where('abogado_id', $abogadoId);
            })
            ->count();

        if ($user->role === 'cliente') {
            return view('turnos.index', compact(
                'turnos',
                'fechaSeleccionada',
                'cerradoFinDeSemana',
                'presentesAhora'
            ));
        }

        $clientes = Cliente::where('abogado_id', $abogadoId)
            ->where('archivado', false)
            ->orderBy('nombre')
            ->get();

        return view('turnos.admin', compact(
            'turnos',
            'fechaSeleccionada',
            'cerradoFinDeSemana',
            'presentesAhora',
            'clientes'
        ));
    }

    private function generarTurnosProximosDias(int $abogadoId): void
    {
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

            foreach ($actividades as $actividad => $datos) {
                foreach ($horarios as [$horaInicio, $horaFin]) {
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

    public function reservar(Turno $turno)
    {
        $cliente = Cliente::where('user_id', auth()->id())->first();

        if (!$cliente) {
            return back()->with('error', 'No existe cliente asociado.');
        }

        if ((int) $turno->abogado_id !== (int) $cliente->abogado_id) {
            abort(403);
        }

        $existe = ReservaTurno::where('cliente_id', $cliente->id)
            ->where('turno_id', $turno->id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya tienes reservado este turno.');
        }

        $reservados = $turno->reservas()->count();

        if ($reservados >= $turno->cupo_maximo) {
            return back()->with('error', 'El turno está completo.');
        }

        ReservaTurno::create([
            'cliente_id' => $cliente->id,
            'turno_id' => $turno->id,
            'estado' => 'reservado',
        ]);

        return back()->with('success', 'Turno reservado correctamente.');
    }

    public function cancelarReserva(ReservaTurno $reserva)
    {
        $cliente = Cliente::where('user_id', auth()->id())->first();

        if (!$cliente || $reserva->cliente_id !== $cliente->id) {
            return redirect()->route('cliente.dashboard');
        }

        $reserva->load('turno');

        if (!$reserva->turno) {
            return back()->with('error', 'No se encontró el turno asociado a esta reserva.');
        }

        if ((int) $reserva->turno->abogado_id !== (int) $cliente->abogado_id) {
            abort(403);
        }

        $inicioTurno = Carbon::parse($reserva->turno->fecha . ' ' . $reserva->turno->hora_inicio);

        if ($inicioTurno->lessThanOrEqualTo(now()->copy()->addHour())) {
            return back()->with('error', 'No se puede cancelar una reserva dentro de la hora previa o cuando la actividad ya comenzó.');
        }

        $reserva->delete();

        return back()->with('success', 'Reserva cancelada correctamente.');
    }

    public function reservarAdmin(Turno $turno)
    {
        if (auth()->user()->role !== 'abogado') {
            abort(403);
        }

        if ((int) $turno->abogado_id !== (int) auth()->id()) {
            abort(403);
        }

        $clienteId = request('cliente_id');

        $cliente = Cliente::where('id', $clienteId)
            ->where('abogado_id', auth()->id())
            ->first();

        if (!$cliente) {
            return back()->with('error', 'Seleccioná un socio válido.');
        }

        $inicioTurno = Carbon::parse($turno->fecha . ' ' . $turno->hora_inicio);

        if ($inicioTurno->isPast()) {
            return back()->with('error', 'Este turno ya comenzó o ya pasó.');
        }

        $reservasDelDia = ReservaTurno::with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) use ($turno) {
                $query->where('abogado_id', auth()->id())
                    ->whereDate('fecha', $turno->fecha);
            })
            ->get();

        foreach ($reservasDelDia as $reserva) {
            if (!$reserva->turno) {
                continue;
            }

            if ($reserva->turno_id === $turno->id) {
                return back()->with('error', 'Este socio ya tiene reservado este turno.');
            }

            if ($reserva->turno->actividad === $turno->actividad) {
                return back()->with('error', 'Este socio ya tiene una reserva de ' . $turno->actividad . ' para este día.');
            }

            if (
                $reserva->turno->hora_inicio < $turno->hora_fin &&
                $reserva->turno->hora_fin > $turno->hora_inicio
            ) {
                return back()->with('error', 'Este socio ya tiene otra reserva en un horario que se superpone.');
            }
        }

        $turno->load('reservas');

        if ($turno->reservas->count() >= $turno->cupo_maximo) {
            return back()->with('error', 'El turno está completo.');
        }

        ReservaTurno::create([
            'cliente_id' => $cliente->id,
            'turno_id' => $turno->id,
            'estado' => 'reservado',
        ]);

        return back()->with('success', 'Turno reservado manualmente.');
    }

    public function cancelarReservaAdmin(ReservaTurno $reserva)
    {
        if (auth()->user()->role !== 'abogado') {
            abort(403);
        }

        $reserva->load('turno', 'cliente');

        if (!$reserva->turno) {
            return back()->with('error', 'No se encontró el turno asociado a esta reserva.');
        }

        if ((int) $reserva->turno->abogado_id !== (int) auth()->id()) {
            abort(403);
        }

        if (!$reserva->cliente || (int) $reserva->cliente->abogado_id !== (int) auth()->id()) {
            abort(403);
        }

        $reserva->delete();

        return back()->with('success', 'Reserva cancelada manualmente.');
    }
}
