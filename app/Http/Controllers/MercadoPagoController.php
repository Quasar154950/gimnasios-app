<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class MercadoPagoController extends Controller
{
    /**
     * Pantalla configuración Mercado Pago
     */
    public function index()
    {
        $user = auth()->user();

        return view('mercadopago.index', compact('user'));
    }

    /**
     * Guardar configuración Mercado Pago
     */
    public function update(Request $request)
    {
        $request->validate([
            'mercadopago_public_key' => ['nullable', 'string'],
            'mercadopago_access_token' => ['nullable', 'string'],
        ]);

        $user = auth()->user();

        $user->update([
            'mercadopago_enabled' => $request->has('mercadopago_enabled'),
            'mercadopago_public_key' => $request->mercadopago_public_key,
            'mercadopago_access_token' => $request->mercadopago_access_token,
            'mercadopago_sandbox' => $request->has('mercadopago_sandbox'),
        ]);

        return back()->with('success', 'Configuración de Mercado Pago guardada correctamente.');
    }

    /**
 * Redirigir al gimnasio a Mercado Pago para autorizar la conexión
 */
public function conectar()
{
    $state = Str::random(40);

    session([
        'mercadopago_oauth_state' => $state,
    ]);

    $url = 'https://auth.mercadopago.com/authorization?' . http_build_query([
        'client_id' => config('services.mercadopago_oauth.client_id'),
        'response_type' => 'code',
        'platform_id' => 'mp',
        'state' => $state,
        'redirect_uri' => config('services.mercadopago_oauth.redirect_uri'),
    ]);

    return redirect()->away($url);
}

    /**
     * Recibir la autorización de Mercado Pago y guardar los tokens
     */
    public function callback(Request $request)
    {
        if ($request->filled('error')) {
            return redirect()
                ->route('mercadopago.index')
                ->with('error', 'La conexión con Mercado Pago fue cancelada o rechazada.');
        }

        $request->validate([
            'code' => ['required', 'string'],
            'state' => ['required', 'string'],
        ]);

        $stateGuardado = session()->pull('mercadopago_oauth_state');

        if (
            !$stateGuardado ||
            !hash_equals($stateGuardado, $request->state)
        ) {
            return redirect()
                ->route('mercadopago.index')
                ->with('error', 'No se pudo validar la conexión con Mercado Pago.');
        }

        $response = Http::asJson()->post(
            'https://api.mercadopago.com/oauth/token',
            [
                'client_id' => config('services.mercadopago_oauth.client_id'),
                'client_secret' => config('services.mercadopago_oauth.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $request->code,
                'redirect_uri' => config('services.mercadopago_oauth.redirect_uri'),
            ]
        );

        if ($response->failed()) {
            return redirect()
                ->route('mercadopago.index')
                ->with('error', 'Mercado Pago no pudo completar la conexión.');
        }

        $datos = $response->json();

        $user = auth()->user();

        $user->update([
            'mercadopago_enabled' => true,
            'mercadopago_access_token' => $datos['access_token'],
            'mercadopago_refresh_token' => $datos['refresh_token'] ?? null,
            'mercadopago_public_key' => $datos['public_key'] ?? null,
            'mercadopago_user_id' => $datos['user_id'] ?? null,
            'mercadopago_token_expires_at' => isset($datos['expires_in'])
                ? now()->addSeconds((int) $datos['expires_in'])
                : null,
            'mercadopago_connected_at' => now(),
            'mercadopago_sandbox' => false,
        ]);

        return redirect()
            ->route('mercadopago.index')
            ->with('success', 'Mercado Pago fue conectado correctamente.');
    }

        /**
     * Desconectar la cuenta de Mercado Pago del gimnasio
     */
    public function desconectar()
    {
        $user = auth()->user();

        $user->update([
            'mercadopago_enabled' => false,
            'mercadopago_public_key' => null,
            'mercadopago_access_token' => null,
            'mercadopago_refresh_token' => null,
            'mercadopago_user_id' => null,
            'mercadopago_token_expires_at' => null,
            'mercadopago_connected_at' => null,
            'mercadopago_sandbox' => false,
        ]);

        return redirect()
            ->route('mercadopago.index')
            ->with('success', 'Mercado Pago fue desconectado correctamente.');
    }

    /**
     * Crear pago Mercado Pago
     */
    public function crearPago()
    {
        $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

        if (!$cliente) {
            return back()->with('error', 'No se encontró el socio vinculado a tu usuario.');
        }

        $gimnasio = \App\Models\User::find($cliente->abogado_id);

        if (
            !$gimnasio ||
            !$gimnasio->mercadopago_enabled ||
            !$gimnasio->mercadopago_access_token
        ) {
            return back()->with('error', '⚠️ El gimnasio todavía no tiene habilitados los pagos online.');
        }

        MercadoPagoConfig::setAccessToken($gimnasio->mercadopago_access_token);

        $client = new PreferenceClient();

        $preference = $client->create([
            "items" => [
                [
                    "title" => "Cuota mensual gimnasio",
                    "quantity" => 1,
                    "unit_price" => 1000,
                    "currency_id" => "ARS",
                ]
            ],

            "payer" => [
                "name" => $cliente->nombre,
                "email" => auth()->user()->email,
            ],
            "external_reference" => (string) $cliente->id,

            "notification_url" => route('webhooks.mercadopago.gimnasio'),

            "back_urls" => [
                "success" => route('cliente.cuota'),
                "failure" => route('cliente.cuota'),
                "pending" => route('cliente.cuota'),
            ],

            "auto_return" => "approved",
        ]);

        return redirect($preference->init_point);
    }
}
