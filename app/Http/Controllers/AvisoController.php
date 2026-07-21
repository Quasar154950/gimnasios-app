<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvisoController extends Controller
{
    public function index(): View
    {
        $avisos = Aviso::query()
            ->where('abogado_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('avisos.index', compact('avisos'));
    }

    public function create(): View
    {
        return view('avisos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
            'prioridad' => [
                'required',
                'string',
                'in:informativo,importante,urgente',
            ],
            'fecha_publicacion' => ['nullable', 'date'],
            'fecha_vencimiento' => [
                'nullable',
                'date',
                'after_or_equal:fecha_publicacion',
            ],
            'activo' => ['nullable', 'boolean'],
        ]);

        Aviso::create([
            'abogado_id' => auth()->id(),
            'titulo' => $datos['titulo'],
            'mensaje' => $datos['mensaje'],
            'prioridad' => $datos['prioridad'],
            'fecha_publicacion' => $datos['fecha_publicacion'] ?? null,
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('avisos.index')
            ->with('success', 'Aviso creado correctamente.');
    }

    public function edit(Aviso $aviso): View
    {
        $this->validarGimnasio($aviso);

        return view('avisos.edit', compact('aviso'));
    }

    public function update(
        Request $request,
        Aviso $aviso
    ): RedirectResponse {
        $this->validarGimnasio($aviso);

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
            'prioridad' => [
                'required',
                'string',
                'in:informativo,importante,urgente',
            ],
            'fecha_publicacion' => ['nullable', 'date'],
            'fecha_vencimiento' => [
                'nullable',
                'date',
                'after_or_equal:fecha_publicacion',
            ],
            'activo' => ['nullable', 'boolean'],
        ]);

        $aviso->update([
            'titulo' => $datos['titulo'],
            'mensaje' => $datos['mensaje'],
            'prioridad' => $datos['prioridad'],
            'fecha_publicacion' => $datos['fecha_publicacion'] ?? null,
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('avisos.index')
            ->with('success', 'Aviso actualizado correctamente.');
    }

    public function destroy(Aviso $aviso): RedirectResponse
    {
        $this->validarGimnasio($aviso);

        $aviso->delete();

        return redirect()
            ->route('avisos.index')
            ->with('success', 'Aviso eliminado correctamente.');
    }

    private function validarGimnasio(Aviso $aviso): void
    {
        abort_unless(
            $aviso->abogado_id === auth()->id(),
            403
        );
    }
}
