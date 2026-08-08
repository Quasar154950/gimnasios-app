<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'slug' => ['required', 'string', 'in:sportgym,demo'],
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

        $gimnasio = $cliente->abogado;

if (! $gimnasio) {
    return response()->json([
        'message' => 'No existe un gimnasio asociado a esta cuenta.',
    ], 422);
}

$slugGimnasio = $gimnasio->slug_estudio;

if ($slugGimnasio !== $credentials['slug']) {
    return response()->json([
        'message' => 'Este usuario no pertenece a este gimnasio.',
    ], 403);
}

        // Evita acumular tokens del mismo teléfono.
        $user->tokens()
            ->where('name', 'gym-mobile')
            ->delete();

        $token = $user
            ->createToken('gym-mobile')
            ->plainTextToken;

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
                'activo' => (bool) $user->activo,
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

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $genericMessage = 'Si el email está registrado, recibirás un enlace para restablecer tu contraseña.';

        $user = User::where('email', $validated['email'])
            ->where('role', 'cliente')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA SEGURA
        |--------------------------------------------------------------------------
        |
        | Siempre devolvemos el mismo mensaje aunque el email no exista.
        | Así evitamos revelar qué usuarios están registrados.
        |
        */

        if (! $user) {
            return response()->json([
                'message' => $genericMessage,
            ]);
        }

        $status = Password::broker('users')->sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => $genericMessage,
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Ya se solicitó un enlace recientemente. Esperá unos minutos antes de volver a intentarlo.',
            ], 429);
        }

        return response()->json([
            'message' => 'No pudimos enviar el correo en este momento. Intentá nuevamente más tarde.',
        ], 500);
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
                'activo' => (bool) $user->activo,
                'ultimo_login_at' => $user->ultimo_login_at,
            ],

            'cliente' => [
    'id' => $cliente->id,
    'nombre' => $cliente->nombre ?? $user->name,
    'apellido' => $cliente->apellido ?? null,
    'dni' => $cliente->dni ?? null,
    'contacto' => $cliente->contacto ?? null,
    'telefono' => $cliente->telefono ?? null,
    'direccion' => $cliente->direccion ?? null,
    'fecha_nacimiento' => $cliente->fecha_nacimiento ?? null,
    'peso' => ! is_null($cliente->peso)
        ? (float) $cliente->peso
        : null,
    'altura' => ! is_null($cliente->altura)
        ? (int) $cliente->altura
        : null,
    'contacto_emergencia' =>
        $cliente->contacto_emergencia ?? null,
    'fecha_vencimiento_cuota' =>
        $cliente->fecha_vencimiento_cuota ?? null,
],

            'gimnasio' => [
                'id' => $gimnasio?->id,
                'nombre' =>
                    $gimnasio?->nombre_estudio
                    ?? $gimnasio?->name
                    ?? 'Gimnasio',
                'slug' =>
                    $gimnasio?->slug_estudio
                    ?? 'gimnasio',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
    $validated = $request->validate([
        'fcm_token' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    if (! empty($validated['fcm_token'])) {
        $request->user()
            ->dispositivosPush()
            ->where('token', $validated['fcm_token'])
            ->delete();
    }

    $request->user()
        ->currentAccessToken()
        ?->delete();

    return response()->json([
        'message' => 'Sesión cerrada correctamente.',
    ]);
 }
}
