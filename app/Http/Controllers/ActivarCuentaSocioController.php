<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ActivarCuentaSocioController extends Controller
{
    public function show()
    {
        return view('auth.activar-cuenta-socio');
    }

    public function activar(Request $request)
{
    $request->validate([
        'identificador' => 'required|string|max:255',
        'password' => 'required|string|min:6|confirmed',
    ], [
        'identificador.required' => 'Ingresá tu DNI o email.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'La confirmación de contraseña no coincide.',
    ]);

    $identificador = trim($request->identificador);

    $slug = session('slug_estudio');

    $gimnasio = User::where('slug_estudio', $slug)->first();

    if (!$gimnasio) {
        return redirect()->route('login.estudio', ['slug' => 'sportgym'])
            ->with('error', 'No pudimos identificar el gimnasio. Volvé a ingresar desde el acceso del gimnasio.');
    }

    $cliente = Cliente::where('abogado_id', $gimnasio->id)
        ->where(function ($query) use ($identificador) {
            $query->where('dni', $identificador)
                  ->orWhere('email', $identificador);
        })
        ->where('archivado', false)
        ->first();

    if (!$cliente) {
        return back()
            ->withInput()
            ->with('error', 'No encontramos un socio activo con ese DNI o email en este gimnasio.');
    }

    if ($cliente->user_id) {
        return back()
            ->with('error', 'Este socio ya tiene una cuenta activada. Usá recuperar contraseña si no recordás el acceso.');
    }

    if (!$cliente->email) {
        return back()
            ->with('error', 'Este socio no tiene email cargado. Contactá al gimnasio.');
    }

    if (User::where('email', $cliente->email)->exists()) {
        return back()
            ->with('error', 'Ya existe un usuario con ese email. Contactá al gimnasio.');
    }

    $user = User::create([
        'name' => $cliente->nombre,
        'email' => $cliente->email,
        'password' => Hash::make($request->password),
        'role' => 'cliente',
        'activo' => true,
    ]);

    $cliente->update([
        'user_id' => $user->id,
    ]);

    auth()->login($user);

    return redirect()->route('cliente.dashboard')
        ->with('success', 'Cuenta activada correctamente.');
}
}
