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
}
