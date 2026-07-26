<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileRutinaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->rutina->nombre,
            'descripcion' => $this->rutina->descripcion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'observaciones' => $this->observaciones,

            'dias' => $this->rutina->dias
                ->where('activo', true)
                ->sortBy('orden')
                ->values()
                ->map(function ($dia) {

                    return [
                        'id' => $dia->id,
                        'nombre' => $dia->nombre,
                        'descripcion' => $dia->descripcion,

                        'ejercicios' => $dia->ejercicios
                            ->where('activo', true)
                            ->values()
                            ->map(function ($ejercicio) {

                                return [
                                    'id' => $ejercicio->id,
                                    'nombre' => $ejercicio->nombre_ejercicio,
                                    'series' => $ejercicio->series,
                                    'repeticiones' => $ejercicio->repeticiones,
                                    'peso' => $ejercicio->peso,
                                    'descanso_segundos' => $ejercicio->descanso_segundos,
                                    'observaciones' => $ejercicio->observaciones,
                                ];
                            }),
                    ];
                }),
        ];
    }
}
