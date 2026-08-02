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
        $fechaInicio = $this->fecha_inicio;
        $fechaFin = $this->fecha_fin;
        $fechaRevision = $this->fecha_revision;

        $totalSemanas = null;
        $semanaActual = null;

        $diasActivos = $this->rutina->dias
            ->where('activo', true);

        $diasEntrenamiento = $diasActivos
            ->filter(function ($dia) {
                return $dia->ejercicios
                    ->where('activo', true)
                    ->isNotEmpty();
            })
            ->count();

        $diasDescanso = max(
            0,
            $diasActivos->count() - $diasEntrenamiento
        );

        if ($fechaInicio && $fechaFin) {
            $diasTotales = $fechaInicio->diffInDays($fechaFin) + 1;

            $totalSemanas = (int) ceil($diasTotales / 7);

            if (now()->greaterThanOrEqualTo($fechaInicio)) {
                $diasTranscurridos = $fechaInicio->diffInDays(now());

                $semanaActual = min(
                    $totalSemanas,
                    (int) floor($diasTranscurridos / 7) + 1
                );
            }
        }

        return [
            'id' => $this->id,
            'nombre' => $this->rutina->nombre,
            'descripcion' => $this->rutina->descripcion,

            'fecha_inicio' => $fechaInicio?->format('Y-m-d'),
            'fecha_fin' => $fechaFin?->format('Y-m-d'),
            'fecha_revision' => $fechaRevision?->format('Y-m-d'),

            'total_semanas' => $totalSemanas,
            'semana_actual' => $semanaActual,

            'dias_entrenamiento' => $diasEntrenamiento,
            'dias_descanso' => $diasDescanso,

            'observaciones' => $this->observaciones,

            'dias' => $diasActivos
                ->sortBy('orden')
                ->values()
                ->map(function ($dia) {
                    return [
                        'id' => $dia->id,
                        'nombre' => $dia->nombre,
                        'descripcion' => $dia->descripcion,

                        'ejercicios' => $dia->ejercicios
                            ->where('activo', true)
                            ->sortBy('orden')
                            ->values()
                            ->map(function ($ejercicio) {
                                $ejercicioBiblioteca =
                                    $ejercicio->ejercicioBiblioteca;

                                return [
                                    'id' => $ejercicio->id,

                                    'nombre' =>
                                        $ejercicio->nombre_ejercicio,

                                    'series' =>
                                        $ejercicio->series,

                                    'repeticiones' =>
                                        $ejercicio->repeticiones,

                                    'peso' =>
                                        $ejercicio->peso,

                                    'descanso_segundos' =>
                                        $ejercicio->descanso_segundos,

                                    'observaciones' =>
                                        $ejercicio->observaciones,

                                    'grupo_muscular' =>
                                        $ejercicioBiblioteca
                                            ?->grupo_muscular,

                                    'descripcion' =>
                                        $ejercicioBiblioteca
                                            ?->descripcion,

                                    'instrucciones' =>
                                        $ejercicioBiblioteca
                                            ?->instrucciones,

                                    'video_url' =>
                                        $ejercicioBiblioteca
                                            ?->video_url,

                                    'imagen_url' =>
                                        $ejercicioBiblioteca
                                            ?->getFirstMediaUrl('imagen')
                                        ?: null,
                                ];
                            }),
                    ];
                }),
        ];
    }
}
