<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

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
}
