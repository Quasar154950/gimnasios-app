<?php

namespace App\Http\Controllers;

use App\Models\SaasPago;
use App\Models\User;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class SaasPagoController extends Controller
{
    public function pagar(User $user)
    {
        $soporte = auth()->user();

        if (!$soporte || $soporte->email !== 'soporte@tuempresa.com') {
            abort(403);
        }

        if ($user->email === 'soporte@tuempresa.com') {
            abort(403);
        }

        if (!$user->precio_suscripcion || $user->precio_suscripcion <= 0) {
            return redirect('/soporte?error=Este cliente no tiene precio de suscripción configurado');
        }

        $accessToken = env('MERCADOPAGO_SAAS_ACCESS_TOKEN');

        if (!$accessToken) {
            return redirect('/soporte?error=Falta configurar Mercado Pago SaaS');
        }

        $pago = SaasPago::create([
            'user_id' => $user->id,
            'plan' => $user->plan,
            'monto' => $user->precio_suscripcion,
            'estado' => 'pendiente',
            'external_reference' => 'saas_pago_' . $user->id . '_' . now()->timestamp,
        ]);

        MercadoPagoConfig::setAccessToken($accessToken);

        $client = new PreferenceClient();

        try {

            $preference = $client->create([

                'items' => [
                    [
                        'title' => 'Suscripción SaaS MCTandil - ' . strtoupper($user->plan),
                        'quantity' => 1,
                        'currency_id' => 'ARS',
                        'unit_price' => (float) $user->precio_suscripcion,
                    ],
                ],

                'payer' => [
                    'email' => $user->email,
                ],

                'external_reference' => $pago->external_reference,

                'back_urls' => [
                    'success' => url('/soporte/saas-pagos/exito'),
                    'failure' => url('/soporte/saas-pagos/error'),
                    'pending' => url('/soporte/saas-pagos/pendiente'),
                ],

            ]);

            $checkoutUrl = $preference->init_point;

            $pago->update([
                'checkout_url' => $checkoutUrl,
            ]);

            return redirect('/soporte?success=Link de pago SaaS generado correctamente');

        } catch (MPApiException $e) {

            $pago->update([
                'estado' => 'error',
            ]);

            return redirect('/soporte?error=Mercado Pago respondió con error al crear el link');

        } catch (\Throwable $e) {

            $pago->update([
                'estado' => 'error',
            ]);

            return redirect('/soporte?error=No se pudo generar el link de pago');
        }
    }

    public function exito()
    {
        return redirect('/soporte?success=Pago iniciado correctamente. Luego conectaremos el webhook para renovar automático');
    }

    public function error()
    {
        return redirect('/soporte?error=El pago fue cancelado o rechazado');
    }

    public function pendiente()
    {
        return redirect('/soporte?success=El pago quedó pendiente de aprobación');
    }
}
