<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\Rutina as RutinaModelo;
use App\Models\RutinaAsignacion;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Rutina extends Component
{
    public Cliente $cliente;

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    public bool $mostrarFormularioAsignacion = false;

    public $rutinaId = '';

    public $fechaInicio = '';

    public $fechaFin = '';

    public $observaciones = '';

    /*
    |--------------------------------------------------------------------------
    | INICIO
    |--------------------------------------------------------------------------
    */

    public function mount(Cliente $cliente): void
    {
        $this->autorizarCliente($cliente);

        $this->cliente = $cliente;

        $this->fechaInicio = now()->toDateString();
    }

    /*
    |--------------------------------------------------------------------------
    | ABRIR Y CERRAR FORMULARIO
    |--------------------------------------------------------------------------
    */

    public function abrirFormularioAsignacion(): void
    {
        $this->rutinaId = '';
        $this->fechaInicio = now()->toDateString();
        $this->fechaFin = '';
        $this->observaciones = '';

        $this->mostrarFormularioAsignacion = true;

        $this->resetValidation();
    }

    public function cancelarAsignacion(): void
    {
        $this->mostrarFormularioAsignacion = false;

        $this->rutinaId = '';
        $this->fechaInicio = now()->toDateString();
        $this->fechaFin = '';
        $this->observaciones = '';

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | ASIGNAR RUTINA
    |--------------------------------------------------------------------------
    */

    public function asignarRutina(): void
    {
        $datosValidados = $this->validate([
            'rutinaId' => [
                'required',
                'integer',
                'exists:rutinas,id',
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
            'observaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'rutinaId.required' => 'Tenés que seleccionar una rutina.',
            'rutinaId.exists' => 'La rutina seleccionada no existe.',
            'fechaInicio.required' => 'La fecha de inicio es obligatoria.',
            'fechaInicio.date' => 'La fecha de inicio no es válida.',
            'fechaFin.date' => 'La fecha de finalización no es válida.',
            'fechaFin.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',
            'observaciones.max' => 'Las observaciones no pueden superar los 2000 caracteres.',
        ]);

        $rutina = RutinaModelo::query()
            ->whereKey($datosValidados['rutinaId'])
            ->where('abogado_id', auth()->id())
            ->first();

        if (! $rutina) {
            $this->addError(
                'rutinaId',
                'La rutina seleccionada no pertenece a este gimnasio.'
            );

            return;
        }

        $this->autorizarCliente($this->cliente);

        DB::transaction(function () use ($datosValidados): void {
            /*
            |--------------------------------------------------------------------------
            | FINALIZAR LA RUTINA ACTIVA ANTERIOR
            |--------------------------------------------------------------------------
            */

            RutinaAsignacion::query()
                ->where('cliente_id', $this->cliente->id)
                ->where('activa', true)
                ->update([
                    'activa' => false,
                    'fecha_fin' => now()->toDateString(),
                ]);

            /*
            |--------------------------------------------------------------------------
            | CREAR LA NUEVA ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            RutinaAsignacion::create([
                'cliente_id' => $this->cliente->id,
                'rutina_id' => $datosValidados['rutinaId'],
                'fecha_inicio' => $datosValidados['fechaInicio'],
                'fecha_fin' => $datosValidados['fechaFin'] ?: null,
                'observaciones' => $datosValidados['observaciones'] ?: null,
                'activa' => true,
            ]);
        });

        $this->cancelarAsignacion();

        session()->flash(
            'success',
            'La rutina fue asignada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINALIZAR RUTINA
    |--------------------------------------------------------------------------
    */

    public function finalizarRutina(int $asignacionId): void
    {
        $asignacion = RutinaAsignacion::query()
            ->whereKey($asignacionId)
            ->where('cliente_id', $this->cliente->id)
            ->firstOrFail();

        $this->autorizarCliente($asignacion->cliente);

        DB::transaction(function () use ($asignacion): void {
            $asignacion->update([
                'activa' => false,
                'fecha_fin' => $asignacion->fecha_fin
                    ?? now()->toDateString(),
            ]);
        });

        session()->flash(
            'success',
            'La rutina fue finalizada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $cliente = Cliente::query()
            ->whereKey($this->cliente->id)
            ->with([
                'rutinaAsignaciones' => function ($query) {
                    $query
                        ->with([
                            'rutina.dias.ejercicios',
                        ])
                        ->orderByDesc('activa')
                        ->orderByDesc('fecha_inicio')
                        ->orderByDesc('id');
                },
            ])
            ->firstOrFail();

        $this->autorizarCliente($cliente);

        $asignacionActiva = $cliente->rutinaAsignaciones
            ->firstWhere('activa', true);

        $historialAsignaciones = $cliente->rutinaAsignaciones
            ->where('activa', false)
            ->values();

        $rutinasDisponibles = RutinaModelo::query()
            ->where('abogado_id', auth()->id())
            ->orderBy('nombre')
            ->get();

        return view('livewire.clientes.rutina', [
            'cliente' => $cliente,
            'asignacionActiva' => $asignacionActiva,
            'historialAsignaciones' => $historialAsignaciones,
            'rutinasDisponibles' => $rutinasDisponibles,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTORIZACIÓN
    |--------------------------------------------------------------------------
    */

    private function autorizarCliente(Cliente $cliente): void
    {
        abort_unless(
            (int) $cliente->abogado_id === (int) auth()->id(),
            403
        );
    }
}
