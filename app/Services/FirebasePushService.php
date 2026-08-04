<?php

namespace App\Services;

use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Throwable;

class FirebasePushService
{
    public function enviar(
        string $token,
        string $titulo,
        string $mensaje,
        array $data = []
    ): array {
        $dataComoTexto = [];

        foreach ($data as $clave => $valor) {
            $dataComoTexto[(string) $clave] = (string) $valor;
        }

        $notificacion = Notification::create(
            $titulo,
            $mensaje
        );

        $mensajeFirebase = CloudMessage::withTarget(
            'token',
            $token
        )
            ->withNotification($notificacion)
            ->withData($dataComoTexto)
            ->withDefaultSounds()
            ->withHighestPossiblePriority();

        try {
            $resultado = Firebase::messaging()->send(
                $mensajeFirebase
            );

            return [
                'ok' => true,
                'resultado' => $resultado,
            ];
        } catch (MessagingException $e) {
            return [
                'ok' => false,
                'error' => $e->getMessage(),
                'detalles' => $e->errors(),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'error' => $e->getMessage(),
                'detalles' => [],
            ];
        }
    }
}
