<x-layouts::app :title="'Caja'">

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
                        💰 Caja
                    </h1>

                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                        Resumen de ingresos registrados por pagos de socios.
                    </p>
                </div>

                <div
                    class="inline-flex items-center rounded-full border border-orange-500/30
                           bg-orange-500/20 px-4 py-2 text-xs font-black text-orange-500"
                >
                    🧾 {{ $cantidadPagos }} pagos
                </div>

            </div>
        </div>


        {{-- FILTROS --}}
        <div
            class="rounded-xl border border-stone-300 bg-stone-200 p-5 shadow-sm
                   dark:border-stone-600 dark:bg-stone-800"
        >

            <form method="GET" action="{{ route('caja.index') }}" class="space-y-4">

                <div class="flex flex-wrap gap-2">

                    <a
                        href="{{ route('caja.index', ['periodo' => 'hoy']) }}"
                        class="rounded-xl px-4 py-2.5 text-xs font-black transition
                               {{ $periodo === 'hoy'
                                    ? 'bg-orange-500 text-white shadow-md'
                                    : 'bg-stone-100 text-stone-700 hover:bg-stone-300 dark:bg-stone-700 dark:text-stone-200 dark:hover:bg-stone-600' }}"
                    >
                        Hoy
                    </a>

                    <a
                        href="{{ route('caja.index', ['periodo' => 'semana']) }}"
                        class="rounded-xl px-4 py-2.5 text-xs font-black transition
                               {{ $periodo === 'semana'
                                    ? 'bg-orange-500 text-white shadow-md'
                                    : 'bg-stone-100 text-stone-700 hover:bg-stone-300 dark:bg-stone-700 dark:text-stone-200 dark:hover:bg-stone-600' }}"
                    >
                        Esta semana
                    </a>

                    <a
                        href="{{ route('caja.index', ['periodo' => 'mes']) }}"
                        class="rounded-xl px-4 py-2.5 text-xs font-black transition
                               {{ $periodo === 'mes'
                                    ? 'bg-orange-500 text-white shadow-md'
                                    : 'bg-stone-100 text-stone-700 hover:bg-stone-300 dark:bg-stone-700 dark:text-stone-200 dark:hover:bg-stone-600' }}"
                    >
                        Este mes
                    </a>

                </div>


                {{-- PERÍODO PERSONALIZADO --}}
                <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_1fr_auto]">

                    <div>
                        <label class="mb-1 block text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                            Desde
                        </label>

                        <input
                            type="date"
                            name="desde"
                            value="{{ request('desde') }}"
                            class="w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2.5
                                   text-sm font-bold text-stone-900 outline-none
                                   focus:border-orange-500
                                   dark:border-stone-600 dark:bg-stone-700 dark:text-stone-100"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                            Hasta
                        </label>

                        <input
                            type="date"
                            name="hasta"
                            value="{{ request('hasta') }}"
                            class="w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2.5
                                   text-sm font-bold text-stone-900 outline-none
                                   focus:border-orange-500
                                   dark:border-stone-600 dark:bg-stone-700 dark:text-stone-100"
                        >
                    </div>

                    <div class="flex items-end">
                        <button
                            type="submit"
                            name="periodo"
                            value="personalizado"
                            style="cursor: pointer !important;"
                            class="w-full rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-black
                                   text-white shadow-md transition duration-150
                                   hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-xl
                                   active:scale-[0.96] md:w-auto"
                        >
                            Filtrar
                        </button>
                    </div>

                </div>

            </form>

        </div>


        {{-- RESUMEN --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            {{-- TOTAL INGRESOS --}}
            <div
                class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm
                       transition duration-200 hover:-translate-y-1 hover:border-orange-400 hover:shadow-xl
                       dark:border-stone-600 dark:bg-stone-800 dark:hover:border-orange-600"
            >
                <p class="text-[11px] font-black uppercase tracking-wide text-stone-500 dark:text-stone-300">
                    Ingresos del período
                </p>

                <p class="mt-2 text-3xl font-black text-stone-900 dark:text-stone-100">
                    $ {{ number_format((float) $totalIngresos, 2, ',', '.') }}
                </p>

                <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                    Solo pagos aprobados.
                </p>
            </div>


            {{-- CANTIDAD DE PAGOS --}}
            <div
                class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm
                       transition duration-200 hover:-translate-y-1 hover:border-orange-400 hover:shadow-xl
                       dark:border-stone-600 dark:bg-stone-800 dark:hover:border-orange-600"
            >
                <p class="text-[11px] font-black uppercase tracking-wide text-stone-500 dark:text-stone-300">
                    Pagos registrados
                </p>

                <p class="mt-2 text-3xl font-black text-stone-900 dark:text-stone-100">
                    {{ $cantidadPagos }}
                </p>

                <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                    Movimientos incluidos en el período.
                </p>
            </div>

        </div>


        {{-- INGRESOS POR MÉTODO --}}
        <div
            class="rounded-xl border border-stone-300 bg-stone-200 p-5 shadow-sm
                   dark:border-stone-600 dark:bg-stone-800"
        >

            <h2 class="text-lg font-black text-stone-900 dark:text-stone-100">
                💳 Ingresos por medio de pago
            </h2>

            @if($ingresosPorMetodo->isEmpty())

                <div
                    class="mt-4 rounded-xl border border-dashed border-stone-400 p-6 text-center
                           text-sm font-bold text-stone-500
                           dark:border-stone-600 dark:text-stone-400"
                >
                    No hay ingresos registrados en este período.
                </div>

            @else

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">

                    @foreach($ingresosPorMetodo as $metodo)

                        <div
                            class="rounded-xl border border-stone-300 bg-stone-100 p-4
                                   dark:border-stone-600 dark:bg-stone-700"
                        >
                            <p class="text-xs font-black uppercase text-stone-500 dark:text-stone-300">
                                {{ $metodo->metodo_pago ?: 'Sin especificar' }}
                            </p>

                            <p class="mt-2 text-xl font-black text-stone-900 dark:text-stone-100">
                                $ {{ number_format((float) $metodo->total, 2, ',', '.') }}
                            </p>

                            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                {{ $metodo->cantidad }}
                                {{ $metodo->cantidad == 1 ? 'pago' : 'pagos' }}
                            </p>
                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- MOVIMIENTOS --}}
        <div
            class="rounded-xl border border-stone-300 bg-stone-200 p-5 shadow-sm
                   dark:border-stone-600 dark:bg-stone-800"
        >

            <div class="flex items-center justify-between gap-3">

                <div>
                    <h2 class="text-lg font-black text-stone-900 dark:text-stone-100">
                        🧾 Movimientos
                    </h2>

                    <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                        Detalle de ingresos del período seleccionado.
                    </p>
                </div>

            </div>


            @if($movimientos->isEmpty())

                <div
                    class="mt-5 rounded-xl border border-dashed border-stone-400 p-8 text-center
                           text-sm font-bold text-stone-500
                           dark:border-stone-600 dark:text-stone-400"
                >
                    No hay movimientos para mostrar.
                </div>

            @else

                <div class="mt-5 overflow-x-auto">

                    <table class="w-full min-w-[760px] text-left">

                        <thead>
                            <tr class="border-b border-stone-300 dark:border-stone-600">

                                <th class="px-3 py-3 text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Fecha
                                </th>

                                <th class="px-3 py-3 text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Socio
                                </th>

                                <th class="px-3 py-3 text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Medio
                                </th>

                                <th class="px-3 py-3 text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Origen
                                </th>

                                <th class="px-3 py-3 text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Comprobante
                                </th>    

                                <th class="px-3 py-3 text-right text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Importe
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @foreach($movimientos as $movimiento)

    <tr
        class="border-b border-stone-300/70 transition
               hover:bg-orange-500/10
               dark:border-stone-700"
    >

        {{-- FECHA --}}
        <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-stone-700 dark:text-stone-200">
            📅 {{ $movimiento->fecha_pago?->format('d/m/Y') }}
        </td>

        {{-- SOCIO --}}
        <td class="px-3 py-4 text-sm font-black text-stone-900 dark:text-stone-100">
            {{ $movimiento->cliente?->nombre ?? 'Socio no disponible' }}
        </td>

        {{-- MEDIO DE PAGO --}}
        <td class="px-3 py-4 text-sm text-stone-700 dark:text-stone-300">
            {{ $movimiento->metodo_pago ?: 'Sin especificar' }}
        </td>

        {{-- ORIGEN --}}
        <td class="px-3 py-4">
            <span
                class="inline-flex rounded-full border border-orange-500/30
                       bg-orange-500/15 px-2.5 py-1 text-[10px]
                       font-black uppercase text-orange-500"
            >
                {{ $movimiento->origen ?: 'manual' }}
            </span>
        </td>

        {{-- COMPROBANTE --}}
        <td class="whitespace-nowrap px-3 py-4 text-sm">

            @if($movimiento->numero_comprobante)

                <div class="flex flex-col items-start gap-1">

                    <span class="font-black text-stone-900 dark:text-stone-100">
                        N.º {{ str_pad($movimiento->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
                    </span>

                    <a
                        href="{{ route('comprobantes.show', $movimiento) }}"
                        class="text-xs font-black text-orange-500 transition
                               hover:text-orange-600 hover:underline"
                    >
                        Ver comprobante →
                    </a>

                </div>

            @else

                <span class="text-xs font-bold text-stone-400">
                    Pago anterior
                </span>

            @endif

        </td>

        {{-- IMPORTE --}}
        <td class="whitespace-nowrap px-3 py-4 text-right text-sm font-black text-stone-900 dark:text-stone-100">
            $ {{ number_format((float) $movimiento->monto, 2, ',', '.') }}
        </td>

    </tr>

@endforeach
                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</x-layouts::app>
