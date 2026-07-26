<?php

namespace App\Livewire\Rutinas;

use App\Models\Rutina;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Rutina $rutina;

    public function mount(Rutina $rutina): void
    {
        abort_unless(
            $rutina->abogado_id === Auth::id(),
            403
        );

        $this->rutina = $rutina->load([
            'dias' => function ($query) {
                $query
                    ->withCount('ejercicios')
                    ->orderBy('orden');
            },
        ]);
    }

    public function render()
    {
        return view('livewire.rutinas.show');
    }
}
