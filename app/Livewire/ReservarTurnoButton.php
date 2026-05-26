<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Turno;
use App\Models\Cliente;
use App\Models\ReservaTurno;

class ReservarTurnoButton extends Component
{
    public Turno $turno;

    public bool $loading = false;

    public function reservar()
    {
        $this->loading = true;

        $cliente = Cliente::where('user_id', auth()->id())->first();

        if (!$cliente) {

            session()->flash('error', 'No existe cliente asociado.');

            return;
        }

        $existe = ReservaTurno::where('cliente_id', $cliente->id)
            ->where('turno_id', $this->turno->id)
            ->exists();

        if ($existe) {

            session()->flash('error', 'Ya tienes reservado este turno.');

            return;
        }

        $reservados = $this->turno->reservas()->count();

        if ($reservados >= $this->turno->cupo_maximo) {

            session()->flash('error', 'El turno está completo.');

            return;
        }

        ReservaTurno::create([
            'cliente_id' => $cliente->id,
            'turno_id' => $this->turno->id,
            'estado' => 'reservado',
        ]);

        session()->flash('success', 'Turno reservado correctamente.');

        $this->dispatch('turnoReservado');
    }

    public function render()
    {
        return view('livewire.reservar-turno-button');
    }
}
