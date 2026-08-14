<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ActivarCuentaSocioController extends Controller
{
    public function activar(Request $request): JsonResponse
    {
        try {
            $datos = $request->validate([
                'identificador' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                ],
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ], [
                'identificador.required' => 'Ingresá tu DNI o email.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                'password.confirmed' => 'La confirmación de contraseña no coincide.',
                'slug.required' => 'No pudimos identificar el gimnasio.',
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => 'Revisá los datos ingresados.',
                'errors' => $exception->errors(),
            ], 422);
        }

        $identificador = trim($datos['identificador']);
        $slug = trim($datos['slug']);

        $gimnasio = User::where('slug_estudio', $slug)->first();

        if (! $gimnasio) {
            return response()->json([
                'message' => 'No pudimos identificar el gimnasio.',
            ], 404);
        }

        $cliente = Cliente::where('abogado_id', $gimnasio->id)
            ->where(function ($query) use ($identificador) {
                $query->where('dni', $identificador)
                    ->orWhere('email', $identificador);
            })
            ->where('archivado', false)
            ->first();

        if (! $cliente) {
            return response()->json([
                'message' => 'No encontramos un socio activo con ese DNI o email en este gimnasio.',
                'debug' => [
                    'slug_recibido' => $slug,
                    'gimnasio_id' => $gimnasio->id,
                    'identificador_recibido' => $identificador,
                ],
            ], 404);
        }

        if ($cliente->user_id) {
            return response()->json([
                'message' => 'Esta cuenta ya fue activada. Iniciá sesión con tu email y contraseña.',
            ], 409);
        }

        if (! $cliente->email) {
            return response()->json([
                'message' => 'Este socio no tiene un email cargado. Contactá al gimnasio.',
            ], 422);
        }

        if (User::where('email', $cliente->email)->exists()) {
            return response()->json([
                'message' => 'Ya existe un usuario con ese email. Contactá al gimnasio.',
            ], 409);
        }

        try {
            $resultado = DB::transaction(function () use ($cliente, $datos) {
                $user = User::create([
                    'name' => $cliente->nombre,
                    'email' => $cliente->email,
                    'password' => Hash::make($datos['password']),
                    'role' => 'cliente',
                    'activo' => true,
                ]);

                $cliente->update([
                    'user_id' => $user->id,
                ]);

                $token = $user->createToken('flutter-mobile')->plainTextToken;

                return [
                    'user' => $user,
                    'cliente' => $cliente->fresh(),
                    'token' => $token,
                ];
            });
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'No pudimos activar la cuenta. Intentá nuevamente.',
            ], 500);
        }

        return response()->json([
            'message' => '¡Cuenta activada correctamente! Bienvenido a SportGym Tandil.',
            'token' => $resultado['token'],
            'token_type' => 'Bearer',
            'user' => [
                'id' => $resultado['user']->id,
                'name' => $resultado['user']->name,
                'email' => $resultado['user']->email,
                'role' => $resultado['user']->role,
            ],
            'cliente' => [
                'id' => $resultado['cliente']->id,
                'nombre' => $resultado['cliente']->nombre,
                'dni' => $resultado['cliente']->dni,
                'email' => $resultado['cliente']->email,
                'abogado_id' => $resultado['cliente']->abogado_id,
            ],
        ], 201);
    }
}