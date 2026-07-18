<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['El email o la contraseña son incorrectos.'],
            ]);
        }

        if ($user->role !== 'cliente') {
            return response()->json([
                'message' => 'Esta aplicación es exclusiva para socios.',
            ], 403);
        }

        if (! $user->activo) {
            return response()->json([
                'message' => 'Tu cuenta se encuentra inactiva.',
            ], 403);
        }

        $cliente = $user->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado a esta cuenta.',
            ], 422);
        }

        // Evita acumular tokens innecesarios del mismo usuario.
        $user->tokens()->where('name', 'sportgym-mobile')->delete();

        $token = $user->createToken('sportgym-mobile')->plainTextToken;

        $user->forceFill([
            'ultimo_login_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Inicio de sesión correcto.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'activo' => $user->activo,
                'ultimo_login_at' => $user->ultimo_login_at,
            ],
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre ?? $user->name,
                'apellido' => $cliente->apellido ?? null,
                'dni' => $cliente->dni ?? null,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
{
    $user = $request->user()->load('cliente.abogado');

    $cliente = $user->cliente;

    if (! $cliente) {
        return response()->json([
            'message' => 'No existe un socio asociado a esta cuenta.',
        ], 422);
    }

    $gimnasio = $cliente->abogado;

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'activo' => $user->activo,
            'ultimo_login_at' => $user->ultimo_login_at,
        ],

        'cliente' => [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre ?? $user->name,
            'apellido' => $cliente->apellido ?? null,
            'dni' => $cliente->dni ?? null,
        ],

        'gimnasio' => [
            'id' => $gimnasio?->id,
            'nombre' => $gimnasio?->nombre_estudio ?? 'SportGym Tandil',
            'slug' => $gimnasio?->slug_estudio ?? 'sportgym',
        ],
    ]);
}

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
