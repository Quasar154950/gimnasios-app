<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Socio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-zinc-100">

    @php
        $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

        $misReservas = collect();
        $actividadEnCurso = null;
        $proximaReserva = null;

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

            $ahora = now();

            $actividadEnCurso = $misReservas
                ->filter(function ($r) use ($ahora) {
                    if (!$r->turno) {
                        return false;
                    }

                    $inicio = \Carbon\Carbon::parse($r->turno->fecha . ' ' . $r->turno->hora_inicio);
                    $fin = \Carbon\Carbon::parse($r->turno->fecha . ' ' . $r->turno->hora_fin);

                    return $ahora->between($inicio, $fin);
                })
                ->sortBy(fn($r) => $r->turno->hora_inicio)
                ->first();

            $proximaReserva = $misReservas
                ->filter(function ($r) use ($ahora) {
                    if (!$r->turno) {
                        return false;
                    }

                    $inicio = \Carbon\Carbon::parse($r->turno->fecha . ' ' . $r->turno->hora_inicio);

                    return $inicio->isFuture();
                })
                ->sortBy(fn($r) => $r->turno->fecha . ' ' . $r->turno->hora_inicio)
                ->first();
        }

        $presentesAhora = \App\Models\Asistencia::where('presente', true)
            ->whereNull('hora_salida')
            ->count();
    @endphp

    <div class="min-h-screen p-6">
        <div class="w-full max-w-5xl mx-auto">

            {{-- BIENVENIDA --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                <h1 class="text-2xl font-bold text-zinc-800 mb-2">
                    👋 Bienvenido, {{ $cliente ? $cliente->nombre : auth()->user()->name }}
                </h1>

                <p class="text-zinc-600">
                    Panel de socio del gimnasio.
                </p>

            </div>

            {{-- ACTIVIDAD EN CURSO / PRÓXIMA --}}
            @if($actividadEnCurso || $proximaReserva)

                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow-md p-6 mb-6 text-white">

                    @if($actividadEnCurso)

                        <p class="text-sm uppercase tracking-wide opacity-80">
                            🟢 Actividad en curso
                        </p>

                        <h2 class="text-2xl font-bold mt-2">
                            {{ $actividadEnCurso->turno->actividad }}
                        </h2>

                        <p class="mt-2 text-sm">
                            🕒
                            {{ \Carbon\Carbon::parse($actividadEnCurso->turno->hora_inicio)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($actividadEnCurso->turno->hora_fin)->format('H:i') }}
                        </p>

                    @endif

                    @if($proximaReserva)

                        <div class="{{ $actividadEnCurso ? 'mt-5 pt-5 border-t border-white/20' : '' }}">

                            <p class="text-sm uppercase tracking-wide opacity-80">
                                📌 Próxima actividad
                            </p>

                            <h2 class="text-2xl font-bold mt-2">
                                {{ $proximaReserva->turno->actividad }}
                            </h2>

                            <p class="mt-2 text-sm">
                                📅 {{ \Carbon\Carbon::parse($proximaReserva->turno->fecha)->format('d/m/Y') }}
                                ·
                                🕒 {{ \Carbon\Carbon::parse($proximaReserva->turno->hora_inicio)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($proximaReserva->turno->hora_fin)->format('H:i') }}
                            </p>

                        </div>

                    @endif

                </div>

            @endif

            {{-- DATOS --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="border border-stone-300 bg-stone-100 rounded-xl p-4">

                        <p class="text-sm text-zinc-500">
                            Socio
                        </p>

                        <p class="font-semibold text-zinc-800">
                            {{ $cliente ? $cliente->nombre : auth()->user()->name }}
                        </p>

                    </div>

                    <div class="border border-stone-300 bg-stone-100 rounded-xl p-4">

                        <p class="text-sm text-zinc-500">
                            Email
                        </p>

                        <p class="font-semibold text-zinc-800">
                            {{ auth()->user()->email }}
                        </p>

                    </div>

                    <div class="border border-stone-300 bg-stone-100 rounded-xl p-4">

                        <p class="text-sm text-zinc-500">
                            Estado
                        </p>

                        <p class="font-semibold text-green-600">
                            ✅ Activo
                        </p>

                    </div>

                </div>

            </div>

            {{-- ACCIONES --}}
<div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

    <h2 class="text-xl font-bold text-zinc-800 mb-4">
        🏋️ Actividades y turnos
    </h2>

    <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory">

        <a href="/cliente/turnos"
           class="min-w-[280px] snap-start border border-stone-300 bg-stone-100 rounded-xl p-5 hover:border-orange-500 hover:bg-orange-50 transition block cursor-pointer">

            <div class="text-3xl mb-2">📅</div>

            <h3 class="font-bold text-zinc-800">
                Ver turnos
            </h3>

            <p class="text-sm text-zinc-500 mt-1">
                Reservá clases y consultá horarios disponibles.
            </p>

        </a>

        <a
            href="{{ route('cliente.cuota') }}"
            class="min-w-[280px] snap-start border border-stone-300 bg-stone-100 rounded-xl p-5 hover:border-orange-500 hover:bg-orange-50 transition block cursor-pointer"
        >

            <div class="text-3xl mb-2">💳</div>

            <h3 class="font-bold text-zinc-800">
                Estado de cuota
            </h3>

            @if($cliente && $cliente->fecha_vencimiento_cuota)

                @php
                    $fechaVencimiento = \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota);
                    $diasRestantes = now()->diffInDays($fechaVencimiento, false);
                @endphp

                @if($diasRestantes < 0)

                    <div class="mt-3 inline-flex items-center rounded-xl bg-red-100 px-3 py-2 text-sm font-bold text-red-700">
                        ❌ Cuota vencida
                    </div>

                @elseif($diasRestantes <= 5)

                    <div class="mt-3 inline-flex items-center rounded-xl bg-yellow-100 px-3 py-2 text-sm font-bold text-yellow-700">
                        ⚠️ Vence pronto
                    </div>

                @else

                    <div class="mt-3 inline-flex items-center rounded-xl bg-green-100 px-3 py-2 text-sm font-bold text-green-700">
                        ✅ Cuota al día
                    </div>

                @endif

                <p class="text-sm text-zinc-500 mt-3">
                    Vencimiento:
                    <span class="font-semibold text-zinc-700">
                        {{ $fechaVencimiento->format('d/m/Y') }}
                    </span>
                </p>

            @else

                <p class="text-sm text-zinc-500 mt-2">
                    Sin vencimiento cargado.
                </p>

            @endif

        </a>

        <a
            href="{{ route('cliente.mi-qr') }}"
            class="min-w-[280px] snap-start border border-stone-300 bg-stone-100 rounded-xl p-5 hover:border-orange-500 hover:bg-orange-50 transition block cursor-pointer"
        >

            <div class="text-3xl mb-2">📱</div>

            <h3 class="font-bold text-zinc-800">
                Mi QR
            </h3>

            <div class="mt-3 inline-flex items-center rounded-xl bg-black px-3 py-2 text-sm font-bold text-white">
                Mostrar QR
            </div>

            <p class="text-sm text-zinc-500 mt-3">
                Usá tu código QR para registrar ingresos y egresos.
            </p>

        </a>

        <div class="min-w-[280px] snap-start border border-stone-300 bg-stone-100 rounded-xl p-5">

            <div class="text-3xl mb-2">🏋️</div>

            <h3 class="font-bold text-zinc-800">
                Musculación ahora
            </h3>

            <div class="mt-3 inline-flex items-center rounded-xl bg-orange-500 px-3 py-2 text-sm font-bold text-white">
                👥 {{ $presentesAhora }} socios entrenando
            </div>

            <p class="text-sm text-zinc-500 mt-3">
                Cantidad actual de personas dentro del gimnasio.
            </p>

        </div>

    </div>

</div>

            {{-- MIS RESERVAS --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                <div class="mb-4">

                    <h2 class="text-xl font-bold text-zinc-800">
                        📅 Mis reservas
                    </h2>

                    <p class="text-sm text-zinc-500 mt-1">
                        Tus turnos reservados vigentes.
                    </p>

                </div>

                @if(!$cliente)

                    <div class="border border-yellow-300 bg-yellow-50 text-yellow-800 rounded-xl p-4">
                        Tu usuario todavía no está vinculado a un socio.
                    </div>

                @elseif($misReservas->isEmpty())

                    <div class="border border-stone-300 bg-stone-100 rounded-xl p-4 text-zinc-600">
                        No tenés reservas vigentes.
                    </div>

                @else

                    <div class="space-y-3">

                        @foreach($misReservas as $reserva)

                            <div class="border border-stone-300 bg-stone-100 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                                <div>

                                    <h3 class="font-bold text-zinc-800">
                                        🏋️ {{ $reserva->turno->actividad ?? 'Turno eliminado' }}
                                    </h3>

                                    @if($reserva->turno)

                                        <p class="text-sm text-zinc-500 mt-1">
                                            📅 {{ \Carbon\Carbon::parse($reserva->turno->fecha)->format('d/m/Y') }}
                                            · 🕒 {{ \Carbon\Carbon::parse($reserva->turno->hora_inicio)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($reserva->turno->hora_fin)->format('H:i') }}
                                        </p>

                                        <p class="text-sm text-zinc-500 mt-1">
                                            👨‍🏫 Profesor: {{ $reserva->turno->profesor ?? 'A confirmar' }}
                                        </p>

                                    @endif

                                </div>

                                <div class="flex flex-col items-center gap-2">

                                    <span class="inline-flex items-center justify-center rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                        ✅ {{ ucfirst($reserva->estado) }}
                                    </span>

                                    <form method="POST" action="{{ route('cliente.reservas.cancelar', $reserva) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="return confirm('¿Cancelar esta reserva?')"
                                            class="rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700 transition"
                                        >
                                            Cancelar reserva
                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

            {{-- CHAT --}}
            @if($cliente)

                <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                    <div class="mb-4">

                        <h2 class="text-xl font-bold text-zinc-800 flex items-center gap-2">
                            💬 Mensajes con el gimnasio
                        </h2>

                        <p class="text-sm text-zinc-500 mt-1">
                            Comunicación directa con administración.
                        </p>

                    </div>

                    <livewire:clientes.mensajes-cliente :cliente="$cliente" />

                </div>

            @endif

            {{-- CERRAR SESIÓN --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6">

                <form method="POST" action="/logout">
                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-orange-500 text-white py-2 rounded-xl hover:bg-orange-600 transition"
                    >
                        Cerrar sesión
                    </button>

                </form>

            </div>

        </div>

    </div>

</body>
</html>
