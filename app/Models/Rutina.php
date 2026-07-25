<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rutina extends Model
{
    protected $table = 'rutinas';

    protected $fillable = [
        'abogado_id',
        'nombre',
        'descripcion',
        'objetivo',
        'duracion_semanas',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function dias(): HasMany
    {
        return $this->hasMany(RutinaDia::class)
            ->orderBy('orden');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(RutinaAsignacion::class);
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
}
