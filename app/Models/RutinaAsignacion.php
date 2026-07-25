<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutinaAsignacion extends Model
{
    protected $table = 'rutina_asignaciones';

    protected $fillable = [
        'rutina_id',
        'cliente_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_revision',
        'activa',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio'   => 'date',
        'fecha_fin'      => 'date',
        'fecha_revision' => 'date',
        'activa'         => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function rutina(): BelongsTo
    {
        return $this->belongsTo(Rutina::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function completadas(): HasMany
    {
        return $this->hasMany(RutinaCompletada::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function estaVigente(): bool
    {
        if (! $this->activa) {
            return false;
        }

        if ($this->fecha_fin && now()->greaterThan($this->fecha_fin)) {
            return false;
        }

        return true;
    }
}
