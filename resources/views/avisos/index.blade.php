<x-layouts::app :title="'Avisos'">

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
                        📢 Avisos
                    </h1>

                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                        Creá y programá avisos generales para todos los socios del gimnasio.
                    </p>

                </div>

                <div class="flex flex-wrap items-center gap-3">

                    {{-- CONTADOR --}}
                    <div
                        class="inline-flex items-center rounded-full border border-orange-500/30
                               bg-orange-500/20 px-4 py-2 text-xs font-black text-orange-500"
                    >
                        📋 {{ $avisos->count() }} avisos
                    </div>


                    {{-- NUEVO AVISO --}}
                    <a
                        href="{{ route('avisos.create') }}"
                        style="cursor: pointer !important;"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                               rounded-xl bg-orange-500 px-4 py-3 text-sm font-black text-white
                               shadow-md transition duration-150
                               hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-xl
                               active:scale-[0.96]"
                    >
                        ➕ Nuevo aviso
                    </a>

                </div>

            </div>

        </div>


        {{-- MENSAJES --}}
        @if(session('success'))

            <div
                class="rounded-xl border border-green-500/30 bg-green-500/20
                       px-5 py-4 text-sm font-bold text-green-700 shadow-sm
                       dark:text-green-300"
            >
                ✅ {{ session('success') }}
            </div>

        @endif


        {{-- LISTADO --}}
        @if($avisos->isEmpty())

            <div
                class="rounded-xl border border-stone-300 bg-stone-200 p-8 text-center shadow-sm
                       transition duration-200 hover:-translate-y-0.5 hover:shadow-lg
                       dark:border-stone-600 dark:bg-stone-800"
            >

                <div class="text-5xl">
                    📭
                </div>

                <h2 class="mt-4 text-xl font-black text-stone-900 dark:text-stone-100">
                    Todavía no hay avisos
                </h2>

                <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                    Creá el primer aviso para que luego pueda mostrarse en la aplicación.
                </p>

                <a
                    href="{{ route('avisos.create') }}"
                    style="cursor: pointer !important;"
                    class="mt-5 inline-flex items-center justify-center gap-2
                           rounded-xl bg-orange-500 px-4 py-3 text-sm font-black text-white
                           shadow-md transition duration-150
                           hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-xl
                           active:scale-[0.96]"
                >
                    ➕ Crear primer aviso
                </a>

            </div>

        @else

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">

                @foreach($avisos as $aviso)

                    @php

                        $ahora = now();

                        $publicacion = $aviso->fecha_publicacion;
                        $vencimiento = $aviso->fecha_vencimiento;

                        if (!$aviso->activo) {

                            $estadoTexto = 'Inactivo';
                            $estadoBadge = 'bg-stone-300 dark:bg-stone-700 text-stone-700 dark:text-stone-200 border border-stone-400 dark:border-stone-600';

                        } elseif ($publicacion && $publicacion->gt($ahora)) {

                            $estadoTexto = 'Programado';
                            $estadoBadge = 'bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-500/30';

                        } elseif ($vencimiento && $vencimiento->lt($ahora)) {

                            $estadoTexto = 'Vencido';
                            $estadoBadge = 'bg-stone-300 dark:bg-stone-700 text-stone-700 dark:text-stone-200 border border-stone-400 dark:border-stone-600';

                        } else {

                            $estadoTexto = 'Publicado';
                            $estadoBadge = 'bg-green-500/20 text-green-700 dark:text-green-300 border border-green-500/30';

                        }


                        switch ($aviso->prioridad) {

                            case 'urgente':
                                $prioridadTexto = 'Urgente';
                                $prioridadIcono = '🔴';
                                $prioridadBadge = 'bg-red-500/20 text-red-700 dark:text-red-300 border border-red-500/30';
                                break;

                            case 'importante':
                                $prioridadTexto = 'Importante';
                                $prioridadIcono = '🟡';
                                $prioridadBadge = 'bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border border-yellow-500/30';
                                break;

                            default:
                                $prioridadTexto = 'Informativo';
                                $prioridadIcono = '🟢';
                                $prioridadBadge = 'bg-green-500/20 text-green-700 dark:text-green-300 border border-green-500/30';
                                break;

                        }

                    @endphp


                    {{-- TARJETA AVISO --}}
                    <div
                        class="group rounded-xl border border-stone-300 bg-stone-200 p-5 shadow-sm
                               transition duration-200
                               hover:-translate-y-1 hover:border-orange-400 hover:shadow-xl
                               dark:border-stone-600 dark:bg-stone-800 dark:hover:border-orange-600"
                    >

                        {{-- CABECERA --}}
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    {{-- PRIORIDAD --}}
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1.5
                                               text-[11px] font-black shadow-sm {{ $prioridadBadge }}"
                                    >
                                        {{ $prioridadIcono }} {{ $prioridadTexto }}
                                    </span>


                                    {{-- ESTADO --}}
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1.5
                                               text-[11px] font-black shadow-sm {{ $estadoBadge }}"
                                    >
                                        {{ $estadoTexto }}
                                    </span>

                                </div>


                                <h2
                                    class="mt-3 break-words text-lg font-black
                                           text-stone-900 dark:text-stone-100"
                                >
                                    {{ $aviso->titulo }}
                                </h2>

                            </div>

                        </div>


                        {{-- MENSAJE --}}
                        <div
                            class="mt-4 rounded-xl border border-stone-300 bg-stone-100 p-4
                                   transition duration-200 group-hover:shadow-sm
                                   dark:border-stone-600 dark:bg-stone-700"
                        >

                            <p
                                class="whitespace-pre-line break-words text-sm leading-6
                                       text-stone-700 dark:text-stone-200"
                            >{{ $aviso->mensaje }}</p>

                        </div>


                        {{-- FECHAS --}}
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">

                            {{-- PUBLICACIÓN --}}
                            <div
                                class="rounded-xl border border-stone-300 bg-stone-100 p-4
                                       transition duration-200 group-hover:shadow-sm
                                       dark:border-stone-600 dark:bg-stone-700"
                            >

                                <p class="text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Publicación
                                </p>

                                <p class="mt-1 text-sm font-black text-stone-900 dark:text-stone-100">

                                    @if($publicacion)

                                        📅 {{ $publicacion->format('d/m/Y H:i') }}

                                    @else

                                        📅 Inmediata

                                    @endif

                                </p>

                            </div>


                            {{-- VENCIMIENTO --}}
                            <div
                                class="rounded-xl border border-stone-300 bg-stone-100 p-4
                                       transition duration-200 group-hover:shadow-sm
                                       dark:border-stone-600 dark:bg-stone-700"
                            >

                                <p class="text-[11px] font-black uppercase text-stone-500 dark:text-stone-300">
                                    Visible hasta
                                </p>

                                <p class="mt-1 text-sm font-black text-stone-900 dark:text-stone-100">

                                    @if($vencimiento)

                                        ⏰ {{ $vencimiento->format('d/m/Y H:i') }}

                                    @else

                                        ⏰ Sin vencimiento

                                    @endif

                                </p>

                            </div>

                        </div>


                        {{-- ACCIONES --}}
                        <div class="mt-5 flex flex-nowrap items-center gap-2 overflow-x-auto pb-1">

                            {{-- EDITAR --}}
                            <a
                                href="{{ route('avisos.edit', $aviso) }}"
                                style="cursor: pointer !important;"
                                class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap
                                       rounded-xl bg-orange-500 px-4 py-2.5 text-xs font-black text-white
                                       shadow-md transition duration-150
                                       hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-xl
                                       active:scale-[0.96]"
                            >
                                ✏️ Editar
                            </a>


                            {{-- ELIMINAR --}}
                            <form
                                method="POST"
                                action="{{ route('avisos.destroy', $aviso) }}"
                                onsubmit="return confirm('¿Seguro que querés eliminar este aviso?');"
                                class="shrink-0"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    style="cursor: pointer !important;"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                                           rounded-xl bg-red-600 px-4 py-2.5 text-xs font-black text-white
                                           shadow-md transition duration-150
                                           hover:-translate-y-0.5 hover:bg-red-500 hover:shadow-xl
                                           active:scale-[0.96]"
                                >
                                    🗑️ Eliminar
                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

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