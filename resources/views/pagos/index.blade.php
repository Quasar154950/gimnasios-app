<x-layouts::app :title="'Pagos / Cuotas'">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 sm:-m-6 sm:p-6">

        {{-- ENCABEZADO --}}
        <div
            class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm
                   transition duration-200 hover:-translate-y-0.5 hover:shadow-lg
                   dark:border-stone-600 dark:bg-stone-800"
        >

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div>

                    <h1 class="text-2xl font-black text-stone-900 dark:text-stone-100">
                        💰 Pagos / Cuotas
                    </h1>

                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                        Estado general de cuotas y vencimientos de socios.
                    </p>

                </div>

                <div
                    class="inline-flex items-center rounded-full border border-orange-500/30
                           bg-orange-500/20 px-4 py-2 text-xs font-black text-orange-500"
                >
                    👥 {{ $clientes->count() }} socios
                </div>

            </div>

        </div>


        {{-- LISTADO --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">

            @foreach($clientes as $cliente)

                @php

                    $hoy = \Carbon\Carbon::today();

                    $vence = $cliente->fecha_vencimiento_cuota
                        ? \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota)
                        : null;

                    if (!$vence) {

                        $badge = 'bg-stone-300 dark:bg-stone-700 text-stone-700 dark:text-stone-200 border border-stone-400 dark:border-stone-600';
                        $textoEstado = 'Sin vencimiento';

                    } elseif ($vence->lt($hoy)) {

                        $badge = 'bg-red-500/20 text-red-600 dark:text-red-300 border border-red-500/30';
                        $textoEstado = 'Cuota vencida';

                    } elseif ($vence->diffInDays($hoy) <= 3) {

                        $badge = 'bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border border-yellow-500/30';
                        $textoEstado = 'Próxima a vencer';

                    } else {

                        $badge = 'bg-green-500/20 text-green-700 dark:text-green-300 border border-green-500/30';
                        $textoEstado = 'Al día';

                    }

                    $telefono = preg_replace('/\D/', '', $cliente->telefono);

                @endphp


                {{-- TARJETA DEL SOCIO --}}
                <div
                    class="group rounded-xl border border-stone-300 bg-stone-200 p-5 shadow-sm
                           transition duration-200
                           hover:-translate-y-1 hover:border-orange-400 hover:shadow-xl
                           dark:border-stone-600 dark:bg-stone-800 dark:hover:border-orange-600"
                >

                    {{-- CABECERA --}}
                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">

                            <h2 class="truncate text-lg font-black text-stone-900 dark:text-stone-100">
                                👤 {{ $cliente->nombre }}
                            </h2>

                            <p class="mt-1 text-sm text-stone-600 dark:text-stone-300">
                                📞 {{ $cliente->telefono }}
                            </p>

                        </div>


                        {{-- ESTADO --}}
                        <span
                            class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full
                                   px-3 py-1.5 text-[11px] font-black shadow-sm {{ $badge }}"
                        >

                            @if($textoEstado === 'Cuota vencida')
                                🔴
                            @elseif($textoEstado === 'Próxima a vencer')
                                🟡
                            @elseif($textoEstado === 'Al día')
                                🟢
                            @else
                                ⚪
                            @endif

                            <span class="ml-1">
                                {{ $textoEstado }}
                            </span>

                        </span>

                    </div>


                    {{-- INFORMACIÓN --}}
                    <div class="mt-5 grid grid-cols-2 gap-3">

                        {{-- VENCIMIENTO --}}
                        <div
                            class="rounded-xl border border-stone-300 bg-stone-100 p-4
                                   transition duration-200 group-hover:shadow-sm
                                   dark:border-stone-600 dark:bg-stone-700"
                        >

                            <p class="text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                Vencimiento
                            </p>

                            <p class="mt-1 text-sm font-black text-stone-900 dark:text-stone-100">

                                @if($vence)

                                    📅 {{ $vence->format('d/m/Y') }}

                                @else

                                    —

                                @endif

                            </p>

                        </div>


                        {{-- ESTADO --}}
                        <div
                            class="rounded-xl border border-stone-300 bg-stone-100 p-4
                                   transition duration-200 group-hover:shadow-sm
                                   dark:border-stone-600 dark:bg-stone-700"
                        >

                            <p class="text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                Estado
                            </p>

                            <p class="mt-1 text-sm font-black text-stone-900 dark:text-stone-100">

                                @if($textoEstado === 'Cuota vencida')
                                    🔴
                                @elseif($textoEstado === 'Próxima a vencer')
                                    🟡
                                @elseif($textoEstado === 'Al día')
                                    🟢
                                @else
                                    ⚪
                                @endif

                                {{ $textoEstado }}

                            </p>

                        </div>

                    </div>


                    {{-- ACCIONES --}}
                    <div class="mt-5 flex flex-nowrap items-center gap-2 overflow-x-auto pb-1">

                        {{-- VER HISTORIAL --}}
                        <a
                            href="{{ route('clientes.pagos', $cliente->id) }}"
                            style="cursor: pointer !important;"
                            class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap
                                   rounded-xl bg-orange-500 px-4 py-2.5 text-xs font-black text-white
                                   shadow-md transition duration-150
                                   hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-xl
                                   active:scale-[0.96]"
                        >
                            💳 Ver historial
                        </a>


                        {{-- WHATSAPP --}}
                        <a
                            href="https://wa.me/549{{ $telefono }}?text={{ urlencode('Hola ' . $cliente->nombre . ', te recordamos el vencimiento de tu cuota del gimnasio.') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="cursor: pointer !important;"
                            class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap
                                   rounded-xl bg-black px-4 py-2.5 text-xs font-black text-white
                                   shadow-md transition duration-150
                                   hover:-translate-y-0.5 hover:bg-zinc-800 hover:shadow-xl
                                   active:scale-[0.96]"
                        >

                            <img
                                src="{{ asset('images/whatsapp.png') }}"
                                alt="WhatsApp"
                                class="h-[18px] w-[18px] shrink-0 object-contain"
                            >

                            Avisar vencimiento

                        </a>

                    </div>

                </div>

            @endforeach

        </div>

    </div>


    <style>

        /*
        |--------------------------------------------------------------------------
        | SCROLL FINO DE ACCIONES
        |--------------------------------------------------------------------------
        */

        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #f97316 transparent;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 5px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 999px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #fb923c;
        }

    </style>

</x-layouts::app>