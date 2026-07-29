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
            $cliente->update([
                'fecha_vencimiento_cuota' => $nuevoVencimiento->toDateString(),
            ]);

            return Pago::create([
                'cliente_id' => $cliente->id,
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
