<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispositivoPush extends Model
{
    protected $table = 'dispositivos_push';

    protected $fillable = [
        'user_id',
        'token',
        'plataforma',
        'modelo',
        'ultimo_uso_at',
    ];

    protected $casts = [
        'ultimo_uso_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
