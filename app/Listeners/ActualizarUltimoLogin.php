<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class ActualizarUltimoLogin
{
    public function handle(Login $event): void
    {
        $event->user->update([
            'ultimo_login_at' => now(),
        ]);
    }
}
