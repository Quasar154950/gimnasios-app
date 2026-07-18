<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\ReservaTurno;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileHomeController extends Controller
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

        /*
        |--------------------------------------------------------------------------
        | CUOTA
        |--------------------------------------------------------------------------
        */

        $fechaVencimientoCuota = $cliente->fecha_vencimiento_cuota
            ? Carbon::parse($cliente->fecha_vencimiento_cuota)
            : null;

        $estadoCuota = 'sin_fecha';

        if ($fechaVencimientoCuota) {
            $estadoCuota = $fechaVencimientoCuota->copy()->endOfDay()->isPast()
                ? 'vencida'
                : 'al_dia';
        }

        $diasRestantes = $fechaVencimientoCuota
            ? now()->startOfDay()->diffInDays(
                $fechaVencimientoCuota->copy()->startOfDay(),
                false
            )
            : null;

        /*
        |--------------------------------------------------------------------------
        | PRÓXIMA RESERVA
        |--------------------------------------------------------------------------
        */

        $ahora = now();

        $proximaReserva = ReservaTurno::query()
            ->with('turno')
            ->where('cliente_id', $cliente->id)
            ->whereHas('turno', function ($query) use ($ahora, $gimnasio) {
                $query
                    ->where('activo', true)
                    ->where(function ($subquery) use ($ahora) {
                        $subquery
                            ->whereDate('fecha', '>', $ahora->toDateString())
                            ->orWhere(function ($hoy) use ($ahora) {
                                $hoy
                                    ->whereDate('fecha', $ahora->toDateString())
                                    ->whereTime(
                                        'hora_fin',
                                        '>=',
                                        $ahora->format('H:i:s')
                                    );
                            });
                    });

                if ($gimnasio) {
                    $query->where('abogado_id', $gimnasio->id);
                }
            })
            ->where(function ($query) {
                $query
                    ->whereNull('estado')
                    ->orWhereNotIn('estado', [
                        'cancelada',
                        'cancelado',
                        'rechazada',
                        'rechazado',
                    ]);
            })
            ->get()
            ->sortBy(function (ReservaTurno $reserva) {
                $turno = $reserva->turno;

                if (! $turno) {
                    return PHP_INT_MAX;
                }

                return Carbon::parse(
                    $turno->fecha . ' ' . $turno->hora_inicio
                )->timestamp;
            })
            ->first();

        $turno = $proximaReserva?->turno;

        /*
        |--------------------------------------------------------------------------
        | MENSAJES SIN LEER
        |--------------------------------------------------------------------------
        */

        $mensajesSinLeer = $cliente->mensajes()
            ->where('leido', false)
            ->where('remitente', '!=', 'cliente')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | MUSCULACIÓN EN TIEMPO REAL
        |--------------------------------------------------------------------------
        */

        $presentesAhora = Asistencia::query()
            ->where('presente', true)
            ->whereNull('hora_salida')
            ->whereHas('cliente', function ($query) use ($gimnasio) {
                if ($gimnasio) {
                    $query->where('abogado_id', $gimnasio->id);
                }
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
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

            'cuota' => [
                'estado' => $estadoCuota,
                'vencimiento' => $fechaVencimientoCuota?->format('Y-m-d'),
                'dias_restantes' => $diasRestantes,
            ],

            'proxima_reserva' => $turno
                ? [
                    'reserva_id' => $proximaReserva->id,
                    'turno_id' => $turno->id,
                    'actividad' => $turno->actividad,
                    'profesor' => $turno->profesor,
                    'fecha' => Carbon::parse($turno->fecha)->format('Y-m-d'),
                    'hora_inicio' => Carbon::parse(
                        $turno->hora_inicio
                    )->format('H:i'),
                    'hora_fin' => Carbon::parse(
                        $turno->hora_fin
                    )->format('H:i'),
                    'estado' => $proximaReserva->estado,
                ]
                : null,

            'mensajes' => [
                'sin_leer' => $mensajesSinLeer,
            ],

            'musculacion' => [
                'presentes_ahora' => $presentesAhora,
            ],

            'qr' => [
                'url' => route('asistencias.marcar', $cliente->id),
            ],
        ]);
    }
}
