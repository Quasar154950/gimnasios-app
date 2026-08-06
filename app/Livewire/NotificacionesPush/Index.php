<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\DispositivoPush;
use App\Models\NotificacionPush;
use App\Services\FirebasePushService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Index extends Component
{
    use WithPagination;

    public string $buscar = '';

    public string $estado = 'todos';

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatingEstado(): void
    {
        $this->resetPage();
    }

    public function reenviar(int $notificacionId): void
    {
        $notificacion = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificacionId);

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
                        auth()->id()
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
            $notificacion->update([
                'estado' => 'error',
                'cantidad_enviada' => 0,
                'error' => 'No se encontraron dispositivos registrados para los destinatarios seleccionados.',
            ]);

            session()->flash(
                'error',
                'No hay celulares registrados para los destinatarios de esta notificación.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | REENVIAR PUSH
        |--------------------------------------------------------------------------
        */

        $firebasePushService = app(
            FirebasePushService::class
        );

        $cantidadEnviada = 0;
        $errores = [];

        foreach ($dispositivos as $dispositivo) {
            try {
                $resultado = $firebasePushService->enviar(
                    token: $dispositivo->token,
                    titulo: $notificacion->titulo,
                    mensaje: $notificacion->mensaje,
                    data: [
                        'tipo' => $notificacion->tipo,
                        'pantalla' => 'inicio',
                        'notificacion_id' => (string) $notificacion->id,
                    ],
                );

                if ($resultado['ok'] ?? false) {
                    $cantidadEnviada++;

                    continue;
                }

                $errores[] = $resultado['error']
                    ?? 'Firebase devolvió un error desconocido.';
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
            $notificacion->update([
                'estado' => 'enviada',
                'enviada_at' => now(),
                'cantidad_enviada' => $cantidadEnviada,
                'error' => $errores !== []
                    ? implode(
                        ' | ',
                        array_unique($errores)
                    )
                    : null,
            ]);

            session()->flash(
                'success',
                $cantidadEnviada === 1
                    ? 'La notificación fue reenviada correctamente a 1 dispositivo.'
                    : "La notificación fue reenviada correctamente a {$cantidadEnviada} dispositivos."
            );

            return;
        }

        $notificacion->update([
            'estado' => 'error',
            'cantidad_enviada' => 0,
            'error' => $errores !== []
                ? implode(
                    ' | ',
                    array_unique($errores)
                )
                : 'No se pudo reenviar la notificación.',
        ]);

        session()->flash(
            'error',
            'La notificación no pudo reenviarse.'
        );
    }

    public function eliminar(int $notificacionId): void
    {
        $notificacion = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificacionId);

        $notificacion->delete();

        session()->flash(
            'success',
            'La notificación fue eliminada correctamente.'
        );

        $this->resetPage();
    }

    public function render()
    {
        $notificaciones = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->with('cliente')
            ->when(
                $this->buscar !== '',
                function (Builder $query): void {
                    $query->where(
                        function (Builder $subquery): void {
                            $subquery
                                ->where(
                                    'titulo',
                                    'ilike',
                                    '%' . $this->buscar . '%'
                                )
                                ->orWhere(
                                    'mensaje',
                                    'ilike',
                                    '%' . $this->buscar . '%'
                                );
                        }
                    );
                }
            )
            ->when(
                $this->estado !== 'todos',
                fn (Builder $query) => $query->where(
                    'estado',
                    $this->estado
                )
            )
            ->latest()
            ->paginate(12);

        return view(
            'livewire.notificaciones-push.index',
            [
                'notificaciones' => $notificaciones,
            ]
        );
    }
}
