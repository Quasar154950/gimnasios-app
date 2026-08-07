<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\NotificacionPush;
use App\Services\NotificacionPushService;
use Illuminate\Console\Command;

class GenerarNotificacionesAutomaticas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'push:generar-automaticas';

    /**
     * The console command description.
     */
    protected $description = 'Genera y envía las notificaciones push automáticas.';

    /**
     * Execute the console command.
     */
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

        $this->info('Proceso de notificaciones automáticas finalizado.');

        return self::SUCCESS;
    }

    /**
     * Generar notificaciones automáticas de cumpleaños.
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
}
