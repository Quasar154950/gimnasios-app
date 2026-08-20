<x-layouts::app :title="__('Socios')">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 pb-10 sm:-m-6 sm:p-6">

        {{-- ENCABEZADO --}}
        <section
            class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl
                   transition duration-200 hover:-translate-y-0.5 hover:shadow-2xl md:p-6"
        >

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">

                    <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-blue-950 px-3 py-1 text-xs font-black uppercase text-blue-300">
                        👥 Gestión de socios
                    </div>

                    <h1 class="text-3xl font-black text-white">
                        Socios
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-400">
                        Administrá los socios registrados, sus cuotas, accesos, rutinas y movimientos.
                    </p>

                </div>


                {{-- ACCIONES PRINCIPALES --}}
                <div class="w-full overflow-x-auto overflow-y-hidden pb-2 md:w-auto">

                    <div class="flex min-w-max flex-nowrap items-center gap-3">

                        {{-- AGREGAR NUEVO SOCIO --}}
                        <a
                            href="{{ route('clientes.create') }}"
                            style="cursor: pointer !important;"
                            class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap
                                   rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white
                                   shadow-md transition duration-150
                                   hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl
                                   active:scale-[0.97]"
                        >
                            <span class="shrink-0 text-base">
                                ➕
                            </span>

                            <span class="whitespace-nowrap">
                                Agregar nuevo socio
                            </span>
                        </a>


                        {{-- SOCIOS INACTIVOS --}}
                        <a
                            href="{{ route('clientes.archivados') }}"
                            style="cursor: pointer !important;"
                            class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap
                                   rounded-xl border border-zinc-700 bg-zinc-800 px-5 py-3 text-sm font-bold text-white
                                   shadow-md transition duration-150
                                   hover:-translate-y-0.5 hover:border-zinc-500 hover:bg-zinc-700 hover:shadow-xl
                                   active:scale-[0.97]"
                        >
                            <span class="shrink-0 text-base">
                                📦
                            </span>

                            <span class="whitespace-nowrap">
                                Socios inactivos
                            </span>
                        </a>

                    </div>

                </div>

            </div>

        </section>


        {{-- LISTADO LIVEWIRE --}}
        <livewire:clientes.index-table />

    </div>


    {{-- SCROLL HORIZONTAL SUAVE --}}
    <style>

        /* Scroll horizontal fino para las acciones principales */
        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #52525b transparent;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 5px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #52525b;
            border-radius: 999px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #71717a;
        }

    </style>

</x-layouts::app>