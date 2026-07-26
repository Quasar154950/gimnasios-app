<?php

namespace App\Livewire\Rutinas;

use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\RutinaDia;
use App\Models\RutinaEjercicio;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dia extends Component
{
    public Rutina $rutina;

    public RutinaDia $dia;

    public string $buscar = '';

    public string $grupoMuscular = '';

    public bool $mostrarBiblioteca = false;

    /*
    |--------------------------------------------------------------------------
    | EDICIÓN DE EJERCICIO
    |--------------------------------------------------------------------------
    */

    public bool $mostrarEditor = false;

    public ?int $rutinaEjercicioEditandoId = null;

    public int $series = 3;

    public int $repeticiones = 10;

    public ?float $peso = null;

    public int $descansoSegundos = 60;

    public string $observaciones = '';

    public function mount(Rutina $rutina, RutinaDia $dia): void
    {
        abort_unless(
            $rutina->abogado_id === Auth::id(),
            403
        );

        abort_unless(
            $dia->rutina_id === $rutina->id,
            404
        );

        $this->rutina = $rutina;
        $this->dia = $dia;
    }

    /*
    |--------------------------------------------------------------------------
    | BIBLIOTECA DE EJERCICIOS
    |--------------------------------------------------------------------------
    */

    public function abrirBiblioteca(): void
    {
        $this->mostrarBiblioteca = true;
    }

    public function cerrarBiblioteca(): void
    {
        $this->mostrarBiblioteca = false;

        $this->reset([
            'buscar',
            'grupoMuscular',
        ]);
    }

    public function agregarEjercicio(int $ejercicioId): void
    {
        $ejercicio = Ejercicio::query()
            ->where('abogado_id', Auth::id())
            ->where('activo', true)
            ->findOrFail($ejercicioId);

        $existente = RutinaEjercicio::query()
            ->where('rutina_dia_id', $this->dia->id)
            ->where('ejercicio_id', $ejercicio->id)
            ->first();

        if ($existente) {
            session()->flash(
                'error',
                'Ese ejercicio ya fue agregado a este día.'
            );

            return;
        }

        $ultimoOrden = RutinaEjercicio::query()
            ->where('rutina_dia_id', $this->dia->id)
            ->max('orden') ?? 0;

        RutinaEjercicio::create([
            'rutina_dia_id' => $this->dia->id,
            'ejercicio_id' => $ejercicio->id,
            'ejercicio' => $ejercicio->nombre,
            'series' => 3,
            'repeticiones' => 10,
            'peso' => null,
            'descanso_segundos' => 60,
            'observaciones' => null,
            'orden' => $ultimoOrden + 1,
            'activo' => true,
        ]);

        session()->flash(
            'mensaje',
            'Ejercicio agregado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR EJERCICIO DEL DÍA
    |--------------------------------------------------------------------------
    */

    public function editarEjercicio(int $rutinaEjercicioId): void
    {
        $rutinaEjercicio = RutinaEjercicio::query()
            ->where('rutina_dia_id', $this->dia->id)
            ->findOrFail($rutinaEjercicioId);

        $this->rutinaEjercicioEditandoId = $rutinaEjercicio->id;

        $this->series = (int) ($rutinaEjercicio->series ?? 3);

        $this->repeticiones = (int) (
            $rutinaEjercicio->repeticiones ?? 10
        );

        $this->peso = $rutinaEjercicio->peso !== null
            ? (float) $rutinaEjercicio->peso
            : null;

        $this->descansoSegundos = (int) (
            $rutinaEjercicio->descanso_segundos ?? 60
        );

        $this->observaciones = (string) (
            $rutinaEjercicio->observaciones ?? ''
        );

        $this->resetValidation();

        $this->mostrarEditor = true;
    }

    public function guardarEjercicio(): void
    {
        $datos = $this->validate([
            'series' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],
            'repeticiones' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
            'peso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999',
            ],
            'descansoSegundos' => [
                'required',
                'integer',
                'min:0',
                'max:3600',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'series.required' => 'Ingresá la cantidad de series.',
            'series.integer' => 'Las series deben ser un número entero.',
            'series.min' => 'Debe haber al menos una serie.',
            'series.max' => 'La cantidad máxima es de 50 series.',

            'repeticiones.required' => 'Ingresá las repeticiones.',
            'repeticiones.integer' =>
                'Las repeticiones deben ser un número entero.',
            'repeticiones.min' =>
                'Debe haber al menos una repetición.',
            'repeticiones.max' =>
                'La cantidad máxima es de 500 repeticiones.',

            'peso.numeric' => 'El peso debe ser un número.',
            'peso.min' => 'El peso no puede ser negativo.',
            'peso.max' => 'El peso ingresado es demasiado alto.',

            'descansoSegundos.required' =>
                'Ingresá el tiempo de descanso.',
            'descansoSegundos.integer' =>
                'El descanso debe expresarse en segundos.',
            'descansoSegundos.min' =>
                'El descanso no puede ser negativo.',
            'descansoSegundos.max' =>
                'El descanso máximo es de una hora.',

            'observaciones.max' =>
                'Las observaciones no pueden superar los 1000 caracteres.',
        ]);

        abort_if(
            $this->rutinaEjercicioEditandoId === null,
            404
        );

        $rutinaEjercicio = RutinaEjercicio::query()
            ->where('rutina_dia_id', $this->dia->id)
            ->findOrFail($this->rutinaEjercicioEditandoId);

        $rutinaEjercicio->update([
            'series' => $datos['series'],
            'repeticiones' => $datos['repeticiones'],
            'peso' => $datos['peso'],
            'descanso_segundos' => $datos['descansoSegundos'],
            'observaciones' => filled($datos['observaciones'])
                ? trim($datos['observaciones'])
                : null,
        ]);

        $this->cerrarEditor();

        session()->flash(
            'mensaje',
            'Datos del ejercicio actualizados correctamente.'
        );
    }

    public function cerrarEditor(): void
    {
        $this->mostrarEditor = false;

        $this->reset([
            'rutinaEjercicioEditandoId',
            'series',
            'repeticiones',
            'peso',
            'descansoSegundos',
            'observaciones',
        ]);

        $this->series = 3;
        $this->repeticiones = 10;
        $this->descansoSegundos = 60;

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR EJERCICIO
    |--------------------------------------------------------------------------
    */

    public function eliminarEjercicio(int $rutinaEjercicioId): void
    {
        $rutinaEjercicio = RutinaEjercicio::query()
            ->where('rutina_dia_id', $this->dia->id)
            ->findOrFail($rutinaEjercicioId);

        $rutinaEjercicio->delete();

        $this->reordenarEjercicios();

        session()->flash(
            'mensaje',
            'Ejercicio eliminado del día.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REORDENAR EJERCICIOS
    |--------------------------------------------------------------------------
    */

    private function reordenarEjercicios(): void
    {
        $ejercicios = RutinaEjercicio::query()
            ->where('rutina_dia_id', $this->dia->id)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        foreach ($ejercicios as $indice => $ejercicio) {
            $ejercicio->update([
                'orden' => $indice + 1,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $ejerciciosDelDia = RutinaEjercicio::query()
            ->with('ejercicioBiblioteca')
            ->where('rutina_dia_id', $this->dia->id)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $ejerciciosAgregadosIds = $ejerciciosDelDia
            ->pluck('ejercicio_id')
            ->filter()
            ->values()
            ->all();

        $biblioteca = Ejercicio::query()
            ->where('abogado_id', Auth::id())
            ->where('activo', true)
            ->when(
                $this->buscar !== '',
                function ($query) {
                    $query->where(
                        'nombre',
                        'like',
                        '%' . $this->buscar . '%'
                    );
                }
            )
            ->when(
                $this->grupoMuscular !== '',
                function ($query) {
                    $query->where(
                        'grupo_muscular',
                        $this->grupoMuscular
                    );
                }
            )
            ->orderBy('grupo_muscular')
            ->orderBy('nombre')
            ->get();

        return view('livewire.rutinas.dia', [
            'ejerciciosDelDia' => $ejerciciosDelDia,
            'ejerciciosAgregadosIds' => $ejerciciosAgregadosIds,
            'biblioteca' => $biblioteca,
            'gruposMusculares' => Ejercicio::gruposMusculares(),
        ]);
    }
}
