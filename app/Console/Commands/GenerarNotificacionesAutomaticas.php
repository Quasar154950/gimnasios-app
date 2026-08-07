<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\NotificacionPush;
use App\Services\NotificacionPushService;
use Illuminate\Console\Command;

class GenerarNotificacionesAutomaticas extends Command
{
    protected $signature = 'push:generar-automaticas';

    protected $description = 'Genera y envía las notificaciones push automáticas.';

    public function handle(
        NotificacionPushService $notificacionPushService
    ): int {
        $this->info('Buscando notificaciones automáticas...');

        $this->generarCumpleanos(
            $notificacionPushService
        );

        $this->generarCuotasPorVencer(
            $notificacionPushService
        );

        $this->info(
            'Proceso de notificaciones automáticas finalizado.'
        );

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | CUMPLEAÑOS
    |--------------------------------------------------------------------------
    */

    private function generarCumpleanos(
        NotificacionPushService $notificacionPushService
    ): void {
        $hoy = now();

        $clientes = Cliente::query()
            ->where('archivado', false)
            ->whereNotNull('fecha_nacimiento')
            ->whereMonth(
                'fecha_nacimiento',
                $hoy->month
            )
            ->whereDay(
                'fecha_nacimiento',
                $hoy->day
            )
            ->get();

        if ($clientes->isEmpty()) {
            $this->line(
                '🎂 No hay cumpleaños para hoy.'
            );

            return;
        }

        foreach ($clientes as $cliente) {
            $claveAutomatica = sprintf(
                'cumpleanos:%d:%s',
                $cliente->id,
                $hoy->format('Y')
            );

            $yaExiste = NotificacionPush::query()
                ->where(
                    'clave_automatica',
                    $claveAutomatica
                )
                ->exists();

            if ($yaExiste) {
                $this->line(
                    "🎂 Cumpleaños de {$cliente->nombre} ya procesado."
                );

                continue;
            }

            $notificacion = NotificacionPush::create([
                'user_id' => $cliente->abogado_id,
                'cliente_id' => $cliente->id,
                'titulo' => '🎂 ¡Feliz cumpleaños!',
                'mensaje' => "¡Feliz cumpleaños, {$cliente->nombre}! 🎉 Te deseamos un excelente día.",
                'tipo' => 'automatica',
                'clave_automatica' => $claveAutomatica,
                'destinatario' => 'cliente',
                'pantalla' => 'inicio',
                'estado' => 'pendiente',
                'programada_para' => null,
                'cantidad_enviada' => 0,
            ]);

            $resultado = $notificacionPushService->enviar(
                $notificacion
            );

            if ($resultado['ok'] ?? false) {
                $this->info(
                    "🎂 Cumpleaños enviado a {$cliente->nombre}."
                );

                continue;
            }

            $this->error(
                "🎂 No se pudo enviar el cumpleaños a {$cliente->nombre}: "
                . ($resultado['error'] ?? 'Error desconocido.')
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CUOTA POR VENCER
    |--------------------------------------------------------------------------
    */

    private function generarCuotasPorVencer(
        NotificacionPushService $notificacionPushService
    ): void {
        $fechaObjetivo = now()
            ->addDays(3)
            ->toDateString();

        $clientes = Cliente::query()
            ->where('archivado', false)
            ->whereNotNull('fecha_vencimiento_cuota')
            ->whereDate(
                'fecha_vencimiento_cuota',
                $fechaObjetivo
            )
            ->get();

        if ($clientes->isEmpty()) {
            $this->line(
                '💳 No hay cuotas que venzan dentro de 3 días.'
            );

            return;
        }

        foreach ($clientes as $cliente) {
            $claveAutomatica = sprintf(
                'cuota_por_vencer:%d:%s',
                $cliente->id,
                $cliente->fecha_vencimiento_cuota
                    ->format('Y-m-d')
            );

            $yaExiste = NotificacionPush::query()
                ->where(
                    'clave_automatica',
                    $claveAutomatica
                )
                ->exists();

            if ($yaExiste) {
                $this->line(
                    "💳 Aviso de cuota de {$cliente->nombre} ya procesado."
                );

                continue;
            }

            $fechaVencimiento = $cliente
                ->fecha_vencimiento_cuota
                ->format('d/m/Y');

            $notificacion = NotificacionPush::create([
                'user_id' => $cliente->abogado_id,
                'cliente_id' => $cliente->id,
                'titulo' => '💳 Tu cuota vence pronto',
                'mensaje' => "Hola {$cliente->nombre}, tu cuota vence el {$fechaVencimiento}. Podés abonarla desde Mi cuota.",
                'tipo' => 'automatica',
                'clave_automatica' => $claveAutomatica,
                'destinatario' => 'cliente',
                'pantalla' => 'cuota',
                'estado' => 'pendiente',
                'programada_para' => null,
                'cantidad_enviada' => 0,
            ]);

            $resultado = $notificacionPushService->enviar(
                $notificacion
            );

            if ($resultado['ok'] ?? false) {
                $this->info(
                    "💳 Aviso de cuota enviado a {$cliente->nombre}."
                );

                continue;
            }

            $this->error(
                "💳 No se pudo enviar el aviso de cuota a {$cliente->nombre}: "
                . ($resultado['error'] ?? 'Error desconocido.')
            );
        }
    }
}
