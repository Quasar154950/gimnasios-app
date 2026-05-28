<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Turno;
use App\Models\Cliente;
use App\Models\ReservaTurno;

class TurnoCard extends Component
{
    public Turno $turno;

    public ?string $mensajeOk = null;
    public ?string $mensajeError = null;

    public function reservar()
    {
        $this->mensajeOk = null;
        $this->mensajeError = null;

        $cliente = Cliente::where('user_id', auth()->id())->first();

        if (!$cliente) {
            $this->mensajeError = 'No existe socio asociado.';
            return;
        }

        $reservasDelDia = ReservaTurno::with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) {
                $query->whereDate('fecha', $this->turno->fecha);
            })
            ->get();

        foreach ($reservasDelDia as $reserva) {
            if (!$reserva->turno) {
                continue;
            }

            if ($reserva->turno_id === $this->turno->id) {
                $this->mensajeError = 'Ya reservaste este turno.';
                return;
            }

            if ($reserva->turno->actividad === $this->turno->actividad) {
                $this->mensajeError = 'Ya tenés una reserva de ' . $this->turno->actividad . ' para este día.';
                return;
            }

            if (
                $reserva->turno->hora_inicio < $this->turno->hora_fin &&
                $reserva->turno->hora_fin > $this->turno->hora_inicio
            ) {
                $this->mensajeError = 'Ya tenés otra reserva en un horario que se superpone.';
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

        $this->turno->refresh()->load('reservas.cliente');

        $this->mensajeOk = null;
    }

    public function cancelar($reservaId)
    {
        $this->mensajeOk = null;
        $this->mensajeError = null;

        $cliente = Cliente::where('user_id', auth()->id())->first();

        if (!$cliente) {
            $this->mensajeError = 'No existe socio asociado.';
            return;
        }

        $reserva = ReservaTurno::where('id', $reservaId)
            ->where('cliente_id', $cliente->id)
            ->where('turno_id', $this->turno->id)
            ->first();

        if (!$reserva) {
            $this->mensajeError = 'No se encontró la reserva.';
            return;
        }

        $reserva->delete();

        $this->turno->refresh()->load('reservas.cliente');

        $this->mensajeOk = null;
        $this->mensajeError = null;
    }

    public function render()
    {
        $this->turno->load('reservas.cliente');

        $cliente = Cliente::where('user_id', auth()->id())->first();

        $miReserva = null;

        if ($cliente) {
            $miReserva = $this->turno->reservas
                ->where('cliente_id', $cliente->id)
                ->first();
        }

        $reservados = $this->turno->reservas->count();
        $disponibles = max($this->turno->cupo_maximo - $reservados, 0);
        $completo = $disponibles <= 0;

        return view('livewire.turno-card', compact(
            'reservados',
            'disponibles',
            'completo',
            'miReserva'
        ));
    }
}
