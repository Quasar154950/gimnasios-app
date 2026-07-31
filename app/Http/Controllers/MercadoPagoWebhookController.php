<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\User;
use App\Services\PagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function handle(
        Request $request,
        PagoService $pagoService
    ) {

            $type = $request->input('type')
            ?? $request->input('topic');

        $paymentId = $request->input('data.id')
            ?? $request->input('id');

        $mercadoPagoUserId = $request->input('user_id');

        if ($type !== 'payment' || !$paymentId) {
            return response()->json(['ok' => true]);
        }

        if (!$mercadoPagoUserId) {
            Log::warning('Webhook Mercado Pago sin user_id', [
                'payment_id' => $paymentId,
                'payload' => $request->all(),
            ]);

            return response()->json(['ok' => true]);
        }

        $gimnasio = User::where(
            'mercadopago_user_id',
            (string) $mercadoPagoUserId
        )->first();

        if (
            !$gimnasio ||
            !$gimnasio->mercadopago_enabled ||
            !$gimnasio->mercadopago_access_token
        ) {
            Log::warning('Gimnasio no encontrado para webhook Mercado Pago', [
                'mercadopago_user_id' => $mercadoPagoUserId,
                'payment_id' => $paymentId,
            ]);

            return response()->json(['ok' => true]);
        }

        $response = Http::withToken(
            $gimnasio->mercadopago_access_token
        )->get(
            "https://api.mercadopago.com/v1/payments/{$paymentId}"
        );

        if (!$response->successful()) {
            Log::warning('No se pudo consultar el pago en Mercado Pago', [
                'payment_id' => $paymentId,
                'status_http' => $response->status(),
                'respuesta' => $response->body(),
            ]);

            return response()->json(['ok' => true]);
        }

        $payment = $response->json();

        $status = $payment['status'] ?? null;
        $externalReference = $payment['external_reference'] ?? null;

        if ($status !== 'approved' || !$externalReference) {
            return response()->json(['ok' => true]);
        }

        $pagoExistente = Pago::where(
            'mercadopago_payment_id',
            (string) $paymentId
        )->exists();

        if ($pagoExistente) {
            return response()->json(['ok' => true]);
        }

        $cliente = Cliente::find($externalReference);

        if (
            !$cliente ||
            (int) $cliente->abogado_id !== (int) $gimnasio->id
        ) {
            Log::warning('Socio inválido para pago Mercado Pago', [
                'cliente_id' => $externalReference,
                'gimnasio_id' => $gimnasio->id,
                'payment_id' => $paymentId,
            ]);

            return response()->json(['ok' => true]);
        }

        $fechaVencimiento = $cliente->fecha_vencimiento_cuota
    ? \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota)
    : null;

$fechaBase = $fechaVencimiento && $fechaVencimiento->isFuture()
    ? $fechaVencimiento->toDateString()
    : now()->toDateString();

        $pagoService->registrarPago(
            cliente: $cliente,
            monto: $payment['transaction_amount'] ?? 0,
            metodoPago: 'Mercado Pago',
            observacion: 'Renovación de cuota mensual',
            fechaBase: $fechaBase,
            fechaPago: isset($payment['date_approved'])
                ? substr($payment['date_approved'], 0, 10)
                : now()->toDateString(),
            origen: 'mercadopago',
            estado: 'aprobado',
            mercadopagoPaymentId: (string) $paymentId,
        );

        return response()->json(['ok' => true]);
    }
}
