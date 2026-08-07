<?php

namespace App\Livewire\NotificacionesPush;

use App\Models\NotificacionPush;
use Livewire\Component;

class Show extends Component
{
    public NotificacionPush $notificacion;

    public function mount(NotificacionPush $notificacion): void
    {
        abort_unless(
            $notificacion->user_id === auth()->id(),
            403
        );

        $this->notificacion = $notificacion->load('cliente');
    }

    public function render()
    {
        return view(
            'livewire.notificaciones-push.show'
        );
    }
}
