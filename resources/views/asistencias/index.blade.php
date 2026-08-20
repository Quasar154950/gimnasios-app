<x-layouts::app :title="'Asistencias'">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 pb-10 sm:-m-6 sm:p-6">

        {{-- CABECERA --}}
        <section
            class="rounded-3xl border border-orange-900/60 bg-zinc-900 p-6 shadow-xl
                   transition duration-200 hover:-translate-y-0.5 hover:shadow-2xl"
        >

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div>

                    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-orange-950 px-3 py-1 text-xs font-black uppercase text-orange-300">
                        👥 Control de asistencia
                    </div>

                    <h1 class="text-3xl font-black text-white">
                        Asistencias
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-400">
                        Ingresos, egresos y socios presentes actualmente en el gimnasio.
                    </p>

                </div>


                {{-- CONTADOR --}}
                <div
                    class="min-w-[170px] rounded-2xl border border-orange-700 bg-orange-600 px-5 py-4 text-white shadow-lg
                           transition duration-200 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl"
                >

                    <p class="text-xs font-black uppercase tracking-wide text-orange-100">
                        Presentes ahora
                    </p>

                    <div class="mt-2 flex items-end gap-2">

                        <p class="text-4xl font-black">
                            {{ $presentes->count() }}
                        </p>

                        <span class="pb-1 text-sm font-bold text-orange-100">
                            socios
                        </span>

                    </div>

                </div>

            </div>

        </section>


        {{-- SOCIOS PRESENTES --}}
        <section class="rounded-3xl border border-orange-900/40 bg-zinc-900 p-6 shadow-xl">

            <div class="mb-5">

                <h2 class="text-xl font-black text-white">
                    🟢 Socios presentes
                </h2>

                <p class="mt-1 text-sm text-zinc-500">
                    Personas que todavía no registraron salida.
                </p>

            </div>


            @if($presentes->isEmpty())

                <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-950 p-8 text-center">

                    <div class="text-4xl">
                        👥
                    </div>

                    <p class="mt-3 text-sm italic text-zinc-500">
                        No hay socios presentes actualmente.
                    </p>

                </div>

            @else

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                    @foreach($presentes as $asistencia)

                        <article
                            class="group rounded-2xl border border-zinc-800 bg-zinc-950 p-5 shadow-lg
                                   transition duration-200 hover:-translate-y-1 hover:border-orange-700 hover:shadow-xl"
                        >

                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <div class="flex items-center gap-2">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-950 text-lg">
                                            👤
                                        </div>

                                        <div class="min-w-0">

                                            <h3 class="truncate text-base font-black text-white">
                                                {{ $asistencia->cliente->nombre }}
                                            </h3>

                                            <span class="mt-1 inline-flex items-center rounded-full bg-green-950 px-2.5 py-1 text-[10px] font-bold text-green-300">
                                                ● Presente
                                            </span>

                                        </div>

                                    </div>


                                    <p class="mt-4 text-sm text-zinc-400">
                                        🕒 Ingreso:
                                        <span class="font-black text-white">
                                            {{ $asistencia->hora_ingreso->format('H:i') }} hs
                                        </span>
                                    </p>

                                </div>


                                {{-- SALIDA --}}
                                <form
                                    method="POST"
                                    action="{{ route('asistencias.salida', $asistencia->id) }}"
                                    onsubmit="
                                        const boton = this.querySelector('button');

                                        if (boton) {
                                            boton.disabled = true;
                                            boton.innerHTML = '⏳ Saliendo...';
                                            boton.style.cursor = 'wait';
                                            boton.style.opacity = '0.75';
                                        }
                                    "
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        style="cursor: pointer !important;"
                                        class="shrink-0 rounded-xl bg-orange-600 px-4 py-2.5 text-xs font-black text-white
                                               shadow-md transition duration-150
                                               hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl
                                               active:scale-[0.96]"
                                    >
                                        🚪 Salida
                                    </button>

                                </form>

                            </div>

                        </article>

                    @endforeach

                </div>

            @endif

        </section>


        {{-- INGRESOS DEL DÍA --}}
        <section class="rounded-3xl border border-orange-900/40 bg-zinc-900 p-6 shadow-xl">

            <div class="mb-5">

                <h2 class="text-xl font-black text-white">
                    📋 Ingresos de hoy
                </h2>

                <p class="mt-1 text-sm text-zinc-500">
                    Registro completo de entradas y salidas del día.
                </p>

            </div>


            <div class="overflow-x-auto rounded-2xl border border-zinc-800">

                <table class="w-full min-w-[650px] border-collapse">

                    <thead class="bg-zinc-950">

                        <tr class="border-b border-zinc-800 text-left text-[11px] font-black uppercase tracking-wide text-zinc-500">

                            <th class="p-3">
                                Socio
                            </th>

                            <th class="p-3">
                                Ingreso
                            </th>

                            <th class="p-3">
                                Salida
                            </th>

                            <th class="p-3">
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-zinc-800">

                        @forelse($ingresosHoy as $item)

                            <tr class="transition duration-150 hover:bg-zinc-800/70">

                                <td class="p-3">

                                    <div class="flex items-center gap-2">

                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-800 text-sm">
                                            👤
                                        </div>

                                        <span class="font-bold text-white">
                                            {{ $item->cliente->nombre }}
                                        </span>

                                    </div>

                                </td>


                                <td class="p-3 text-sm text-zinc-400">

                                    <span class="font-black text-white">
                                        {{ $item->hora_ingreso->format('H:i') }}
                                    </span>
                                    hs

                                </td>


                                <td class="p-3 text-sm text-zinc-400">

                                    @if($item->hora_salida)

                                        <span class="font-black text-white">
                                            {{ $item->hora_salida->format('H:i') }}
                                        </span>
                                        hs

                                    @else

                                        <span class="text-zinc-600">
                                            —
                                        </span>

                                    @endif

                                </td>


                                <td class="p-3">

                                    @if($item->presente)

                                        <span class="inline-flex items-center rounded-full bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                                            🟢 Presente
                                        </span>

                                    @else

                                        <span class="inline-flex items-center rounded-full bg-zinc-800 px-3 py-1 text-xs font-bold text-zinc-400">
                                            ⚪ Retirado
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="p-8 text-center text-sm italic text-zinc-500"
                                >
                                    No hay asistencias registradas hoy.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </div>


    <style>
        /* Scroll fino para tablas en pantallas chicas */
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