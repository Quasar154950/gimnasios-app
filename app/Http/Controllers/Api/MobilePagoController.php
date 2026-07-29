<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Throwable;

class MobilePagoController extends Controller
{
    /**
     * Crear una preferencia de pago para la cuota del socio.
     */
    public function crearPago(Request $request): JsonResponse
    {
        $user = $request->user();

        $cliente = Cliente::where('user_id', $user->id)->first();

        if (! $cliente) {
            return response()->json([
                'message' => 'No se encontró el socio vinculado a tu usuario.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTE CONFIGURADO PARA EL SOCIO
        |--------------------------------------------------------------------------
        */

        $ultimoPago = Pago::where('cliente_id', $cliente->id)
            ->latest('fecha_pago')
            ->latest('id')
            ->first();

        if (! $ultimoPago || $ultimoPago->monto <= 0) {
            return response()->json([
                'message' => 'El administrador todavía no configuró el importe de tu cuota.',
            ], 422);
        }

        $montoCuota = (float) $ultimoPago->monto;

        /*
        |--------------------------------------------------------------------------
        | MERCADO PAGO DEL GIMNASIO
        |--------------------------------------------------------------------------
        */

        $gimnasio = User::find($cliente->abogado_id);

        if (
            ! $gimnasio ||
            ! $gimnasio->mercadopago_enabled ||
            ! $gimnasio->mercadopago_access_token
        ) {
            return response()->json([
                'message' => 'El gimnasio todavía no tiene habilitados los pagos online.',
            ], 422);
        }

        try {
            MercadoPagoConfig::setAccessToken(
                $gimnasio->mercadopago_access_token
            );

            $client = new PreferenceClient();

            $preference = $client->create([
                'items' => [
                    [
                        'title' => 'Cuota mensual gimnasio',
                        'quantity' => 1,
                        'unit_price' => $montoCuota,
                        'currency_id' => 'ARS',
                    ],
                ],

                'payer' => [
                    'name' => $cliente->nombre,
                    'email' => $user->email,
                ],

                'external_reference' => 'cuota_cliente_' . $cliente->id,

                'back_urls' => [
                    'success' => route('cliente.cuota'),
                    'failure' => route('cliente.cuota'),
                    'pending' => route('cliente.cuota'),
                ],

                'auto_return' => 'approved',
            ]);

            return response()->json([
                'message' => 'Pago creado correctamente.',
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'monto' => $montoCuota,
            ]);
        } catch (Throwable $error) {
            report($error);

            return response()->json([
                'message' => 'No se pudo iniciar el pago con Mercado Pago.',
            ], 500);
        }
    }
}
