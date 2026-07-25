<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutinaEjercicio extends Model
{
    protected $table = 'rutina_ejercicios';

    protected $fillable = [
        'rutina_dia_id',
        'ejercicio_id',
        'ejercicio',
        'series',
        'repeticiones',
        'peso',
        'descanso_segundos',
        'observaciones',
        'orden',
        'activo',
    ];

    protected $casts = [
        'series' => 'integer',
        'repeticiones' => 'integer',
        'descanso_segundos' => 'integer',
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function dia(): BelongsTo
    {
        return $this->belongsTo(RutinaDia::class, 'rutina_dia_id');
    }

    public function ejercicioBiblioteca(): BelongsTo
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicio_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /*
    |--------------------------------------------------------------------------
    | NOMBRE PARA MOSTRAR
    |--------------------------------------------------------------------------
    */

    public function getNombreEjercicioAttribute(): string
    {
        return $this->ejercicioBiblioteca?->nombre
            ?? $this->ejercicio
            ?? 'Ejercicio sin nombre';
    }
}
