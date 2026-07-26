<?php

namespace App\Livewire\Rutinas;

use App\Models\Rutina;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public string $nombre = '';

    public string $objetivo = '';

    public ?int $duracion_semanas = null;

    public string $descripcion = '';

    public bool $activa = true;

    protected function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:120',
                Rule::unique('rutinas', 'nombre')
                    ->where(fn ($query) => $query
                        ->where('abogado_id', Auth::id())
                    ),
            ],
            'objetivo' => [
                'nullable',
                'string',
                'max:120',
            ],
            'duracion_semanas' => [
                'nullable',
                'integer',
                'min:1',
                'max:52',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'activa' => [
                'boolean',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'Ingresá un nombre para la rutina.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 120 caracteres.',
            'nombre.unique' => 'Ya existe una rutina con ese nombre.',

            'objetivo.max' => 'El objetivo no puede superar los 120 caracteres.',

            'duracion_semanas.integer' => 'La duración debe ser un número entero.',
            'duracion_semanas.min' => 'La duración mínima es de 1 semana.',
            'duracion_semanas.max' => 'La duración máxima es de 52 semanas.',

            'descripcion.max' => 'La descripción no puede superar los 2000 caracteres.',
        ];
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        $rutina = Rutina::create([
            'abogado_id' => Auth::id(),
            'nombre' => trim($datos['nombre']),
            'objetivo' => filled($datos['objetivo'] ?? null)
                ? trim($datos['objetivo'])
                : null,
            'duracion_semanas' => $datos['duracion_semanas'] ?? null,
            'descripcion' => filled($datos['descripcion'] ?? null)
                ? trim($datos['descripcion'])
                : null,
            'activa' => $datos['activa'],
        ]);

        $rutina->dias()->createMany([
    [
        'nombre' => 'Lunes',
        'descripcion' => null,
        'orden' => 1,
        'activo' => true,
    ],
    [
        'nombre' => 'Martes',
        'descripcion' => null,
        'orden' => 2,
        'activo' => true,
    ],
    [
        'nombre' => 'Miércoles',
        'descripcion' => null,
        'orden' => 3,
        'activo' => true,
    ],
    [
        'nombre' => 'Jueves',
        'descripcion' => null,
        'orden' => 4,
        'activo' => true,
    ],
    [
        'nombre' => 'Viernes',
        'descripcion' => null,
        'orden' => 5,
        'activo' => true,
    ],
    [
        'nombre' => 'Sábado',
        'descripcion' => null,
        'orden' => 6,
        'activo' => true,
    ],
    [
        'nombre' => 'Domingo',
        'descripcion' => null,
        'orden' => 7,
        'activo' => true,
    ],
]);

        session()->flash(
            'success',
            'La rutina "' . $rutina->nombre . '" fue creada correctamente.'
        );

        $this->redirectRoute(
            'rutinas.index',
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.rutinas.create');
    }
}
