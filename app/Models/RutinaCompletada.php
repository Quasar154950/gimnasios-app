<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutinaCompletada extends Model
{
    protected $table = 'rutina_completadas';

    protected $fillable = [
        'rutina_asignacion_id',
        'rutina_dia_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(
            RutinaAsignacion::class,
            'rutina_asignacion_id'
        );
    }

    public function dia(): BelongsTo
    {
        return $this->belongsTo(
            RutinaDia::class,
            'rutina_dia_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getFinalizadaAttribute(): bool
    {
        return ! empty($this->hora_fin);
    }
}
