<?php

namespace App\Services;

use App\Models\DispositivoPush;
use App\Models\NotificacionPush;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class NotificacionPushService
{
    public function __construct(
        private readonly FirebasePushService $firebasePushService
    ) {
    }

    public function enviar(
        NotificacionPush $notificacion
    ): array {
        /*
        |--------------------------------------------------------------------------
        | EVITAR DOBLE ENVÍO DE PROGRAMADAS
        |--------------------------------------------------------------------------
        */

        if (
            $notificacion->estado === 'enviando'
            || (
                $notificacion->estado === 'enviada'
                && $notificacion->programada_para !== null
            )
        ) {
            return [
                'ok' => false,
                'cantidad_enviada' => 0,
                'error' => 'La notificación ya fue procesada.',
            ];
        }

        $notificacion->update([
            'estado' => 'enviando',
            'error' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | OBTENER DISPOSITIVOS DESTINATARIOS
        |--------------------------------------------------------------------------
        */

        $dispositivos = DispositivoPush::query()
            ->whereHas(
                'user.cliente',
                function (Builder $query) use ($notificacion): void {
                    $query->where(
                        'abogado_id',
                        $notificacion->user_id
                    );

                    if ($notificacion->destinatario === 'cliente') {
                        $query->where(
                            'id',
                            $notificacion->cliente_id
                        );
                    }

                    if ($notificacion->destinatario === 'cuota_vencida') {
                        $query
                            ->whereNotNull(
                                'fecha_vencimiento_cuota'
                            )
                            ->whereDate(
                                'fecha_vencimiento_cuota',
                                '<',
                                now()->toDateString()
                            );
                    }
                }
            )
            ->get();

        if ($dispositivos->isEmpty()) {
            $error = 'No se encontraron dispositivos registrados para los destinatarios seleccionados.';

            $notificacion->update([
                'estado' => 'error',
                'cantidad_enviada' => 0,
                'error' => $error,
            ]);

            return [
                'ok' => false,
                'cantidad_enviada' => 0,
                'error' => $error,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | ENVIAR A FIREBASE
        |--------------------------------------------------------------------------
        */

        $cantidadEnviada = 0;
        $errores = [];

        foreach ($dispositivos as $dispositivo) {
            try {
                $resultado = $this->firebasePushService->enviar(
                    token: $dispositivo->token,
                    titulo: $notificacion->titulo,
                    mensaje: $notificacion->mensaje,
                    data: [
                        'tipo' => $notificacion->tipo,
                        'pantalla' => $notificacion->pantalla ?? 'inicio',
                        'notificacion_id' => (string) $notificacion->id,
                    ],
                );

                if ($resultado['ok'] ?? false) {
                    $cantidadEnviada++;

                    continue;
                }

                $errorFirebase = $resultado['error']
                    ?? 'Firebase devolvió un error desconocido.';

                if (str_contains(
                    strtolower($errorFirebase),
                    'notregistered'
                )) {
                   $dispositivo->delete();

                   continue;
                }

                $errores[] = $errorFirebase;

                
            } catch (Throwable $e) {
                $errores[] = $e->getMessage();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR HISTORIAL
        |--------------------------------------------------------------------------
        */

        if ($cantidadEnviada > 0) {
            $errorParcial = $errores !== []
                ? implode(
                    ' | ',
                    array_unique($errores)
                )
                : null;

            $notificacion->update([
                'estado' => 'enviada',
                'enviada_at' => now(),
                'cantidad_enviada' => $cantidadEnviada,
                'error' => $errorParcial,
            ]);

            return [
                'ok' => true,
                'cantidad_enviada' => $cantidadEnviada,
                'error' => $errorParcial,
            ];
        }

        $error = $errores !== []
            ? implode(
                ' | ',
                array_unique($errores)
            )
            : 'No se pudo enviar la notificación.';

        $notificacion->update([
            'estado' => 'error',
            'cantidad_enviada' => 0,
            'error' => $error,
        ]);

        return [
            'ok' => false,
            'cantidad_enviada' => 0,
            'error' => $error,
        ];
    }
}
