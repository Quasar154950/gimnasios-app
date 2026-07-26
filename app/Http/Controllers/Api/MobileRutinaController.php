<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileRutinaResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileRutinaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('cliente');

        $cliente = $user->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado a esta cuenta.',
            ], 422);
        }

        $cliente = Cliente::query()
            ->whereKey($cliente->id)
            ->with([
                'rutinaAsignaciones' => function ($query) {
                    $query
                        ->where('activa', true)
                        ->with([
                            'rutina.dias.ejercicios.ejercicioBiblioteca',
                        ]);
                },
            ])
            ->firstOrFail();

        $asignacion = $cliente->rutinaAsignaciones->first();

        if (! $asignacion) {
            return response()->json([
                'ok' => true,
                'rutina' => null,
            ]);
        }

        return response()->json([
            'ok' => true,
            'rutina' => new MobileRutinaResource($asignacion),
        ]);
    }
}
