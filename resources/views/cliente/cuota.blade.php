<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de cuota</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-zinc-100">

    @php

        $fechaVencimiento = null;
        $diasRestantes = null;

        if ($cliente && $cliente->fecha_vencimiento_cuota) {

            $fechaVencimiento = \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota);

            $diasRestantes = now()->diffInDays($fechaVencimiento, false);
        }

    @endphp

    <div class="min-h-screen p-6">

        <div class="w-full max-w-3xl mx-auto">

            {{-- CABECERA --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                <h1 class="text-2xl font-black text-zinc-800">
                    💳 Estado de cuota
                </h1>

                <p class="text-sm text-zinc-600 mt-2">
                    Información sobre tu membresía del gimnasio.
                </p>

            </div>

            @if(session('error'))
                <div class="rounded-xl bg-red-100 text-red-700 font-bold px-4 py-3 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="rounded-xl bg-green-100 text-green-700 font-bold px-4 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ESTADO --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                @if($cliente && $fechaVencimiento)

                    @if($diasRestantes < 0)

                        <div class="inline-flex items-center rounded-xl bg-red-100 px-4 py-3 text-base font-black text-red-700">
                            ❌ Cuota vencida
                        </div>

                    @elseif($diasRestantes <= 5)

                        <div class="inline-flex items-center rounded-xl bg-yellow-100 px-4 py-3 text-base font-black text-yellow-700">
                            ⚠️ Tu cuota vence pronto
                        </div>

                    @else

                        <div class="inline-flex items-center rounded-xl bg-green-100 px-4 py-3 text-base font-black text-green-700">
                            ✅ Cuota al día
                        </div>

                    @endif

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="border border-stone-300 bg-stone-100 rounded-xl p-4">

                            <p class="text-sm text-zinc-500">
                                Socio
                            </p>

                            <p class="font-bold text-zinc-800 mt-1">
                                {{ $cliente->nombre }}
                            </p>

                        </div>

                        <div class="border border-stone-300 bg-stone-100 rounded-xl p-4">

                            <p class="text-sm text-zinc-500">
                                Vencimiento
                            </p>

                            <p class="font-bold text-zinc-800 mt-1">
                                {{ $fechaVencimiento->format('d/m/Y') }}
                            </p>

                        </div>

                    </div>

                @else

                    <div class="rounded-xl bg-yellow-100 text-yellow-800 font-bold px-4 py-3">
                        ⚠️ No hay vencimiento cargado.
                    </div>

                @endif

            </div>

            {{-- PAGO ONLINE --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                <h2 class="text-lg font-black text-zinc-800">
                    💳 Pagar cuota online
                </h2>

                <p class="text-sm text-zinc-600 mt-3 leading-relaxed">
                    Aboná tu cuota del gimnasio de forma online con Mercado Pago.
                </p>

                <a
                    href="{{ route('cliente.pagar-cuota') }}"
                    class="mt-5 flex items-center justify-center transition duration-200 hover:scale-105"
                >

                    <div class="bg-white border border-stone-300 rounded-2xl shadow-md hover:shadow-xl p-4">

                        <img
                            src="{{ asset('images/mp-logo.png') }}"
                            alt="Pagar con Mercado Pago"
                            class="h-16 w-auto"
                        >

                    </div>

                </a>

                <p class="text-xs text-zinc-500 mt-3 text-center">
                    Serás redirigido a Mercado Pago para completar el pago.
                </p>

            </div>

            {{-- CONTACTO --}}
            <div class="bg-stone-200 border border-stone-300 rounded-xl shadow-md p-6 mb-6">

                <h2 class="text-lg font-black text-zinc-800">
                    📲 Administración
                </h2>

                <p class="text-sm text-zinc-600 mt-3">
                    Si necesitás regularizar tu cuota o tenés dudas, comunicate con el gimnasio.
                </p>

                <a
                    href="https://wa.me/"
                    target="_blank"
                    class="mt-5 inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 text-sm font-bold text-white hover:bg-orange-600 transition"
                >
                    WhatsApp administración
                </a>

            </div>

            {{-- VOLVER --}}
            <div class="text-center">

                <a
                    href="{{ route('cliente.dashboard') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-bold text-white hover:bg-zinc-800 transition"
                >
                    ← Volver al panel
                </a>

            </div>

        </div>

    </div>

</body>
</html>
