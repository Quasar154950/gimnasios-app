<?php

namespace App\Http\Controllers;

use App\Models\Novedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'tipo' => ['required', 'string', 'in:novedad,promocion,evento,consejo'],
        ]);

        Novedad::create([
            'abogado_id' => auth()->id(),
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'tipo' => $datos['tipo'],
            'fecha_publicacion' => now(),
            'activo' => true,
            'destacado' => false,
        ]);

        return redirect()
            ->route('novedades.index')
            ->with('success', 'Publicación creada correctamente.');
    }

    /**
     * Diagnóstico temporal de edición.
     */
    public function edit(Novedad $novedad)
    {
        dd([
            'novedad_id' => $novedad->id,
            'abogado_id_novedad' => $novedad->abogado_id,
            'usuario_actual_id' => auth()->id(),
            'usuario_actual_email' => auth()->user()?->email,
            'usuario_actual_role' => auth()->user()?->role,
            'usuario_actual_slug' => auth()->user()?->slug_estudio,
        ]);
    }

    /**
     * Actualizar publicación.
     */
    public function update(Request $request, Novedad $novedad): RedirectResponse
    {
        $this->validarPropietario($novedad);

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'tipo' => ['required', 'string', 'in:novedad,promocion,evento,consejo'],
        ]);

        $novedad->update([
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'tipo' => $datos['tipo'],
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

        $novedad->delete();

        return redirect()
            ->route('novedades.index')
            ->with('success', 'Publicación eliminada correctamente.');
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
