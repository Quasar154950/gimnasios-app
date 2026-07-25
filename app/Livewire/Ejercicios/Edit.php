<?php

namespace App\Livewire\Ejercicios;

use App\Models\Ejercicio;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Ejercicio $ejercicio;

    public string $nombre = '';
    public string $grupo_muscular = '';
    public string $descripcion = '';
    public string $video_url = '';
    public bool $activo = true;

    public $imagen;

    public function mount(Ejercicio $ejercicio): void
    {
        abort_unless(
            $ejercicio->abogado_id === auth()->id(),
            403
        );

        $this->ejercicio = $ejercicio;

        $this->nombre = $ejercicio->nombre;
        $this->grupo_muscular = $ejercicio->grupo_muscular;
        $this->descripcion = $ejercicio->descripcion ?? '';
        $this->video_url = $ejercicio->video_url ?? '';
        $this->activo = $ejercicio->activo;
    }

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
        abort_unless(
            $this->ejercicio->abogado_id === auth()->id(),
            403
        );

        $this->validate();

        $this->ejercicio->update([
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

            $this->ejercicio
                ->addMedia($this->imagen->getRealPath())
                ->usingName($nombreSinExtension)
                ->usingFileName($nombreSeguro)
                ->toMediaCollection('imagen');
        }

        session()->flash(
            'mensaje',
            'Ejercicio actualizado correctamente.'
        );

        return redirect()->route('ejercicios.index');
    }

    public function render(): View
    {
        return view('livewire.ejercicios.edit', [
            'gruposMusculares' => Ejercicio::gruposMusculares(),
        ]);
    }
}
