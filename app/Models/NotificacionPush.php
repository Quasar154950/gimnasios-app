<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacionPush extends Model
{
    protected $table = 'notificaciones_push';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'titulo',
        'mensaje',
        'tipo',
        'destinatario',
        'estado',
        'programada_para',
        'enviada_at',
        'cantidad_enviada',
        'error',
    ];

    protected $casts = [
        'programada_para' => 'datetime',
        'enviada_at' => 'datetime',
        'cantidad_enviada' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
