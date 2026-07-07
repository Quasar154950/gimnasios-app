<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#071015] text-white">

<div class="min-h-screen max-w-md mx-auto pb-32">

    <div class="px-6 pt-8">
        <a href="{{ route('cliente.dashboard') }}" class="inline-flex items-center text-orange-400 font-bold">
            ← Volver
        </a>

        <h1 class="text-3xl font-black mt-5">
            👤 Mi perfil
        </h1>

        <p class="text-zinc-400 mt-2">
            Tus datos y actividad dentro del gimnasio.
        </p>
    </div>

    <div class="px-6 mt-8">

        <div class="rounded-[2rem] bg-gradient-to-br from-orange-500 to-orange-700 p-6 shadow-2xl text-center">
            <div class="h-24 w-24 mx-auto rounded-full bg-white text-zinc-900 flex items-center justify-center text-5xl shadow-xl">
                👤
            </div>

            <h2 class="text-2xl font-black mt-4">
                {{ $cliente?->nombre ?? auth()->user()->name }}
            </h2>

            <p class="text-orange-100 mt-1">
                Socio del gimnasio
            </p>

            <div class="mt-4 inline-flex rounded-full bg-green-100 px-4 py-2 text-sm font-black text-green-700">
                🟢 Activo
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6">

            <div class="rounded-3xl bg-white text-zinc-900 p-5 shadow-xl">
                <div class="text-3xl mb-2">📧</div>
                <p class="text-xs text-zinc-500">Email</p>
                <p class="font-bold text-sm break-words mt-1">
                    {{ auth()->user()->email }}
                </p>
            </div>

            <div class="rounded-3xl bg-white text-zinc-900 p-5 shadow-xl">
                <div class="text-3xl mb-2">📱</div>
                <p class="text-xs text-zinc-500">Teléfono</p>
                <p class="font-bold text-sm mt-1">
                    {{ $cliente?->telefono ?? 'No cargado' }}
                </p>
            </div>

            <div class="rounded-3xl bg-white text-zinc-900 p-5 shadow-xl">
                <div class="text-3xl mb-2">🏋️</div>
                <p class="text-xs text-zinc-500">Ingresos este mes</p>
                <p class="font-black text-2xl mt-1">
                    {{ $ingresosMes }}
                </p>
            </div>

            <div class="rounded-3xl bg-white text-zinc-900 p-5 shadow-xl">
                <div class="text-3xl mb-2">📅</div>
                <p class="text-xs text-zinc-500">Reservas activas</p>
                <p class="font-black text-2xl mt-1">
                    {{ $reservasActivas }}
                </p>
            </div>

        </div>

        <div class="rounded-3xl bg-white/10 border border-white/10 p-5 mt-6">
            <h3 class="font-black text-lg">
                💳 Estado de cuota
            </h3>

            @if($cliente && $cliente->fecha_vencimiento_cuota)
                @php
                    $vencimiento = \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota);
                    $dias = now()->diffInDays($vencimiento, false);
                @endphp

                <p class="text-zinc-400 mt-2">
                    Vencimiento:
                    <span class="font-bold text-white">
                        {{ $vencimiento->format('d/m/Y') }}
                    </span>
                </p>

                @if($dias < 0)
                    <div class="mt-4 rounded-2xl bg-red-500/20 border border-red-500/30 p-3 text-red-300 font-bold">
                        ❌ Cuota vencida
                    </div>
                @elseif($dias <= 5)
                    <div class="mt-4 rounded-2xl bg-yellow-500/20 border border-yellow-500/30 p-3 text-yellow-300 font-bold">
                        ⚠️ Vence pronto
                    </div>
                @else
                    <div class="mt-4 rounded-2xl bg-green-500/20 border border-green-500/30 p-3 text-green-300 font-bold">
                        ✅ Cuota al día
                    </div>
                @endif
            @else
                <p class="text-zinc-400 mt-2">
                    Sin vencimiento cargado.
                </p>
            @endif
        </div>

        <div class="rounded-3xl bg-white/10 border border-white/10 p-5 mt-6">
            <h3 class="font-black text-lg">
                🕒 Último ingreso
            </h3>

            @if($ultimoIngreso)
                <p class="text-zinc-300 mt-2">
                    {{ \Carbon\Carbon::parse($ultimoIngreso->created_at)->format('d/m/Y H:i') }}
                </p>
            @else
                <p class="text-zinc-400 mt-2">
                    Todavía no hay ingresos registrados.
                </p>
            @endif
        </div>

        <form method="POST" action="/logout" class="mt-8">
            @csrf

            <button class="w-full rounded-2xl bg-white/10 border border-white/10 py-3 font-bold text-zinc-300">
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
