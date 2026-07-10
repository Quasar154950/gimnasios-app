<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis reservas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app-effects.css') }}">
</head>

<body class="bg-[#071015] text-white">

<div class="min-h-screen max-w-md mx-auto pb-10">

    {{-- CABECERA --}}
    <div class="px-6 pt-8">

        <a href="{{ route('cliente.dashboard') }}"
           class="inline-flex items-center text-orange-400 font-bold">
            ← Volver
        </a>

        <h1 class="text-3xl font-black mt-5">
            📅 Mis reservas
        </h1>

        <p class="text-zinc-400 mt-2">
            Administrá tus actividades reservadas.
        </p>

    </div>

    <div class="px-6 mt-8 space-y-4">

        @if($misReservas->isEmpty())

            <div class="card-app rounded-3xl bg-white/10 p-8 text-center">

                <div class="text-6xl mb-4">
                    📭
                </div>

                <h2 class="text-xl font-black">
                    No tenés reservas
                </h2>

                <p class="text-zinc-400 mt-2">
                    Reservá una actividad para comenzar.
                </p>

                <a href="{{ route('cliente.turnos') }}"
                   class="mt-6 inline-block rounded-2xl bg-orange-500 px-6 py-3 font-bold">
                    Ver actividades
                </a>

            </div>

        @else

            @foreach($misReservas as $reserva)

                <div class="card-app rounded-3xl bg-white text-zinc-900 p-5 shadow-xl">

                    <div class="flex justify-between items-start">

                        <div>

                            <h2 class="text-xl font-black">
                                🏋️ {{ $reserva->turno->actividad }}
                            </h2>

                            <p class="text-sm text-zinc-500 mt-2">
                                📅 {{ \Carbon\Carbon::parse($reserva->turno->fecha)->format('d/m/Y') }}
                            </p>

                            <p class="text-sm text-zinc-500">
                                🕒 {{ \Carbon\Carbon::parse($reserva->turno->hora_inicio)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($reserva->turno->hora_fin)->format('H:i') }}
                            </p>

                        </div>

                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                            Confirmada
                        </span>

                    </div>

                    @php
    $inicioTurno = \Carbon\Carbon::parse($reserva->turno->fecha . ' ' . $reserva->turno->hora_inicio);
    $limiteCancelacion = now()->copy()->addHour();
    $puedeCancelar = $inicioTurno->greaterThan($limiteCancelacion);
@endphp

@if($puedeCancelar)

    <form
        method="POST"
        action="{{ route('cliente.reservas.cancelar',$reserva) }}"
        class="mt-5">

        @csrf
        @method('DELETE')

        <button
            onclick="return confirm('¿Cancelar esta reserva?')"
            class="w-full rounded-2xl bg-red-500 py-3 font-bold text-white">

            Cancelar reserva

        </button>

    </form>

@else

    <div class="mt-5 rounded-2xl bg-zinc-200 py-3 text-center font-bold text-zinc-500">
        Cancelación no disponible
    </div>

    <p class="text-xs text-zinc-500 text-center mt-2">
        No se puede cancelar una reserva dentro de la hora previa o cuando la actividad ya comenzó.
    </p>

@endif

                </div>

            @endforeach

            <a href="{{ route('cliente.turnos') }}"
               class="block text-center rounded-2xl bg-orange-500 py-4 font-black text-white">

                ➕ Reservar otra actividad

            </a>

        @endif

    </div>

</div>
<script src="{{ asset('js/app-effects.js') }}"></script>
</body>
</html>
