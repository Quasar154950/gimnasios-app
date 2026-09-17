<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
}
