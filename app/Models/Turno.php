<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $fillable = [
        'actividad',
        'profesor',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'cupo_maximo',
        'activo',
        'abogado_id',
    ];

    public function reservas()
    {
        return $this->hasMany(ReservaTurno::class);
    }
}
