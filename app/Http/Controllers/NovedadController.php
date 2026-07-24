<?php

namespace App\Http\Controllers;

use App\Models\Novedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NovedadController extends Controller
{
    /**
     * Listado de publicaciones.
     */
    public function index(): View
    {
        $novedades = Novedad::query()
            ->where('abogado_id', auth()->id())
            ->orderByDesc('destacado')
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get();

        return view('novedades.index', compact('novedades'));
    }

    /**
     * Formulario de nueva publicación.
     */
    public function create(): View
    {
        return view('novedades.create');
    }

    /**
     * Guardar nueva publicación.
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            'tipo' => [
                'required',
                'string',
                'in:novedad,promocion,evento,consejo',
            ],
            'imagen' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
            'destacado' => [
                'nullable',
                'boolean',
            ],
        ]);

        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            $rutaImagen = $request
                ->file('imagen')
                ->store('novedades', 'public');
        }

        Novedad::create([
            'abogado_id' => auth()->id(),
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'tipo' => $datos['tipo'],
            'imagen' => $rutaImagen,
            'fecha_publicacion' => now(),
            'activo' => $request->boolean('activo'),
            'destacado' => $request->boolean('destacado'),
        ]);

        return redirect()
            ->route('novedades.index')
            ->with('success', 'Publicación creada correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Novedad $novedad): View
    {
        $this->validarPropietario($novedad);

        return view('novedades.edit', compact('novedad'));
    }

    /**
     * Actualizar publicación.
     */
    public function update(
        Request $request,
        Novedad $novedad
    ): RedirectResponse {
        $this->validarPropietario($novedad);

        $datos = $request->validate([
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            'tipo' => [
                'required',
                'string',
                'in:novedad,promocion,evento,consejo',
            ],
            'imagen' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'eliminar_imagen' => [
                'nullable',
                'boolean',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
            'destacado' => [
                'nullable',
                'boolean',
            ],
        ]);

        $rutaImagen = $novedad->imagen;

        /*
        |--------------------------------------------------------------------------
        | ELIMINAR IMAGEN ACTUAL
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('eliminar_imagen')) {
            $this->eliminarImagen($novedad->imagen);

            $rutaImagen = null;
        }

        /*
        |--------------------------------------------------------------------------
        | REEMPLAZAR O AGREGAR IMAGEN
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('imagen')) {
            $this->eliminarImagen($novedad->imagen);

            $rutaImagen = $request
                ->file('imagen')
                ->store('novedades', 'public');
        }

        $novedad->update([
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'tipo' => $datos['tipo'],
            'imagen' => $rutaImagen,
            'activo' => $request->boolean('activo'),
            'destacado' => $request->boolean('destacado'),
        ]);

        return redirect()
            ->route('novedades.index')
            ->with('success', 'Publicación actualizada correctamente.');
    }

    /**
     * Eliminar publicación.
     */
    public function destroy(Novedad $novedad): RedirectResponse
    {
        $this->validarPropietario($novedad);

        $this->eliminarImagen($novedad->imagen);

        $novedad->delete();

        return redirect()
            ->route('novedades.index')
            ->with('success', 'Publicación eliminada correctamente.');
    }

    /**
     * Elimina una imagen del disco público.
     */
    private function eliminarImagen(?string $rutaImagen): void
    {
        if (
            $rutaImagen &&
            Storage::disk('public')->exists($rutaImagen)
        ) {
            Storage::disk('public')->delete($rutaImagen);
        }
    }

    /**
     * Evita modificar publicaciones de otro gimnasio.
     */
    private function validarPropietario(Novedad $novedad): void
    {
        abort_unless(
            (int) $novedad->abogado_id === (int) auth()->id(),
            403
        );
    }
}
