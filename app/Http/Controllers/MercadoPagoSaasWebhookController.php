<?php

namespace App\Http\Controllers;

use App\Models\SaasPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MercadoPagoSaasWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $accessToken = env('MERCADOPAGO_SAAS_ACCESS_TOKEN');

        if (!$accessToken) {
            return response()->json(['error' => 'Mercado Pago SaaS no configurado'], 500);
        }

        $type = $request->input('type') ?? $request->input('topic');
        $paymentId = $request->input('data.id') ?? $request->input('id');

        if ($type !== 'payment' || !$paymentId) {
            return response()->json(['ok' => true]);
        }

        $response = Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$response->successful()) {
            return response()->json(['error' => 'No se pudo consultar el pago'], 500);
        }

        $payment = $response->json();

        $externalReference = $payment['external_reference'] ?? null;
        $status = $payment['status'] ?? null;

        if (!$externalReference) {
            return response()->json(['ok' => true]);
        }

        $pago = SaasPago::where('external_reference', $externalReference)->first();

        if (!$pago) {
            return response()->json(['ok' => true]);
        }

        $yaEstabaAprobado = $pago->estado === 'approved';

        $pago->update([
            'payment_id' => $paymentId,
            'estado' => $status ?? 'desconocido',
            'metodo_pago' => $payment['payment_method_id'] ?? null,
            'fecha_pago' => $status === 'approved'
                ? ($pago->fecha_pago ?? now())
                : $pago->fecha_pago,
        ]);

        if ($status === 'approved' && !$yaEstabaAprobado) {
            $pago->user->renovarSuscripcion(30);
        }

        return response()->json(['ok' => true]);
    }
}
