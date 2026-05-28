<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login.estudio', [
                'slug' => session('slug_estudio', 'demo')
            ]);
        }

        if (auth()->user()->role !== $role) {

            if (auth()->user()->role === 'cliente') {
                return redirect()->route('cliente.dashboard');
            }

            if (auth()->user()->role === 'abogado') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('app.start');
        }

        return $next($request);
    }
}
