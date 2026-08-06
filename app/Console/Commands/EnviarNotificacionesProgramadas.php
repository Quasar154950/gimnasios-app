<?php

namespace App\Console\Commands;

use App\Models\NotificacionPush;
use App\Services\NotificacionPushService;
use Illuminate\Console\Command;

class EnviarNotificacionesProgramadas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'push:enviar-programadas';

    /**
     * The console command description.
     */
    protected $description = 'Envía las notificaciones push programadas pendientes.';

    /**
     * Execute the console command.
     */
    public function handle(
        NotificacionPushService $notificacionPushService
    ): int {

        $pendientes = NotificacionPush::query()
            ->where('estado', 'pendiente')
            ->whereNotNull('programada_para')
            ->where('programada_para', '<=', now())
            ->orderBy('programada_para')
            ->get();

        if ($pendientes->isEmpty()) {

            $this->info(
                'No hay notificaciones programadas pendientes.'
            );

            return self::SUCCESS;
        }

        foreach ($pendientes as $notificacion) {

            $this->line(
                "Enviando #{$notificacion->id}..."
            );

            $notificacionPushService->enviar(
                $notificacion
            );

        }

        $this->info(
            "{$pendientes->count()} notificaciones procesadas."
        );

        return self::SUCCESS;
    }
}
