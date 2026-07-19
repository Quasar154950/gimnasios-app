<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MensajeCliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileMensajeController extends Controller
{
    /**
     * Obtener la conversación del socio.
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

        /*
        |--------------------------------------------------------------------------
        | MARCAR COMO LEÍDOS LOS MENSAJES DEL GIMNASIO
        |--------------------------------------------------------------------------
        */

        $mensajesPendientes = $cliente
            ->mensajes()
            ->where('remitente', 'estudio')
            ->where('leido', false);

        if ($cliente->chat_borrado_cliente_at) {
            $mensajesPendientes->where(
                'created_at',
                '>',
                $cliente->chat_borrado_cliente_at
            );
        }

        $mensajesPendientes->update([
            'leido' => true,
            'leido_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | OBTENER CONVERSACIÓN VISIBLE PARA EL SOCIO
        |--------------------------------------------------------------------------
        */

        $query = $cliente
            ->mensajes()
            ->oldest();

        if ($cliente->chat_borrado_cliente_at) {
            $query->where(
                'created_at',
                '>',
                $cliente->chat_borrado_cliente_at
            );
        }

        $mensajes = $query
            ->get()
            ->map(function (MensajeCliente $mensaje) {
                return [
                    'id' => $mensaje->id,
                    'mensaje' => $mensaje->mensaje,
                    'remitente' => $mensaje->remitente,
                    'es_mio' => $mensaje->remitente === 'cliente',
                    'leido' => (bool) $mensaje->leido,
                    'leido_at' => $mensaje->leido_at?->toIso8601String(),
                    'created_at' => $mensaje->created_at?->toIso8601String(),
                    'fecha' => $mensaje->created_at?->format('d/m/Y'),
                    'hora' => $mensaje->created_at?->format('H:i'),
                ];
            });

        return response()->json([
            'gimnasio' => [
                'nombre' => $cliente->abogado?->nombre
                    ?? $cliente->abogado?->name
                    ?? 'Mi gimnasio',
            ],
            'socio' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
            ],
            'mensajes' => $mensajes,
        ]);
    }

    /**
     * Enviar un mensaje desde Flutter.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user()->load('cliente');

        $cliente = $user->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado a esta cuenta.',
            ], 422);
        }

        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $mensaje = MensajeCliente::create([
            'cliente_id' => $cliente->id,
            'user_id' => $user->id,
            'mensaje' => trim($datos['mensaje']),
            'remitente' => 'cliente',
        ]);

        return response()->json([
            'message' => 'Mensaje enviado correctamente.',
            'mensaje' => [
                'id' => $mensaje->id,
                'mensaje' => $mensaje->mensaje,
                'remitente' => $mensaje->remitente,
                'es_mio' => true,
                'leido' => (bool) $mensaje->leido,
                'leido_at' => $mensaje->leido_at?->toIso8601String(),
                'created_at' => $mensaje->created_at?->toIso8601String(),
                'fecha' => $mensaje->created_at?->format('d/m/Y'),
                'hora' => $mensaje->created_at?->format('H:i'),
            ],
        ], 201);
    }

    /**
     * Vaciar la conversación solamente para el socio.
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user()->load('cliente');

        $cliente = $user->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado a esta cuenta.',
            ], 422);
        }

        $cliente->chat_borrado_cliente_at = now();
        $cliente->save();

        return response()->json([
            'message' => 'La conversación fue limpiada correctamente.',
        ]);
    }
}
