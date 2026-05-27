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

        $existe = ReservaTurno::where('cliente_id', $cliente->id)
            ->where('turno_id', $this->turno->id)
            ->exists();

        if ($existe) {
            $this->mensajeError = 'Ya reservaste este turno.';
            return;
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

        $this->mensajeOk = 'Turno reservado correctamente.';
    }

    public function render()
    {
        $this->turno->load('reservas.cliente');

        $reservados = $this->turno->reservas->count();
        $disponibles = max($this->turno->cupo_maximo - $reservados, 0);
        $completo = $disponibles <= 0;

        return view('livewire.turno-card', compact(
            'reservados',
            'disponibles',
            'completo'
        ));
    }
}
