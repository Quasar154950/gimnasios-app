<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutinaDia extends Model
{
    protected $table = 'rutina_dias';

    protected $fillable = [
        'rutina_id',
        'nombre',
        'descripcion',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
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

    public function ejercicios(): HasMany
    {
        return $this->hasMany(RutinaEjercicio::class)
            ->orderBy('orden');
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

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
