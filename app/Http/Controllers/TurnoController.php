<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Cliente;
use App\Models\ReservaTurno;
use Carbon\Carbon;

class TurnoController extends Controller
{
    public function index()
    {
        $this->generarTurnosProximosDias();

        $fechaSeleccionada = request('fecha', now()->toDateString());
        $fecha = Carbon::parse($fechaSeleccionada);
        $cerradoFinDeSemana = $fecha->isSaturday() || $fecha->isSunday();

        $turnos = Turno::with('reservas.cliente')
            ->where('activo', true)
            ->whereDate('fecha', $fechaSeleccionada)
            ->orderBy('hora_inicio')
            ->orderBy('actividad')
            ->get();

        if (auth()->user()->role === 'cliente') {
            return view('turnos.index', compact(
                'turnos',
                'fechaSeleccionada',
                'cerradoFinDeSemana'
            ));
        }

        return view('turnos.admin', compact(
            'turnos',
            'fechaSeleccionada',
            'cerradoFinDeSemana'
        ));
    }

    private function generarTurnosProximosDias(): void
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
                    Turno::firstOrCreate(
                        [
                            'actividad' => $actividad,
                            'fecha' => $fecha->toDateString(),
                            'hora_inicio' => $horaInicio,
                        ],
                        [
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
            abort(403);
        }

        $reserva->delete();

        return back()->with('success', 'Reserva cancelada correctamente.');
    }
}
