<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Turno;
use App\Models\Cliente;
use App\Models\ReservaTurno;
use Carbon\Carbon;

class AdminTurnoCard extends Component
{
    public Turno $turno;

    public $clientes;

    public ?int $clienteId = null;

    public ?string $mensajeExito = null;

    public ?string $mensajeError = null;

    public function reservar()
    {
        $this->mensajeExito = null;
        $this->mensajeError = null;

        if (auth()->user()->role !== 'abogado') {
            abort(403);
        }

        $this->turno = Turno::findOrFail($this->turno->id);

        if ((int) $this->turno->abogado_id !== (int) auth()->id()) {
            abort(403);
        }

        if (!$this->clienteId) {
            $this->mensajeError = 'Seleccioná un socio válido.';
            return;
        }

        $cliente = Cliente::where('id', $this->clienteId)
            ->where('abogado_id', auth()->id())
            ->where('archivado', false)
            ->first();

        if (!$cliente) {
            $this->mensajeError = 'Seleccioná un socio válido.';
            return;
        }

        $inicioTurno = Carbon::parse(
            $this->turno->fecha . ' ' . $this->turno->hora_inicio
        );

        if ($inicioTurno->isPast()) {
            $this->mensajeError = 'Este turno ya comenzó o ya pasó.';
            return;
        }

        $reservasDelDia = ReservaTurno::with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) {
                $query->where('abogado_id', auth()->id())
                    ->whereDate('fecha', $this->turno->fecha);
            })
            ->get();

        foreach ($reservasDelDia as $reserva) {
            if (!$reserva->turno) {
                continue;
            }

            // No permite reservar exactamente el mismo turno dos veces.
            if ((int) $reserva->turno_id === (int) $this->turno->id) {
                $this->mensajeError = 'Este socio ya tiene reservado este turno.';
                return;
            }

            // El administrador puede asignar varios turnos de la misma
            // actividad, pero nunca en horarios superpuestos.
            if (
                $reserva->turno->hora_inicio < $this->turno->hora_fin &&
                $reserva->turno->hora_fin > $this->turno->hora_inicio
            ) {
                $this->mensajeError = 'Este socio ya tiene otra reserva en un horario que se superpone.';
                return;
            }
        }

        $this->turno->load('reservas');

        if ($this->turno->reservas->count() >= $this->turno->cupo_maximo) {
            $this->mensajeError = 'El turno está completo.';
            return;
        }

        ReservaTurno::create([
            'cliente_id' => $cliente->id,
            'turno_id' => $this->turno->id,
            'estado' => 'reservado',
        ]);

        $this->clienteId = null;

        $this->mensajeError = null;
        $this->mensajeExito = 'Turno reservado manualmente.';

        $this->turno->load('reservas.cliente');
    }

    public function cancelar(int $reservaId)
    {
        $this->mensajeExito = null;
        $this->mensajeError = null;

        if (auth()->user()->role !== 'abogado') {
            abort(403);
        }

        $reserva = ReservaTurno::with('turno', 'cliente')
            ->where('id', $reservaId)
            ->first();

        if (!$reserva) {
            $this->mensajeError = 'No se encontró la reserva.';
            return;
        }

        if (!$reserva->turno) {
            $this->mensajeError = 'No se encontró el turno asociado a esta reserva.';
            return;
        }

        if ((int) $reserva->turno_id !== (int) $this->turno->id) {
            abort(403);
        }

        if ((int) $reserva->turno->abogado_id !== (int) auth()->id()) {
            abort(403);
        }

        if (
            !$reserva->cliente ||
            (int) $reserva->cliente->abogado_id !== (int) auth()->id()
        ) {
            abort(403);
        }

        $reserva->delete();

        $this->mensajeError = null;
        $this->mensajeExito = 'Reserva cancelada manualmente.';

        $this->turno->load('reservas.cliente');
    }

    public function render()
    {
        $this->turno = Turno::with('reservas.cliente')
            ->findOrFail($this->turno->id);

        $reservados = $this->turno->reservas->count();

        $disponibles = max(
            $this->turno->cupo_maximo - $reservados,
            0
        );

        $completo = $disponibles <= 0;

        $inicioTurno = Carbon::parse(
            $this->turno->fecha . ' ' . $this->turno->hora_inicio
        );

        $finTurno = Carbon::parse(
            $this->turno->fecha . ' ' . $this->turno->hora_fin
        );

        $ahora = now();

        $turnoEnCurso = $ahora->between($inicioTurno, $finTurno);
        $turnoPasado = $finTurno->isPast();

        $bloqueado = $completo || $turnoEnCurso || $turnoPasado;

        return view('livewire.admin-turno-card', compact(
            'reservados',
            'disponibles',
            'completo',
            'turnoEnCurso',
            'turnoPasado',
            'bloqueado'
        ));
    }
}
