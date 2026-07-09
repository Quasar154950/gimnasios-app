<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musculación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app-effects.css') }}">
</head>

<body class="bg-[#071015] text-white">

@php
    $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();

    $presentesAhora = 0;

    if ($cliente) {
        $presentesAhora = \App\Models\Asistencia::where('presente', true)
            ->whereNull('hora_salida')
            ->whereHas('cliente', function ($query) use ($cliente) {
                $query->where('abogado_id', $cliente->abogado_id);
            })
            ->count();
    }

    $capacidad = 60;

    $porcentaje = $capacidad > 0 ? min(100, round(($presentesAhora / $capacidad) * 100)) : 0;

    if ($porcentaje < 40) {
        $estado = 'Ocupación baja';
        $color = 'bg-green-500';
        $texto = 'Hay buena disponibilidad para entrenar.';
    } elseif ($porcentaje < 75) {
        $estado = 'Ocupación media';
        $color = 'bg-yellow-500';
        $texto = 'El gimnasio tiene movimiento moderado.';
    } else {
        $estado = 'Ocupación alta';
        $color = 'bg-red-500';
        $texto = 'Hay bastante gente entrenando ahora.';
    }
@endphp

<div class="min-h-screen max-w-md mx-auto px-6 pt-8 pb-28">

    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('cliente.dashboard') }}"
           class="h-11 w-11 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
            ←
        </a>

        <h1 class="text-xl font-black">
            Musculación
        </h1>

        <div class="h-11 w-11"></div>
    </div>

    <div class="rounded-[2rem] bg-gradient-to-br from-orange-500 to-orange-700 p-6 shadow-2xl text-center">

        <div class="text-6xl mb-4">🏋️</div>

        <p class="text-sm text-orange-100">
            Socios entrenando ahora
        </p>

        <h2 class="text-6xl font-black mt-2">
            {{ $presentesAhora }}
        </h2>

        <p class="text-orange-100 mt-2">
            dentro del gimnasio
        </p>

    </div>

    <div class="mt-6 rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">

        <div class="flex items-center justify-between mb-3">
            <h2 class="font-black text-lg">
                Estado actual
            </h2>

            <span class="text-xs font-bold px-3 py-1 rounded-full text-white {{ $color }}">
                {{ $estado }}
            </span>
        </div>

        <p class="text-sm text-zinc-500 mb-4">
            {{ $texto }}
        </p>

        <div class="w-full h-4 bg-zinc-200 rounded-full overflow-hidden">
            <div class="h-full {{ $color }} rounded-full"
                 style="width: {{ $porcentaje }}%;">
            </div>
        </div>

        <div class="flex justify-between mt-3 text-sm text-zinc-500">
            <span>{{ $presentesAhora }} presentes</span>
            <span>Capacidad estimada: {{ $capacidad }}</span>
        </div>

    </div>

    <div class="mt-6 rounded-[1.5rem] bg-white/10 border border-white/10 p-5">
        <h3 class="font-black mb-2">
            Información
        </h3>

        <p class="text-sm text-zinc-400">
            Estos datos se actualizan según los ingresos y egresos registrados por QR.
        </p>
    </div>

</div>

<script src="{{ asset('js/app-effects.js') }}"></script> 
</body>
</html>
