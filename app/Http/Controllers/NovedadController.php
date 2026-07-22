<?php

namespace App\Http\Controllers;

use App\Models\Novedad;
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
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'tipo' => ['required', 'string'],
        ]);

        Novedad::create([
            'abogado_id' => auth()->id(),
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'fecha_publicacion' => now(),
            'activo' => true,
            'destacado' => false,
        ]);

        return redirect()
            ->route('novedades.index')
            ->with('success', 'Publicación creada correctamente.');
    }
}
