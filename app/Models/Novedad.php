<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Spatie
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Novedad extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'novedades';

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

    /**
     * Miniatura para futuras vistas.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->queued();
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
