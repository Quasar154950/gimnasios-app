<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
