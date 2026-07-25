<?php

namespace App\Livewire\Ejercicios;

use App\Models\Ejercicio;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public string $nombre = '';
    public string $grupo_muscular = '';
    public string $descripcion = '';
    public string $video_url = '';
    public bool $activo = true;

    public $imagen;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'grupo_muscular' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'activo' => 'boolean',

            'imagen' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El nombre del ejercicio es obligatorio.',

            'grupo_muscular.required' =>
                'Seleccioná un grupo muscular.',

            'video_url.url' =>
                'El enlace del video debe ser una URL válida.',

            'imagen.image' =>
                'El archivo seleccionado debe ser una imagen.',

            'imagen.mimes' =>
                'La imagen debe ser JPG, JPEG, PNG o WEBP.',

            'imagen.max' =>
                'La imagen no puede superar los 5 MB.',
        ];
    }

    public function guardar()
    {
        try {
            $this->validate();

            $ejercicio = Ejercicio::create([
                'abogado_id' => auth()->id(),
                'nombre' => trim($this->nombre),
                'grupo_muscular' => $this->grupo_muscular,
                'descripcion' => $this->descripcion ?: null,
                'video_url' => $this->video_url ?: null,
                'activo' => $this->activo,
            ]);

            if ($this->imagen) {
                $nombreSinExtension = pathinfo(
                    $this->imagen->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $nombreSeguro = str($nombreSinExtension)
                    ->slug('-')
                    ->append('-' . uniqid())
                    ->toString();

                $ejercicio
                    ->addMedia($this->imagen->getRealPath())
                    ->usingName($nombreSinExtension)
                    ->usingFileName($nombreSeguro)
                    ->toMediaCollection('imagen');
            }

            session()->flash(
                'success',
                'Ejercicio creado correctamente.'
            );

            return redirect()->route('ejercicios.index');

        } catch (\Throwable $e) {
            $error = implode(PHP_EOL, [
                'FECHA: ' . now(),
                'ERROR: ' . $e->getMessage(),
                'ARCHIVO: ' . $e->getFile(),
                'LÍNEA: ' . $e->getLine(),
                '',
                'TRACE:',
                $e->getTraceAsString(),
            ]);

            file_put_contents(
                storage_path('error-ejercicio.txt'),
                $error
            );

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.ejercicios.create', [
            'gruposMusculares' => Ejercicio::gruposMusculares(),
        ]);
    }
}
