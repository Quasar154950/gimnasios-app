<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ejercicio extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'ejercicios';

    protected $fillable = [
        'abogado_id',
        'nombre',
        'grupo_muscular',
        'descripcion',
        'instrucciones',
        'video_url',
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

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function rutinaEjercicios(): HasMany
    {
        return $this->hasMany(RutinaEjercicio::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA LIBRARY
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('imagen')
            ->singleFile();
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
    | CATÁLOGO DE GRUPOS MUSCULARES
    |--------------------------------------------------------------------------
    */

    public static function gruposMusculares(): array
    {
        return [
            'Pecho',
            'Espalda',
            'Hombros',
            'Bíceps',
            'Tríceps',
            'Piernas',
            'Glúteos',
            'Abdominales',
            'Cardio',
            'Movilidad',
            'Cuerpo completo',
        ];
    }
}
