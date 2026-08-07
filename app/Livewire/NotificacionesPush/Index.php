<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\NotificacionPush;
use App\Services\NotificacionPushService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

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

    /*
    |--------------------------------------------------------------------------
    | ENVIAR AHORA
    |--------------------------------------------------------------------------
    |
    | Permite adelantar una notificación que todavía se encuentra pendiente,
    | aunque originalmente haya sido programada para una fecha futura.
    |
    */

    public function enviarAhora(
        int $notificacionId,
        NotificacionPushService $notificacionPushService
    ): void {
        $notificacion = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificacionId);

        if ($notificacion->estado !== 'pendiente') {
            session()->flash(
                'error',
                'Solo se pueden enviar ahora las notificaciones pendientes.'
            );

            return;
        }

        $programadaParaOriginal = $notificacion->programada_para;

        /*
        | El servicio puede impedir el envío anticipado si la notificación
        | conserva una fecha futura. La quitamos antes de procesarla.
        */

        $notificacion->forceFill([
            'programada_para' => null,
        ])->save();

        $resultado = $notificacionPushService->enviar(
            $notificacion
        );

        if ($resultado['ok'] ?? false) {
            $cantidadEnviada = (int) (
                $resultado['cantidad_enviada'] ?? 0
            );

            session()->flash(
                'success',
                $cantidadEnviada === 1
                    ? 'La notificación fue enviada ahora a 1 dispositivo.'
                    : "La notificación fue enviada ahora a {$cantidadEnviada} dispositivos."
            );

            return;
        }

        /*
        | Si el envío falla, recuperamos la programación original para que
        | pueda volver a intentarse automáticamente mediante Railway.
        */

        if ($programadaParaOriginal !== null) {
            $notificacion->forceFill([
                'programada_para' => $programadaParaOriginal,
            ])->save();
        }

        session()->flash(
            'error',
            $resultado['error']
                ?? 'La notificación no pudo enviarse.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REENVIAR
    |--------------------------------------------------------------------------
    |
    | Permite volver a enviar una notificación que ya fue procesada.
    |
    */

    public function reenviar(
        int $notificacionId,
        NotificacionPushService $notificacionPushService
    ): void {
        $notificacion = NotificacionPush::query()
            ->where('user_id', auth()->id())
            ->findOrFail($notificacionId);

        if (! in_array(
            $notificacion->estado,
            ['enviada', 'error'],
            true
        )) {
            session()->flash(
                'error',
                'Esta notificación todavía no puede reenviarse.'
            );

            return;
        }

        $programadaParaOriginal = $notificacion->programada_para;

        /*
        | El servicio central evita duplicar una notificación programada que
        | ya fue enviada. Para el reenvío manual quitamos temporalmente la
        | fecha programada.
        */

        if ($programadaParaOriginal !== null) {
            $notificacion->forceFill([
                'programada_para' => null,
            ])->save();
        }

        $resultado = $notificacionPushService->enviar(
            $notificacion
        );

        /*
        | Conservamos la fecha histórica que tenía la notificación.
        */

        if ($programadaParaOriginal !== null) {
            $notificacion->forceFill([
                'programada_para' => $programadaParaOriginal,
            ])->save();
        }

        if ($resultado['ok'] ?? false) {
            $cantidadEnviada = (int) (
                $resultado['cantidad_enviada'] ?? 0
            );

            session()->flash(
                'success',
                $cantidadEnviada === 1
                    ? 'La notificación fue reenviada correctamente a 1 dispositivo.'
                    : "La notificación fue reenviada correctamente a {$cantidadEnviada} dispositivos."
            );

            return;
        }

        session()->flash(
            'error',
            $resultado['error']
                ?? 'La notificación no pudo reenviarse.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

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
