<?php

namespace App\Livewire\Ejercicios;

use App\Models\Ejercicio;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    public string $buscar = '';

    public string $grupoMuscular = '';

    public string $estado = 'activos';

    public string $orden = 'nombre';

    /*
    |--------------------------------------------------------------------------
    | QUERY STRING
    |--------------------------------------------------------------------------
    |
    | Permite conservar los filtros en la URL cuando el administrador
    | navega por el sistema o actualiza la página.
    |
    */

    protected $queryString = [
        'buscar' => [
            'except' => '',
        ],

        'grupoMuscular' => [
            'except' => '',
        ],

        'estado' => [
            'except' => 'activos',
        ],

        'orden' => [
            'except' => 'nombre',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | REINICIAR PAGINACIÓN
    |--------------------------------------------------------------------------
    */

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    public function updatedGrupoMuscular(): void
    {
        $this->resetPage();
    }

    public function updatedEstado(): void
    {
        $this->resetPage();
    }

    public function updatedOrden(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | LIMPIAR FILTROS
    |--------------------------------------------------------------------------
    */

    public function limpiarFiltros(): void
    {
        $this->reset([
            'buscar',
            'grupoMuscular',
        ]);

        $this->estado = 'activos';
        $this->orden = 'nombre';

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVAR O DESACTIVAR EJERCICIO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(int $ejercicioId): void
    {
        $ejercicio = Ejercicio::query()
            ->where('abogado_id', auth()->id())
            ->findOrFail($ejercicioId);

        $ejercicio->update([
            'activo' => ! $ejercicio->activo,
        ]);

        session()->flash(
            'mensaje',
            $ejercicio->activo
                ? 'El ejercicio fue activado correctamente.'
                : 'El ejercicio fue desactivado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR EJERCICIO
    |--------------------------------------------------------------------------
    */

    public function eliminar(int $ejercicioId): void
    {
        $ejercicio = Ejercicio::query()
            ->where('abogado_id', auth()->id())
            ->findOrFail($ejercicioId);

        /*
         * No permitimos eliminar ejercicios que ya estén utilizados
         * dentro de alguna rutina.
         */
        if ($ejercicio->rutinaEjercicios()->exists()) {
            session()->flash(
                'error',
                'No se puede eliminar el ejercicio porque está siendo utilizado en una rutina. Podés desactivarlo.'
            );

            return;
        }

        /*
         * Media Library eliminará también los archivos asociados
         * al borrar el modelo.
         */
        $ejercicio->delete();

        session()->flash(
            'mensaje',
            'El ejercicio fue eliminado correctamente.'
        );

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        $ejercicios = Ejercicio::query()
            /*
             * Cada gimnasio solamente puede consultar sus propios ejercicios.
             */
            ->where('abogado_id', auth()->id())

            /*
             * Buscador por nombre, descripción o grupo muscular.
             */
            ->when(
                filled($this->buscar),
                function ($query) {
                    $texto = trim($this->buscar);

                    $query->where(function ($subquery) use ($texto) {
                        $subquery
                            ->where('nombre', 'ilike', "%{$texto}%")
                            ->orWhere('descripcion', 'ilike', "%{$texto}%")
                            ->orWhere('grupo_muscular', 'ilike', "%{$texto}%");
                    });
                }
            )

            /*
             * Filtro por grupo muscular.
             */
            ->when(
                filled($this->grupoMuscular),
                fn ($query) => $query->where(
                    'grupo_muscular',
                    $this->grupoMuscular
                )
            )

            /*
             * Filtro por estado.
             */
            ->when(
                $this->estado === 'activos',
                fn ($query) => $query->where('activo', true)
            )
            ->when(
                $this->estado === 'inactivos',
                fn ($query) => $query->where('activo', false)
            )

            /*
             * Orden del listado.
             */
            ->when(
                $this->orden === 'nombre',
                fn ($query) => $query->orderBy('nombre')
            )
            ->when(
                $this->orden === 'recientes',
                fn ($query) => $query->latest()
            )
            ->when(
                $this->orden === 'antiguos',
                fn ($query) => $query->oldest()
            )

            ->paginate(12);

        return view('livewire.ejercicios.index', [
            'ejercicios' => $ejercicios,
            'gruposMusculares' => Ejercicio::gruposMusculares(),
        ]);
    }
}
