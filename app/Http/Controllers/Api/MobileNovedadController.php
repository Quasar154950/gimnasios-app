<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Novedad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileNovedadController extends Controller
{
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

        $novedades = Novedad::query()
            ->where('abogado_id', $gimnasio->id)
            ->activas()
            ->where(function ($query) {
                $query
                    ->whereNull('fecha_publicacion')
                    ->orWhereDate(
                        'fecha_publicacion',
                        '<=',
                        now()->toDateString()
                    );
            })
            ->orderByDesc('destacado')
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Novedad $novedad) {
                return [
                    'id' => $novedad->id,
                    'titulo' => $novedad->titulo,
                    'descripcion' => $novedad->descripcion,
                    'tipo' => $novedad->tipo,

                    'imagen' => $novedad->imagen
                        ? url(Storage::url($novedad->imagen))
                        : null,

                    'fecha_publicacion' =>
                        $novedad->fecha_publicacion?->format('Y-m-d'),

                    'destacado' => (bool) $novedad->destacado,

                    'created_at' =>
                        $novedad->created_at?->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'gimnasio' => [
                'id' => $gimnasio->id,
                'nombre' => $gimnasio->name,
                'slug' => $gimnasio->slug_estudio,
            ],
            'cantidad' => $novedades->count(),
            'novedades' => $novedades,
        ]);
    }
}
