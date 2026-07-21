<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    use HasFactory;

    protected $fillable = [
        'abogado_id',
        'titulo',
        'mensaje',
        'prioridad',
        'fecha_publicacion',
        'fecha_vencimiento',
        'activo',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'activo' => 'boolean',
    ];

    public function gimnasio()
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function scopeDelGimnasio(
        Builder $query,
        int $abogadoId
    ): Builder {
        return $query->where('abogado_id', $abogadoId);
    }

    public function scopeVisibles(Builder $query): Builder
    {
        return $query
            ->where('activo', true)
            ->where(function (Builder $query) {
                $query
                    ->whereNull('fecha_publicacion')
                    ->orWhere('fecha_publicacion', '<=', now());
            })
            ->where(function (Builder $query) {
                $query
                    ->whereNull('fecha_vencimiento')
                    ->orWhere('fecha_vencimiento', '>=', now());
            });
    }
}
