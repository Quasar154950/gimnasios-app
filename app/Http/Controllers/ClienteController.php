<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Etiqueta;
use App\Models\Pago;
use App\Models\User;
use App\Models\Asistencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::where('abogado_id', auth()->id())
            ->where('archivado', false)
            ->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function archivados()
    {
        $clientes = Cliente::where('abogado_id', auth()->id())
            ->where('archivado', true)
            ->paginate(10);

        return view('clientes.archivados', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'dni' => 'required|string|max:30',
        'fecha_nacimiento' => 'nullable|date',
        'peso' => 'nullable|numeric|min:0|max:500',
        'altura' => 'nullable|integer|min:30|max:300',
        'contacto_emergencia' => 'nullable|string|max:255',
        'telefono' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:clientes,email',
        'direccion' => 'required|string|max:255',
        'fecha_vencimiento_cuota' => 'nullable|date',
        'monto_cuota' => 'required|numeric|min:0',
    ], [
        'nombre.required' => 'El nombre es obligatorio.',
        'dni.required' => 'El DNI es obligatorio.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'El email no es válido.',
        'email.unique' => 'El email ya está registrado.',
        'direccion.required' => 'La dirección es obligatoria.',
    ]);

    Cliente::create([
        'nombre' => $request->nombre,
        'dni' => $request->dni,
        'telefono' => $request->telefono,
        'email' => $request->email,
        'direccion' => $request->direccion,
        'fecha_vencimiento_cuota' => $request->fecha_vencimiento_cuota,
        'monto_cuota' => $request->monto_cuota,
        'archivado' => false,
        'abogado_id' => auth()->id(),
        'fecha_nacimiento' => $request->fecha_nacimiento,
        'peso' => $request->peso,
        'altura' => $request->altura,
        'contacto_emergencia' => $request->contacto_emergencia,

    ]);

    return redirect()->route('clientes.index')->with('success', 'Socio creado correctamente.');
}

    public function show(Request $request, string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->with([
                'user',
                'notas',
                'seguimientos.etiqueta',
            ])
            ->findOrFail($id);

        $etiquetas = Etiqueta::all();
        $usuarios = User::where('role', 'cliente')->get();

        $estadoFiltro = $request->query('estado');
        $hoy = Carbon::today();

        $stats = [
            'todos' => $cliente->seguimientos->count(),
            'pendiente' => $cliente->seguimientos->where('estado', 'pendiente')->count(),
            'en_curso' => $cliente->seguimientos->where('estado', 'en_curso')->count(),
            'resuelto' => $cliente->seguimientos->where('estado', 'resuelto')->count(),
        ];

        $seguimientosFiltrados = $cliente->seguimientos
            ->when($estadoFiltro, function ($collection) use ($estadoFiltro) {
                return $collection->where('estado', $estadoFiltro);
            })
            ->sortBy(function ($s) use ($hoy) {

                $prioEstado = ($s->estado === 'resuelto') ? 1 : 0;

                if ($s->fecha_recordatorio) {

                    $fecha = Carbon::parse($s->fecha_recordatorio)->startOfDay();

                    if ($fecha->lt($hoy)) {
                        $prioFecha = 0;

                    } elseif ($fecha->isSameDay($hoy)) {
                        $prioFecha = 1;

                    } else {
                        $prioFecha = 2;
                    }

                } else {
                    $prioFecha = 3;
                }

                $prioImportancia = match ($s->prioridad) {
                    'alta' => 0,
                    'media' => 1,
                    'baja' => 2,
                    default => 1,
                };

                return [
                    $prioEstado,
                    $prioFecha,
                    $prioImportancia,
                    -$s->created_at->timestamp
                ];
            });

        return view('clientes.show', compact(
            'cliente',
            'seguimientosFiltrados',
            'stats',
            'estadoFiltro',
            'etiquetas',
            'usuarios'
        ));
    }

    public function asignarUsuario(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        $cliente->update([
            'user_id' => $request->user_id,
        ]);

        return back()->with('success', 'Usuario asignado correctamente.');
    }

    public function crearAcceso(Request $request, string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        if ($cliente->user_id) {
            return back()->with('error', 'Este cliente ya tiene un acceso creado.');
        }

        $request->validate([
            'password_acceso' => 'required|string|min:6|confirmed',
        ], [

            'password_acceso.required' => 'La contraseña es obligatoria.',
            'password_acceso.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password_acceso.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

if (! $cliente->email) {
    return back()->with(
        'error',
        'El socio debe tener un email cargado antes de crear el acceso.'
    );
}

if (User::where('email', $cliente->email)->exists()) {
    return back()->with(
        'error',
        'Ese email ya está siendo utilizado por otro usuario.'
    );
}

$user = User::create([
    'name' => $cliente->nombre,
    'email' => $cliente->email,
    'password' => Hash::make($request->password_acceso),
    'role' => 'cliente',
]);

        $cliente->update([
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Acceso del cliente creado y vinculado correctamente.');
    }

    public function resetPassword(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->with('user')
            ->findOrFail($id);

        if (!$cliente->user) {
            return back()->with('error', 'Este cliente no tiene un usuario asociado.');
        }

        $nuevaPassword = Str::password(10);

        $cliente->user->update([
            'password' => Hash::make($nuevaPassword),
        ]);

        return back()
            ->with('success', 'Contraseña restablecida correctamente.')
            ->with('nueva_password', $nuevaPassword);
    }

    public function quitarAcceso(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->with('user')
            ->findOrFail($id);

        if (!$cliente->user) {
            return back()->with('error', 'Este cliente no tiene acceso vinculado.');
        }

        $user = $cliente->user;

        $cliente->update([
            'user_id' => null,
        ]);

        $user->delete();

        return back()->with('success', 'Acceso del cliente eliminado correctamente.');
    }

    public function storeSeguimiento(Request $request, string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        $request->validate([
            'descripcion' => 'required|string',
            'etiqueta_id' => 'nullable|exists:etiquetas,id',
        ]);

        $cliente->seguimientos()->create([
            'descripcion' => $request->descripcion,
            'etiqueta_id' => $request->etiqueta_id,
            'user_id' => auth()->id(),
            'estado' => 'pendiente',
            'prioridad' => $request->prioridad ?? 'media',
            'fecha_recordatorio' => $request->fecha_recordatorio,
        ]);

        return back()->with('success', 'Seguimiento registrado con éxito.');
    }

    public function edit(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, string $id)
{
    $cliente = Cliente::where('abogado_id', auth()->id())
        ->with('user')
        ->findOrFail($id);

    $request->validate([
        'nombre' => 'required|string|max:255',
        'dni' => 'required|string|max:30',
        'telefono' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('clientes', 'email')->ignore($cliente->id),
            Rule::unique('users', 'email')->ignore($cliente->user_id),
        ],
        'direccion' => 'required|string|max:255',
        'fecha_vencimiento_cuota' => 'nullable|date',
        'monto_cuota' => 'required|numeric|min:0',
        'fecha_nacimiento' => 'nullable|date',
        'peso' => 'nullable|numeric|min:0|max:500',
        'altura' => 'nullable|integer|min:30|max:300',
        'contacto_emergencia' => 'nullable|string|max:255',


    ], [
        'nombre.required' => 'El nombre es obligatorio.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'El email no es válido.',
        'email.unique' => 'El email ya está registrado.',
        'direccion.required' => 'La dirección es obligatoria.',
    ]);

    $cliente->update([
        'nombre' => $request->nombre,
        'dni' => $request->dni ?? $cliente->dni,
        'telefono' => $request->telefono,
        'email' => $request->email,
        'direccion' => $request->direccion,
        'fecha_vencimiento_cuota' =>
            $request->fecha_vencimiento_cuota,
        'monto_cuota' => $request->monto_cuota,
        'fecha_nacimiento' => $request->fecha_nacimiento,
        'peso' => $request->peso,
        'altura' => $request->altura,
        'contacto_emergencia' => $request->contacto_emergencia,

    ]);

    if ($cliente->user) {
        $cliente->user->update([
            'name' => $request->nombre,
            'email' => $request->email,
        ]);
    }

    return redirect()
        ->route('clientes.index')
        ->with('success', 'Socio actualizado correctamente.');
}

    public function destroy(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Socio eliminado correctamente.');
    }

    public function archivar(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        $cliente->update([
            'archivado' => true
        ]);

        return redirect()->route('clientes.index')->with('success', 'Socio archivado correctamente.');
    }

    public function desarchivar(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())->findOrFail($id);

        $cliente->update([
            'archivado' => false
        ]);

        return redirect()->route('clientes.archivados')->with('success', 'Socio restaurado correctamente.');
    }

    public function pagos(string $id)
    {
        $cliente = Cliente::where('abogado_id', auth()->id())
            ->findOrFail($id);

        $pagos = Pago::where('cliente_id', $cliente->id)
            ->latest()
            ->get();

        return view('clientes.pagos', compact(
            'cliente',
            'pagos'
        ));
    }
    public function entrenamientos(string $id)
{
    $cliente = Cliente::where('abogado_id', auth()->id())
        ->findOrFail($id);

    $asistencias = Asistencia::where('cliente_id', $cliente->id)
        ->orderByDesc('created_at')
        ->get();

    return view('clientes.entrenamientos', compact(
        'cliente',
        'asistencias'
    ));
}

}