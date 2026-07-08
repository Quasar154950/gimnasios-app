<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app-effects.css') }}">
</head>

<body class="bg-[#071015] text-white">

@php
    $cliente = \App\Models\Cliente::where('user_id', auth()->id())->first();
@endphp

<div class="min-h-screen max-w-md mx-auto pb-28 px-6 pt-8">

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('cliente.dashboard') }}"
           class="h-11 w-11 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
            ←
        </a>

        <h1 class="text-xl font-black">
            Mensajes
        </h1>

        <div class="h-11 w-11"></div>
    </div>

    @if($cliente)
        <div class="rounded-[1.5rem] bg-white text-zinc-900 p-5 shadow-xl">
            <h2 class="text-xl font-black mb-2">
                💬 Chat con el gimnasio
            </h2>

            <p class="text-sm text-zinc-500 mb-4">
                Comunicación directa con administración.
            </p>

            <livewire:clientes.mensajes-cliente :cliente="$cliente" />
        </div>
    @else
        <div class="rounded-[1.5rem] bg-yellow-100 text-yellow-800 p-5">
            Tu usuario todavía no está vinculado a un socio.
        </div>
    @endif

</div>

<script src="{{ asset('js/app-effects.js') }}"></script>
</body>
</html>
