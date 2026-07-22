<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Novedad extends Model
{
    protected $fillable = [
        'abogado_id',
        'titulo',
        'descripcion',
        'tipo',
        'imagen',
        'fecha_publicacion',
        'activo',
        'destacado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'date',
            'activo' => 'boolean',
            'destacado' => 'boolean',
        ];
    }

    public function gimnasio(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'abogado_id',
        );
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
