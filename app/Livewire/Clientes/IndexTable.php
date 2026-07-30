<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\User;
use App\Services\PagoService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class IndexTable extends Component
{
    use WithPagination;

    public $busqueda = '';

    /*
    |--------------------------------------------------------------------------
    | RENOVACIÓN DE CUOTA
    |--------------------------------------------------------------------------
    */

    public $clientePagoId = null;
    public $montoPago = '';
    public $metodoPago = 'Efectivo';
    public $observacionPago = '';
    public $fechaBasePago = '';

    /*
    |--------------------------------------------------------------------------
    | EDICIÓN MANUAL DEL VENCIMIENTO
    |--------------------------------------------------------------------------
    */

    public $clienteVencimientoId = null;
    public $fechaVencimientoManual = '';

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | RENOVACIÓN DE CUOTA
    |--------------------------------------------------------------------------
    */

    public function abrirRenovacion($id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($id);

        if (! $cliente) {
            session()->flash('error', 'No se encontró el socio seleccionado.');

            return;
        }

        $hoy = now()->startOfDay();

        if (
            $cliente->fecha_vencimiento_cuota
            && Carbon::parse($cliente->fecha_vencimiento_cuota)
                ->startOfDay()
                ->greaterThanOrEqualTo($hoy)
        ) {
            $fechaBase = Carbon::parse($cliente->fecha_vencimiento_cuota);
        } else {
            $fechaBase = $hoy;
        }

        $this->clientePagoId = $cliente->id;

        /*
        | Carga automáticamente el monto oficial de la cuota.
        | Este mismo importe se utiliza para generar el pago online.
        */
        $this->montoPago = $cliente->monto_cuota ?? '';

        $this->metodoPago = 'Efectivo';
        $this->observacionPago = '';
        $this->fechaBasePago = $fechaBase->toDateString();

        $this->cancelarEdicionVencimiento();

        $this->resetValidation();
    }

    public function cancelarRenovacion()
    {
        $this->clientePagoId = null;
        $this->montoPago = '';
        $this->metodoPago = 'Efectivo';
        $this->observacionPago = '';
        $this->fechaBasePago = '';

        $this->resetValidation();
    }

    /*
    | Guarda únicamente el monto oficial de la cuota.
    | No registra pagos ni modifica el vencimiento.
    */
    public function guardarMontoCuota()
    {
        $this->validate([
            'clientePagoId' => 'required|integer|exists:clientes,id',
            'montoPago' => 'required|numeric|min:0.01',
        ], [
            'montoPago.required' => 'Ingresá el monto de la cuota.',
            'montoPago.numeric' => 'El monto debe ser un número válido.',
            'montoPago.min' => 'El monto debe ser mayor que cero.',
        ]);

        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($this->clientePagoId);

        if (! $cliente) {
            session()->flash('error', 'No se encontró el socio seleccionado.');

            return;
        }

        $cliente->update([
            'monto_cuota' => $this->montoPago,
        ]);

        /*
        | Mantenemos abierto el formulario para que el administrador pueda,
        | si corresponde, registrar después un pago manual.
        */
        $this->montoPago = $cliente->fresh()->monto_cuota;

        $this->resetValidation();

        session()->flash(
            'success',
            'El monto de la cuota fue actualizado correctamente.'
        );
    }

    public function renovarCuota(PagoService $pagoService)
    {
        $this->validate([
            'clientePagoId' => 'required|integer|exists:clientes,id',
            'montoPago' => 'required|numeric|min:0',
            'metodoPago' => 'required|string|max:255',
            'observacionPago' => 'nullable|string|max:1000',
            'fechaBasePago' => 'required|date',
        ]);

        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($this->clientePagoId);

        if (! $cliente) {
            session()->flash('error', 'No se encontró el socio seleccionado.');

            return;
        }

        $pagoService->registrarPago(
            cliente: $cliente,
            monto: $this->montoPago,
            metodoPago: $this->metodoPago,
            observacion: $this->observacionPago,
            fechaBase: $this->fechaBasePago,
        );

        $this->cancelarRenovacion();

        session()->flash(
            'success',
            'Cuota renovada por 30 días y pago registrado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDICIÓN MANUAL DEL VENCIMIENTO
    |--------------------------------------------------------------------------
    */

    public function abrirEdicionVencimiento($id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($id);

        if (! $cliente) {
            session()->flash('error', 'No se encontró el socio seleccionado.');

            return;
        }

        $this->clienteVencimientoId = $cliente->id;

        $this->fechaVencimientoManual = $cliente->fecha_vencimiento_cuota
            ? Carbon::parse($cliente->fecha_vencimiento_cuota)->toDateString()
            : now()->toDateString();

        $this->cancelarRenovacion();

        $this->resetValidation();
    }

    public function cancelarEdicionVencimiento()
    {
        $this->clienteVencimientoId = null;
        $this->fechaVencimientoManual = '';

        $this->resetValidation();
    }

    public function guardarVencimientoManual()
    {
        $this->validate([
            'clienteVencimientoId' => 'required|integer|exists:clientes,id',
            'fechaVencimientoManual' => 'required|date',
        ]);

        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($this->clienteVencimientoId);

        if (! $cliente) {
            session()->flash('error', 'No se encontró el socio seleccionado.');

            return;
        }

        $cliente->update([
            'fecha_vencimiento_cuota' => Carbon::parse(
                $this->fechaVencimientoManual
            )->toDateString(),
        ]);

        $this->cancelarEdicionVencimiento();

        session()->flash(
            'success',
            'La fecha de vencimiento fue actualizada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRACIÓN DEL SOCIO
    |--------------------------------------------------------------------------
    */

    public function archivar($id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($id);

        if ($cliente) {
            $cliente->update([
                'archivado' => true,
            ]);

            session()->flash(
                'success',
                'Socio dado de baja correctamente.'
            );
        }
    }

    public function delete($id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->find($id);

        if ($cliente) {
            $userId = $cliente->user_id;

            $cliente->delete();

            if ($userId) {
                User::where('id', $userId)->delete();
            }

            session()->flash(
                'success',
                'Socio y acceso eliminados definitivamente.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $clientes = Cliente::query()
            ->where('abogado_id', auth()->id())
            ->where('archivado', false)
            ->where(function ($query) {
                $query
                    ->where('nombre', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('email', 'like', '%' . $this->busqueda . '%')
                    ->orWhere('telefono', 'like', '%' . $this->busqueda . '%');
            })
            ->withCount([
                'mensajes as mensajes_no_leidos_count' => function ($query) {
                    $query
                        ->where('remitente', 'cliente')
                        ->where('leido', false);
                },
            ])
            ->orderBy('nombre', 'asc')
            ->paginate(10);

        return view('livewire.clientes.index-table', [
            'clientes' => $clientes,
        ]);
    }
}