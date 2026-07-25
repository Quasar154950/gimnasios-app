<?php

namespace App\Support;

use App\Models\Cliente;
use App\Models\Ejercicio;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class EstudioPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        /*
        |--------------------------------------------------------------------------
        | EJERCICIOS DEL GIMNASIO
        |--------------------------------------------------------------------------
        |
        | Cada ejercicio tendrá su propia carpeta.
        | Esto evita que imágenes con el mismo nombre se sobrescriban.
        |
        */

        if ($media->model instanceof Ejercicio) {
            $ejercicio = $media->model;

            $gimnasioId = $ejercicio->abogado_id ?? 'general';
            $ejercicioId = $ejercicio->id ?? $media->model_id;

            return "gimnasios/{$gimnasioId}/ejercicios/{$ejercicioId}/";
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS DE CLIENTES
        |--------------------------------------------------------------------------
        */

        if ($media->model instanceof Cliente) {
            $cliente = $media->model;

            $slug = $cliente->abogado?->slug_estudio ?? 'general';

            return "estudios/{$slug}/documentos/{$cliente->id}/";
        }

        /*
        |--------------------------------------------------------------------------
        | OTROS ARCHIVOS
        |--------------------------------------------------------------------------
        |
        | Se separan por modelo e ID para evitar colisiones entre archivos.
        |
        */

        $modelo = class_basename($media->model_type);
        $modelo = strtolower($modelo);
        $modeloId = $media->model_id;

        return "general/{$modelo}/{$modeloId}/";
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive/';
    }
}
