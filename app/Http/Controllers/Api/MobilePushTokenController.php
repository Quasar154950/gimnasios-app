<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispositivoPush;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobilePushTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:500'],
            'plataforma' => ['nullable', 'string', 'max:20'],
            'modelo' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        $dispositivo = DispositivoPush::updateOrCreate(
            [
                'token' => $validated['token'],
            ],
            [
                'user_id' => $user->id,
                'plataforma' => $validated['plataforma'] ?? 'android',
                'modelo' => $validated['modelo'] ?? null,
                'ultimo_uso_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Token push guardado correctamente.',
            'dispositivo' => [
                'id' => $dispositivo->id,
                'plataforma' => $dispositivo->plataforma,
                'modelo' => $dispositivo->modelo,
                'ultimo_uso_at' => $dispositivo->ultimo_uso_at,
            ],
        ]);
    }
}
