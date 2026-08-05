<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\NotificacionPush;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $buscar = '';

    public string $estado = 'todos';

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatingEstado(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $notificacionId): void
    {
        $notificacion = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificacionId);

        $notificacion->delete();

        session()->flash(
            'success',
            'La notificación fue eliminada correctamente.'
        );

        $this->resetPage();
    }

    public function render()
    {
        $notificaciones = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->when(
                $this->buscar !== '',
                function (Builder $query): void {
                    $query->where(function (Builder $subquery): void {
                        $subquery
                            ->where('titulo', 'ilike', '%' . $this->buscar . '%')
                            ->orWhere('mensaje', 'ilike', '%' . $this->buscar . '%');
                    });
                }
            )
            ->when(
                $this->estado !== 'todos',
                fn (Builder $query) => $query->where('estado', $this->estado)
            )
            ->latest()
            ->paginate(12);

        return view('livewire.notificaciones-push.index', [
            'notificaciones' => $notificaciones,
        ]);
    }
}
