<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PagoService
{
    /**
     * Registra un pago y renueva la cuota del socio por 30 días.
     */
    public function registrarPago(
        Cliente $cliente,
        float|int|string $monto,
        string $metodoPago,
        ?string $observacion,
        string $fechaBase,
        ?string $fechaPago = null,
        string $origen = 'manual',
        string $estado = 'aprobado',
        ?string $mercadopagoPaymentId = null
    ): Pago {
        $fechaBaseCalculada = Carbon::parse($fechaBase)->startOfDay();

        $nuevoVencimiento = $fechaBaseCalculada
            ->copy()
            ->addDays(30);

        $fechaPagoCalculada = $fechaPago
            ? Carbon::parse($fechaPago)->toDateString()
            : now()->toDateString();

        return DB::transaction(function () use (
            $cliente,
            $monto,
            $metodoPago,
            $observacion,
            $fechaBaseCalculada,
            $nuevoVencimiento,
            $fechaPagoCalculada,
            $origen,
            $estado,
            $mercadopagoPaymentId
        ) {

            /*
            |--------------------------------------------------------------------------
            | BLOQUEO DE NUMERACIÓN POR GIMNASIO
            |--------------------------------------------------------------------------
            |
            | Bloqueamos temporalmente al administrador del gimnasio durante
            | la generación del número para evitar que dos pagos simultáneos
            | reciban el mismo comprobante.
            |
            */

            DB::table('users')
                ->where('id', $cliente->abogado_id)
                ->lockForUpdate()
                ->first();

            /*
            |--------------------------------------------------------------------------
            | PRÓXIMO NÚMERO DE COMPROBANTE
            |--------------------------------------------------------------------------
            |
            | Cada gimnasio mantiene su propia secuencia:
            |
            | Gimnasio A: 1, 2, 3...
            | Gimnasio B: 1, 2, 3...
            |
            | Los ceros a la izquierda se agregarán solamente al mostrarlo:
            | 1 => 000001
            |
            */

            $ultimoNumero = Pago::whereHas('cliente', function ($query) use ($cliente) {
                    $query->where('abogado_id', $cliente->abogado_id);
                })
                ->whereNotNull('numero_comprobante')
                ->max('numero_comprobante');

            $numeroComprobante = ((int) $ultimoNumero) + 1;

            /*
            |--------------------------------------------------------------------------
            | RENOVAR CUOTA
            |--------------------------------------------------------------------------
            */

            $cliente->update([
                'fecha_vencimiento_cuota' => $nuevoVencimiento->toDateString(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | REGISTRAR PAGO
            |--------------------------------------------------------------------------
            */

            return Pago::create([
                'cliente_id' => $cliente->id,
                'numero_comprobante' => $numeroComprobante,
                'monto' => $monto,
                'metodo_pago' => $metodoPago,
                'origen' => $origen,
                'estado' => $estado,
                'mercadopago_payment_id' => $mercadopagoPaymentId,
                'observacion' => $observacion
                    ?: 'Renovación de cuota mensual',
                'fecha_pago' => $fechaPagoCalculada,
                'fecha_base' => $fechaBaseCalculada->toDateString(),
                'vencimiento_cuota' => $nuevoVencimiento->toDateString(),
            ]);
        });
    }
}
