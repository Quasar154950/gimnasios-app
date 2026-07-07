<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Gimnasio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#071015] text-white">

@php
    $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

    $abogado = $cliente?->abogado;
    $esDemoGym = ($abogado?->slug_estudio ?? null) === 'demo';

    $logoSocio = $esDemoGym
        ? asset('images/logo-demogym.png')
        : asset('images/logo-sportgym.png');

    $nombreGym = $esDemoGym ? 'DemoGym' : 'SportGym';

    $presentesAhora = \App\Models\Asistencia::where('presente', true)
        ->whereNull('hora_salida')
        ->count();
    $misReservas = collect();
$proximaReserva = null;
$estadoReservaTexto = 'Sin reservas activas';
$estadoReservaColor = 'text-zinc-500';

if ($cliente) {
    $misReservas = \App\Models\ReservaTurno::with('turno')
        ->where('cliente_id', $cliente->id)
        ->whereHas('turno', function ($query) {
            $query->where(function ($q) {
                $q->whereDate('fecha', '>', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('fecha', now()->toDateString())
                         ->whereTime('hora_fin', '>=', now()->format('H:i:s'));
                  });
            });
        })
        ->get()
        ->sortBy(fn($r) => $r->turno->fecha . ' ' . $r->turno->hora_inicio);

   $proximaReserva = $misReservas->first();
}

if ($proximaReserva && $proximaReserva->turno) {
    $inicio = \Carbon\Carbon::parse($proximaReserva->turno->fecha . ' ' . $proximaReserva->turno->hora_inicio);
    $fin = \Carbon\Carbon::parse($proximaReserva->turno->fecha . ' ' . $proximaReserva->turno->hora_fin);
    $ahora = now();

    if ($ahora->between($inicio, $fin)) {
        $estadoReservaTexto = '🔴 Actividad en curso';
        $estadoReservaColor = 'text-red-600';
    } elseif ($inicio->isToday()) {
        $minutos = $ahora->diffInMinutes($inicio, false);

        if ($minutos <= 60 && $minutos > 0) {
            $estadoReservaTexto = '🟠 Empieza en ' . $minutos . ' min';
            $estadoReservaColor = 'text-orange-600';
        } else {
            $estadoReservaTexto = '🟢 Hoy tenés actividad';
            $estadoReservaColor = 'text-green-600';
        }
    } else {
        $estadoReservaTexto = '📌 Próxima reserva';
        $estadoReservaColor = 'text-orange-600';
    }
}

@endphp

<div class="min-h-screen max-w-md mx-auto bg-[#071015] pb-28">

    {{-- HEADER --}}
    <div class="px-6 pt-8 pb-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-orange-400 font-semibold">
                    Hola 👋
                </p>

                <h1 class="text-2xl font-black leading-tight">
                    {{ $cliente ? $cliente->nombre : auth()->user()->name }}
                </h1>

                <p class="text-sm text-zinc-400 mt-1">
                    {{ $nombreGym }} Tandil
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('cliente.mensajes') }}"
                   class="h-11 w-11 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
                    🔔
                </a>

                <img src="{{ $logoSocio }}"
                     alt="{{ $nombreGym }}"
                     class="h-14 w-14 rounded-2xl object-contain bg-white p-1">
            </div>
        </div>
    </div>

    {{-- TARJETA PRINCIPAL --}}
    <div class="px-6">
        <div class="rounded-[2rem] bg-gradient-to-br from-orange-500 to-orange-700 p-5 shadow-2xl">
            <p class="text-sm text-orange-100">
                Gimnasio en tiempo real
            </p>

            <h2 class="text-3xl font-black mt-1">
                {{ $presentesAhora }} socios
            </h2>

            <p class="text-sm text-orange-100 mt-1">
                entrenando ahora
            </p>

            <div class="mt-5 flex items-center justify-between">
                <a href="{{ route('cliente.mi-qr') }}"
                   class="rounded-2xl bg-black/80 px-5 py-3 text-sm font-bold">
                    Mostrar QR
                </a>

                <span class="text-4xl">🏋️</span>
            </div>
        </div>
    </div>

    {{-- BOTONES --}}
    <div class="px-6 mt-6">
        <div class="grid grid-cols-2 gap-4">

            <a href="{{ route('cliente.cuota') }}"
               class="rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">
                <div class="text-3xl mb-3">💳</div>
                <h3 class="font-black">Pagos</h3>
                <p class="text-xs text-zinc-500 mt-1">Cuotas y estado</p>
            </a>

            <a href="{{ route('cliente.mis-reservas') }}"
   class="rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">
    <div class="text-3xl mb-3">📅</div>

    <h3 class="font-black">
        Mis reservas
    </h3>

    @if($misReservas->count() > 0)
        <p class="text-xs {{ $estadoReservaColor }} font-bold mt-1">
    {{ $estadoReservaTexto }}
</p>

<p class="text-xs text-zinc-500 mt-1">
    {{ $misReservas->count() }} reserva{{ $misReservas->count() > 1 ? 's' : '' }} activa{{ $misReservas->count() > 1 ? 's' : '' }}
</p>

        @if($proximaReserva && $proximaReserva->turno)
            <p class="text-xs text-zinc-500 mt-1">
                Próxima:
                {{ $proximaReserva->turno->actividad }}
                {{ \Carbon\Carbon::parse($proximaReserva->turno->hora_inicio)->format('H:i') }}
            </p>
        @endif
    @else
        <p class="text-xs text-zinc-500 mt-1">
            Sin reservas activas
        </p>
    @endif
</a>

            <a href="{{ route('cliente.musculacion') }}"
               class="rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">
               <div class="text-3xl mb-3">🏋️</div>
               <h3 class="font-black">Musculación</h3>
               <p class="text-xs text-zinc-500 mt-1">{{ $presentesAhora }} presentes</p>
            </a>

            <a href="{{ route('cliente.mensajes') }}"
               class="rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">
                <div class="text-3xl mb-3">💬</div>
                <h3 class="font-black">Mensajes</h3>
                <p class="text-xs text-zinc-500 mt-1">Chat interno</p>
            </a>

            <div class="rounded-[1.5rem] bg-white/10 border border-white/10 p-5">
                <div class="text-3xl mb-3">📈</div>
                <h3 class="font-black">Mi progreso</h3>
                <p class="text-xs text-zinc-400 mt-1">Próximamente</p>
            </div>

            <div class="rounded-[1.5rem] bg-white/10 border border-white/10 p-5">
                <div class="text-3xl mb-3">📝</div>
                <h3 class="font-black">Rutinas</h3>
                <p class="text-xs text-zinc-400 mt-1">Próximamente</p>
            </div>

            <div class="rounded-[1.5rem] bg-white/10 border border-white/10 p-5" id="novedades">
                <div class="text-3xl mb-3">📢</div>
                <h3 class="font-black">Novedades</h3>
                <p class="text-xs text-zinc-400 mt-1">Próximamente</p>
            </div>

            <a href="{{ route('cliente.perfil') }}"
   class="rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">

    <div class="text-3xl mb-3">👤</div>

    <h3 class="font-black">
        Mi perfil
    </h3>

    <p class="text-xs text-zinc-500 mt-1">
        Datos personales
    </p>

</a>

        </div>
    </div>

    {{-- CERRAR SESIÓN --}}
    <div class="px-6 mt-8">
        <form method="POST" action="/logout">
            @csrf

            <button type="submit"
                    class="w-full rounded-2xl bg-white/10 border border-white/10 py-3 font-bold text-zinc-300">
                Cerrar sesión
            </button>
        </form>
    </div>

</div>

{{-- BARRA INFERIOR --}}
<div class="fixed bottom-0 left-0 right-0 z-50">
    <div class="max-w-md mx-auto px-5 pb-4">
        <div class="relative rounded-[2rem] bg-white text-zinc-900 shadow-2xl px-5 py-3 flex items-center justify-between">

            <a href="{{ route('cliente.dashboard') }}" class="text-center text-xs font-bold">
                <div class="text-xl">🏠</div>
                Inicio
            </a>

            <a href="{{ route('cliente.turnos') }}" class="text-center text-xs font-bold">
                <div class="text-xl">📅</div>
                Reservas
            </a>

            <a href="{{ route('cliente.mi-qr') }}"
               class="absolute left-1/2 -translate-x-1/2 -top-7 h-16 w-16 rounded-full bg-orange-500 text-white flex items-center justify-center text-3xl shadow-xl border-4 border-[#071015]">
                📱
            </a>

            <a href="{{ route('cliente.mensajes') }}" class="text-center text-xs font-bold ml-14">
                <div class="text-xl">🔔</div>
                Avisos
            </a>

            <a href="{{ route('cliente.perfil') }}" class="text-center text-xs font-bold">
                <div class="text-xl">👤</div>
                Perfil
            </a>

        </div>
    </div>
</div>

</body>
</html>
