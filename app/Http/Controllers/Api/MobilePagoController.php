<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $montoCuota = (float) $cliente->monto_cuota;

        if ($montoCuota <= 0) {
            return response()->json([
                'message' => 'El administrador todavía no configuró el importe de tu cuota.',
            ], 422);
        }

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

        /*
        |--------------------------------------------------------------------------
        | DEEP LINK SEGÚN LA MARCA
        |--------------------------------------------------------------------------
        */

        $scheme = $gimnasio->slug_estudio === 'demo'
            ? 'demogym'
            : 'sportgym';

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

                'external_reference' => (string) $cliente->id,

                'notification_url' => route(
                    'webhooks.mercadopago.gimnasio'
                ),

                'back_urls' => [
                    'success' => "{$scheme}://pago/exito",
                    'failure' => "{$scheme}://pago/error",
                    'pending' => "{$scheme}://pago/pendiente",
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

    /**
     * Listar comprobantes del socio autenticado.
     */
    public function comprobantes(Request $request): JsonResponse
    {
        $user = $request->user();

        $cliente = Cliente::where('user_id', $user->id)->first();

        if (! $cliente) {
            return response()->json([
                'message' => 'No se encontró el socio vinculado a tu usuario.',
            ], 422);
        }

        $pagos = Pago::where('cliente_id', $cliente->id)
            ->where('estado', 'aprobado')
            ->whereNotNull('numero_comprobante')
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->get()
            ->map(function (Pago $pago) {
                return [
                    'id' => $pago->id,
                    'numero_comprobante' => str_pad(
                        $pago->numero_comprobante,
                        6,
                        '0',
                        STR_PAD_LEFT
                    ),
                    'fecha_pago' => $pago->fecha_pago?->format('Y-m-d'),
                    'monto' => (float) $pago->monto,
                    'metodo_pago' => $pago->metodo_pago,
                    'origen' => $pago->origen,
                    'estado' => $pago->estado,
                    'observacion' => $pago->observacion,
                    'vencimiento_cuota' => $pago->vencimiento_cuota?->format('Y-m-d'),
                    'pdf_url' => url(
                        '/api/mobile/comprobantes/' . $pago->id . '/pdf'
                    ),
                ];
            });

        return response()->json([
            'comprobantes' => $pagos,
        ]);
    }

    /**
     * Descargar PDF de un comprobante perteneciente al socio autenticado.
     */
    public function descargarComprobante(Request $request, Pago $pago)
    {
        $user = $request->user();

        $cliente = Cliente::where('user_id', $user->id)->first();

        if (! $cliente) {
            return response()->json([
                'message' => 'No se encontró el socio vinculado a tu usuario.',
            ], 422);
        }

        if (
            $pago->cliente_id !== $cliente->id ||
            $pago->estado !== 'aprobado' ||
            ! $pago->numero_comprobante
        ) {
            abort(404);
        }

        $gimnasio = User::find($cliente->abogado_id);

        $slug = $gimnasio?->slug_estudio ?? 'sportgym';

        $nombreGimnasio = match ($slug) {
            'demo' => 'DemoGym',
            'sportgym' => 'SportGym',
            default => $gimnasio?->name ?? 'Gimnasio',
        };

        $logo = match ($slug) {
            'demo' => public_path('images/logo-demo.png'),
            'sportgym' => public_path('images/logo-sportgym.png'),
            default => null,
        };

        $pago->load('cliente');

        $pdf = Pdf::loadView('comprobantes.pdf', compact(
            'pago',
            'logo',
            'nombreGimnasio'
        ));

        $numero = str_pad(
            $pago->numero_comprobante,
            6,
            '0',
            STR_PAD_LEFT
        );

        return $pdf->download(
            'comprobante-' . $numero . '.pdf'
        );
    }
}