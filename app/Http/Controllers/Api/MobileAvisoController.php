<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aviso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileAvisoController extends Controller
{
    /**
     * Obtener los avisos visibles para el socio.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('cliente.abogado');

        $cliente = $user->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado a esta cuenta.',
            ], 422);
        }

        $gimnasio = $cliente->abogado;

        if (! $gimnasio) {
            return response()->json([
                'message' => 'No existe un gimnasio asociado a este socio.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | AVISOS VISIBLES
        |--------------------------------------------------------------------------
        |
        | Solo devuelve avisos:
        | - pertenecientes al gimnasio del socio;
        | - activos;
        | - cuya publicación ya comenzó;
        | - que todavía no vencieron.
        |
        */

        $ahora = now();

        $avisos = Aviso::query()
            ->where('abogado_id', $gimnasio->id)
            ->where('activo', true)
            ->where(function ($query) use ($ahora) {
                $query
                    ->whereNull('fecha_publicacion')
                    ->orWhere('fecha_publicacion', '<=', $ahora);
            })
            ->where(function ($query) use ($ahora) {
                $query
                    ->whereNull('fecha_vencimiento')
                    ->orWhere('fecha_vencimiento', '>=', $ahora);
            })
            ->orderByRaw("
                CASE prioridad
                    WHEN 'urgente' THEN 1
                    WHEN 'importante' THEN 2
                    ELSE 3
                END
            ")
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Aviso $aviso) {
                return [
                    'id' => $aviso->id,
                    'titulo' => $aviso->titulo,
                    'mensaje' => $aviso->mensaje,
                    'prioridad' => $aviso->prioridad,
                    'activo' => (bool) $aviso->activo,

                    'fecha_publicacion' =>
                        $aviso->fecha_publicacion?->toIso8601String(),

                    'fecha_vencimiento' =>
                        $aviso->fecha_vencimiento?->toIso8601String(),

                    'created_at' =>
                        $aviso->created_at?->toIso8601String(),

                    'fecha' =>
                        ($aviso->fecha_publicacion ?? $aviso->created_at)
                            ?->format('d/m/Y'),

                    'hora' =>
                        ($aviso->fecha_publicacion ?? $aviso->created_at)
                            ?->format('H:i'),

                    'es_urgente' =>
                        $aviso->prioridad === 'urgente',

                    'es_importante' =>
                        $aviso->prioridad === 'importante',
                ];
            });

        return response()->json([
            'gimnasio' => [
                'id' => $gimnasio->id,
                'nombre' => $gimnasio->nombre
                    ?? $gimnasio->name
                    ?? 'Mi gimnasio',
            ],

            'cantidad' => $avisos->count(),

            'avisos' => $avisos,
        ]);
    }
}
