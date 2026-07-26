<?php

namespace App\Livewire\Rutinas;

use App\Models\Rutina;
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

    public function cambiarEstado(int $rutinaId): void
    {
        $rutina = Rutina::query()
            ->where('abogado_id', auth()->id())
            ->findOrFail($rutinaId);

        $rutina->update([
            'activa' => ! $rutina->activa,
        ]);

        session()->flash(
            'success',
            $rutina->activa
                ? 'La rutina fue activada correctamente.'
                : 'La rutina fue desactivada correctamente.'
        );
    }

    public function eliminar(int $rutinaId): void
    {
        $rutina = Rutina::query()
            ->where('abogado_id', auth()->id())
            ->findOrFail($rutinaId);

        if ($rutina->asignaciones()->exists()) {
            session()->flash(
                'error',
                'No se puede eliminar una rutina que está asignada a socios.'
            );

            return;
        }

        $rutina->delete();

        session()->flash(
            'success',
            'La rutina fue eliminada correctamente.'
        );

        $this->resetPage();
    }

    public function render()
    {
        $rutinas = Rutina::query()
            ->where('abogado_id', auth()->id())
            ->withCount([
                'dias',
                'asignaciones',
            ])
            ->when(
                $this->buscar !== '',
                function (Builder $query): void {
                    $query->where(function (Builder $subquery): void {
                        $subquery
                            ->where('nombre', 'ilike', '%' . $this->buscar . '%')
                            ->orWhere('objetivo', 'ilike', '%' . $this->buscar . '%')
                            ->orWhere('descripcion', 'ilike', '%' . $this->buscar . '%');
                    });
                }
            )
            ->when(
                $this->estado === 'activas',
                fn (Builder $query) => $query->where('activa', true)
            )
            ->when(
                $this->estado === 'inactivas',
                fn (Builder $query) => $query->where('activa', false)
            )
            ->latest()
            ->paginate(12);

        return view('livewire.rutinas.index', [
            'rutinas' => $rutinas,
        ]);
    }
}
