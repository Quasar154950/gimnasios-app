<?php

namespace App\Livewire\Rutinas;

use App\Models\Cliente;
use App\Models\Rutina;
use App\Models\RutinaAsignacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Asignar extends Component
{
    public Rutina $rutina;

    public string $buscar = '';

    public array $clientesSeleccionados = [];

    public ?string $fechaInicio = null;

    public ?string $fechaFin = null;

    public ?string $fechaRevision = null;

    public string $observaciones = '';

    public function mount(Rutina $rutina): void
    {
        $this->autorizarRutina($rutina);

        $this->rutina = $rutina;

        $this->fechaInicio = now()->toDateString();
    }

    public function seleccionarTodos(): void
    {
        $this->clientesSeleccionados = $this->clientes()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    public function quitarSeleccion(): void
    {
        $this->clientesSeleccionados = [];
    }

    public function guardar(): void
    {
        $this->autorizarRutina($this->rutina);

        $datos = $this->validate([
            'clientesSeleccionados' => [
                'required',
                'array',
                'min:1',
            ],
            'clientesSeleccionados.*' => [
                'required',
                'integer',
                'exists:clientes,id',
            ],
            'fechaInicio' => [
                'required',
                'date',
            ],
            'fechaFin' => [
                'nullable',
                'date',
                'after_or_equal:fechaInicio',
            ],
            'fechaRevision' => [
                'nullable',
                'date',
                'after_or_equal:fechaInicio',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'clientesSeleccionados.required' => 'Seleccioná al menos un socio.',
            'clientesSeleccionados.min' => 'Seleccioná al menos un socio.',
            'fechaInicio.required' => 'Indicá la fecha de inicio.',
            'fechaFin.after_or_equal' => 'La fecha de finalización no puede ser anterior al inicio.',
            'fechaRevision.after_or_equal' => 'La fecha de revisión no puede ser anterior al inicio.',
            'observaciones.max' => 'Las observaciones no pueden superar los 2000 caracteres.',
        ]);

        $clientesValidos = Cliente::query()
            ->where('abogado_id', auth()->id())
            ->whereIn('id', $datos['clientesSeleccionados'])
            ->pluck('id');

        if ($clientesValidos->count() !== count($datos['clientesSeleccionados'])) {
            abort(403);
        }

        DB::transaction(function () use ($clientesValidos, $datos): void {
            foreach ($clientesValidos as $clienteId) {
                RutinaAsignacion::updateOrCreate(
                    [
                        'rutina_id' => $this->rutina->id,
                        'cliente_id' => $clienteId,
                    ],
                    [
                        'fecha_inicio' => $datos['fechaInicio'],
                        'fecha_fin' => $datos['fechaFin'] ?: null,
                        'fecha_revision' => $datos['fechaRevision'] ?: null,
                        'activa' => true,
                        'observaciones' => filled($datos['observaciones'])
                            ? trim($datos['observaciones'])
                            : null,
                    ]
                );
            }
        });

        session()->flash(
            'success',
            count($clientesValidos) === 1
                ? 'La rutina fue asignada correctamente.'
                : 'La rutina fue asignada correctamente a los socios seleccionados.'
        );

        $this->redirectRoute(
            'rutinas.show',
            ['rutina' => $this->rutina],
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.rutinas.asignar', [
            'clientes' => $this->clientes(),
            'asignacionesActivas' => $this->rutina
                ->asignaciones()
                ->with('cliente')
                ->activas()
                ->latest('fecha_inicio')
                ->get(),
        ]);
    }

    private function clientes(): Collection
    {
        return Cliente::query()
            ->where('abogado_id', auth()->id())
            ->where('archivado', false)
            ->when(
                filled($this->buscar),
                function ($query): void {
                    $termino = '%' . trim($this->buscar) . '%';

                    $query->where(function ($subquery) use ($termino): void {
                        $subquery
                            ->where('nombre', 'like', $termino)
                            ->orWhere('dni', 'like', $termino)
                            ->orWhere('email', 'like', $termino);
                    });
                }
            )
            ->orderBy('nombre')
            ->get();
    }

    private function autorizarRutina(Rutina $rutina): void
    {
        if ((int) $rutina->abogado_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
