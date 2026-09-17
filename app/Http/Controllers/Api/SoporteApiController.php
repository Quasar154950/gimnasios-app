<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SaasPago;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class SoporteApiController extends Controller
{
    /**
     * Devuelve los gimnasios administradores con cantidad de socios.
     *
     * SOLO LECTURA.
     */
    public function gimnasios(): JsonResponse
    {
        $gimnasios = User::query()
            ->where('role', 'abogado')
            ->where('tipo_app', 'gimnasios')
            ->withCount([
                'clientesAdministrados as total_socios',
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($gimnasio) {
                return [
                    'id' => $gimnasio->id,
                    'name' => $gimnasio->name,
                    'email' => $gimnasio->email,
                    'slug' => $gimnasio->slug_estudio,
                    'acceso_url' => url('/estudio/' . $gimnasio->slug_estudio),
                    'activo' => $gimnasio->activo,
                    'fecha_vencimiento' => $gimnasio->fecha_vencimiento,
                    'plan' => $gimnasio->plan,
                    'precio_suscripcion' => $gimnasio->precio_suscripcion,
                    'ultimo_login_at' => $gimnasio->ultimo_login_at,
                    'total_socios' => $gimnasio->total_socios,
                ];
            });

        return response()->json([
            'ok' => true,
            'producto' => 'gimnasios',
            'total_gimnasios' => $gimnasios->count(),
            'total_socios' => $gimnasios->sum('total_socios'),
            'gimnasios' => $gimnasios,
        ]);
    }

    /**
     * Renueva 30 días la suscripción de un gimnasio.
     */
    public function renovar(User $gimnasio): JsonResponse
    {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        $gimnasio->renovarSuscripcion(30);
        $gimnasio->refresh();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Suscripción renovada +30 días.',
            'gimnasio' => [
                'id' => $gimnasio->id,
                'activo' => $gimnasio->activo,
                'fecha_vencimiento' => $gimnasio->fecha_vencimiento,
            ],
        ]);
    }

    /**
     * Suspende o activa un gimnasio.
     */
    public function toggleActivo(User $gimnasio): JsonResponse
    {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        $gimnasio->activo = !$gimnasio->activo;
        $gimnasio->save();
        $gimnasio->refresh();

        return response()->json([
            'ok' => true,
            'mensaje' => $gimnasio->activo
                ? 'Gimnasio activado correctamente.'
                : 'Gimnasio suspendido correctamente.',
            'gimnasio' => [
                'id' => $gimnasio->id,
                'activo' => $gimnasio->activo,
            ],
        ]);
    }

    /**
     * Actualiza los datos del administrador de un gimnasio.
     */
    public function actualizarAdministrador(
        Request $request,
        User $gimnasio
    ): JsonResponse {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($gimnasio->id),
            ],
        ]);

        $gimnasio->update([
            'name' => $datos['name'],
            'email' => $datos['email'],
        ]);

        $gimnasio->refresh();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Administrador actualizado correctamente.',
            'gimnasio' => [
                'id' => $gimnasio->id,
                'name' => $gimnasio->name,
                'email' => $gimnasio->email,
            ],
        ]);
    }

    /**
     * Resetea la contraseña del administrador de un gimnasio.
     */
    public function resetPassword(User $gimnasio): JsonResponse
    {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        $nuevaPassword = $gimnasio->resetearPassword();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Contraseña reseteada correctamente.',
            'password' => $nuevaPassword,
            'gimnasio' => [
                'id' => $gimnasio->id,
                'name' => $gimnasio->name,
                'email' => $gimnasio->email,
            ],
        ]);
    }

    /**
     * Actualiza la suscripción de un gimnasio.
     */
    public function actualizarSuscripcion(
        Request $request,
        User $gimnasio
    ): JsonResponse {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        $datos = $request->validate([
            'fecha_vencimiento' => [
                'nullable',
                'date',
            ],
            'plan' => [
                'required',
                Rule::in([
                    'basico',
                    'pro',
                    'premium',
                    'personalizado',
                ]),
            ],
            'precio_suscripcion' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $gimnasio->update([
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'plan' => $datos['plan'],
            'precio_suscripcion' => $datos['precio_suscripcion'],
        ]);

        $gimnasio->refresh();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Suscripción actualizada correctamente.',
            'gimnasio' => [
                'id' => $gimnasio->id,
                'fecha_vencimiento' => $gimnasio->fecha_vencimiento,
                'plan' => $gimnasio->plan,
                'precio_suscripcion' => $gimnasio->precio_suscripcion,
            ],
        ]);
    }

    /**
     * Genera un acceso temporal firmado para ingresar como
     * administrador de un gimnasio desde el soporte central.
     */
    public function verComo(User $gimnasio): JsonResponse
    {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        $url = URL::temporarySignedRoute(
            'soporte.gimnasios.impersonar',
            now()->addMinutes(5),
            [
                'gimnasio' => $gimnasio->id,
            ]
        );

        return response()->json([
            'ok' => true,
            'mensaje' => 'Acceso temporal generado correctamente.',
            'url' => $url,
        ]);
    }

    /**
     * Genera un nuevo link de pago SaaS para un gimnasio
     * desde el soporte central.
     */
    public function cobrarSaas(User $gimnasio): JsonResponse
    {
        if (
            $gimnasio->role !== 'abogado'
            || $gimnasio->tipo_app !== 'gimnasios'
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El usuario indicado no corresponde a un gimnasio.',
            ], 404);
        }

        if (
            !$gimnasio->precio_suscripcion
            || $gimnasio->precio_suscripcion <= 0
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Este gimnasio no tiene precio de suscripción configurado.',
            ], 422);
        }

        $accessToken = env('MERCADOPAGO_SAAS_ACCESS_TOKEN');

        if (!$accessToken) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Falta configurar Mercado Pago SaaS.',
            ], 500);
        }

        $pago = SaasPago::create([
            'user_id' => $gimnasio->id,
            'plan' => $gimnasio->plan,
            'monto' => $gimnasio->precio_suscripcion,
            'estado' => 'pendiente',
            'external_reference' =>
                'saas_pago_' . $gimnasio->id . '_' . now()->timestamp,
        ]);

        MercadoPagoConfig::setAccessToken($accessToken);

        $client = new PreferenceClient();

        $baseUrl = rtrim(config('app.url'), '/');

        $payload = [
            'items' => [[
                'title' =>
                    'Suscripción SaaS MCTandil - '
                    . strtoupper($gimnasio->plan ?? 'PLAN'),
                'quantity' => 1,
                'currency_id' => 'ARS',
                'unit_price' => (int) $gimnasio->precio_suscripcion,
            ]],

            'external_reference' => $pago->external_reference,

            'back_urls' => [
                'success' => $baseUrl . '/suscripcion',
                'failure' => $baseUrl . '/suscripcion',
                'pending' => $baseUrl . '/suscripcion',
            ],

            'auto_return' => 'approved',

            'notification_url' =>
                $baseUrl . '/webhooks/mercadopago/saas',
        ];

        Log::info('MP SaaS payload soporte central Gimnasios', $payload);

        try {
            $preference = $client->create($payload);

            $checkoutUrl = $preference->init_point;

            $pago->update([
                'checkout_url' => $checkoutUrl,
            ]);

            return response()->json([
                'ok' => true,
                'mensaje' => 'Link de pago SaaS generado correctamente.',
                'pago' => [
                    'id' => $pago->id,
                    'gimnasio_id' => $gimnasio->id,
                    'gimnasio' => $gimnasio->name,
                    'plan' => $gimnasio->plan,
                    'monto' => $gimnasio->precio_suscripcion,
                    'estado' => $pago->estado,
                    'checkout_url' => $checkoutUrl,
                ],
            ]);

        } catch (MPApiException $e) {
            Log::error('MP SaaS API error soporte central Gimnasios', [
                'message' => $e->getMessage(),
                'api_response' => method_exists($e, 'getApiResponse')
                    ? $e->getApiResponse()
                    : null,
            ]);

            $pago->update([
                'estado' => 'error',
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => 'Mercado Pago respondió con error al crear el link.',
            ], 502);

        } catch (\Throwable $e) {
            Log::error('MP SaaS error general soporte central Gimnasios', [
                'message' => $e->getMessage(),
            ]);

            $pago->update([
                'estado' => 'error',
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => 'No se pudo generar el link de pago.',
            ], 500);
        }
    }
}
