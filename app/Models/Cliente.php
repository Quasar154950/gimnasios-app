<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Nota;
use App\Models\Seguimiento;
use App\Models\Expediente;
use App\Models\User;
use App\Models\MensajeCliente;
use App\Models\Asistencia;
use App\Traits\RegistraActividad;

// Spatie
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Relaciones tipadas
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model implements HasMedia
{
    use RegistraActividad, InteractsWithMedia;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'fecha_vencimiento_cuota',
        'monto_cuota',
        'archivado',
        'user_id',
        'abogado_id',
        'dni',
        'fecha_nacimiento',
        'peso',
        'altura',
        'contacto_emergencia',

    ];

    /**
     * Miniaturas de archivos
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->queued();
    }

    public function getLogNombre()
    {
        return $this->nombre ?? 'Sin nombre';
    }

    public function traducirAtributo($campo, $valor)
    {
        if ($campo === 'archivado') {
            return $valor ? '📁 Cliente Archivado' : '👥 Cliente Activo';
        }

        return $valor;
    }

    // RELACIONES

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class);
    }

    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class);
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeCliente::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

     public function rutinaAsignaciones(): HasMany
    {
        return $this->hasMany(RutinaAsignacion::class);
    }
}