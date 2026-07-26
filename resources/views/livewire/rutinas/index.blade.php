<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="mb-1 text-sm font-medium uppercase tracking-widest text-green-400">
                    Entrenamiento
                </p>

                <h1 class="text-3xl font-bold tracking-tight text-white">
                    Rutinas
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Creá, organizá y asigná planes de entrenamiento a tus socios.
                </p>
            </div>

            <a
                href="/rutinas/nueva"
                wire:navigate
                class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 font-semibold text-zinc-950 shadow-lg shadow-green-950/40 transition hover:bg-green-400 active:scale-95"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 5v14M5 12h14"/>
                </svg>

                Nueva rutina
            </a>

        </div>

        {{-- MENSAJES --}}
        @if (session()->has('success'))
            <div class="mb-6 rounded-xl border border-green-800 bg-green-950/70 px-4 py-3 text-sm font-medium text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 rounded-xl border border-red-800 bg-red-950/70 px-4 py-3 text-sm font-medium text-red-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- FILTROS --}}
        <div class="mb-6 grid gap-4 rounded-2xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl sm:grid-cols-[1fr_220px]">

            <div>
                <label
                    for="buscar-rutina"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Buscar
                </label>

                <div class="relative">
                    <svg
                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-zinc-500"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>

                    <input
                        id="buscar-rutina"
                        type="search"
                        wire:model.live.debounce.350ms="buscar"
                        placeholder="Nombre, objetivo o descripción..."
                        class="w-full rounded-xl border border-zinc-300 bg-white py-3 pl-12 pr-4 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-green-500 focus:ring-4 focus:ring-green-500/20"
                    >
                </div>
            </div>

            <div>
                <label
                    for="estado-rutina"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Estado
                </label>

                <select
                    id="estado-rutina"
                    wire:model.live="estado"
                    class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-green-500 focus:ring-4 focus:ring-green-500/20"
                >
                    <option value="todos">Todas</option>
                    <option value="activas">Activas</option>
                    <option value="inactivas">Inactivas</option>
                </select>
            </div>

        </div>

        {{-- CARGANDO --}}
        <div
            wire:loading.flex
            wire:target="buscar,estado,cambiarEstado,eliminar"
            class="mb-5 items-center gap-3 rounded-xl border border-zinc-800 bg-zinc-900 px-4 py-3 text-sm text-zinc-300"
        >
            <svg
                class="h-5 w-5 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
            </svg>

            Actualizando rutinas...
        </div>

        {{-- LISTADO --}}
        @if ($rutinas->count())

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                @foreach ($rutinas as $rutina)

                    <article
                        wire:key="rutina-{{ $rutina->id }}"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-xl transition hover:-translate-y-1 hover:border-zinc-700 hover:shadow-2xl"
                    >

                        {{-- CABECERA --}}
                        <div class="border-b border-zinc-800 p-5">

                            <div class="mb-4 flex items-start justify-between gap-4">

                                <div class="min-w-0">
                                    <h2 class="truncate text-xl font-bold text-white">
                                        {{ $rutina->nombre }}
                                    </h2>

                                    @if ($rutina->objetivo)
                                        <p class="mt-1 truncate text-sm font-medium text-green-400">
                                            {{ $rutina->objetivo }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-zinc-500">
                                            Sin objetivo especificado
                                        </p>
                                    @endif
                                </div>

                                @if ($rutina->activa)
                                    <span class="shrink-0 rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                                        Activa
                                    </span>
                                @else
                                    <span class="shrink-0 rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1 text-xs font-bold text-zinc-400">
                                        Inactiva
                                    </span>
                                @endif

                            </div>

                            @if ($rutina->descripcion)
                                <p class="line-clamp-3 min-h-[60px] text-sm leading-5 text-zinc-400">
                                    {{ $rutina->descripcion }}
                                </p>
                            @else
                                <p class="min-h-[60px] text-sm italic leading-5 text-zinc-600">
                                    Esta rutina todavía no tiene una descripción.
                                </p>
                            @endif

                        </div>

                        {{-- INFORMACIÓN --}}
                        <div class="grid grid-cols-3 divide-x divide-zinc-800 border-b border-zinc-800 bg-zinc-950/50">

                            <div class="px-3 py-4 text-center">
                                <p class="text-xl font-bold text-white">
                                    {{ $rutina->dias_count }}
                                </p>

                                <p class="mt-1 text-xs uppercase tracking-wide text-zinc-500">
                                    Días
                                </p>
                            </div>

                            <div class="px-3 py-4 text-center">
                                <p class="text-xl font-bold text-white">
                                    {{ $rutina->asignaciones_count }}
                                </p>

                                <p class="mt-1 text-xs uppercase tracking-wide text-zinc-500">
                                    Socios
                                </p>
                            </div>

                            <div class="px-3 py-4 text-center">
                                <p class="text-xl font-bold text-white">
                                    {{ $rutina->duracion_semanas ?: '—' }}
                                </p>

                                <p class="mt-1 text-xs uppercase tracking-wide text-zinc-500">
                                    Semanas
                                </p>
                            </div>

                        </div>

                        {{-- ACCIONES --}}
<div class="mt-auto grid grid-cols-2 gap-3 p-5">

    <a
    href="{{ route('rutinas.show', $rutina) }}"
    wire:navigate
    class="col-span-2 inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-green-500 px-4 py-3 text-sm font-bold text-zinc-950 transition hover:bg-green-400 active:scale-95"
>
    <svg
        class="h-5 w-5"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M12 5v14M5 12h14"/>
    </svg>

    Armar rutina
</a>

    <a
        href="/rutinas/{{ $rutina->id }}/editar"
        wire:navigate
        class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:border-zinc-600 hover:bg-zinc-700 active:scale-95"
    >
        Editar
    </a>

    <button
        type="button"
        wire:click="cambiarEstado({{ $rutina->id }})"
        wire:loading.attr="disabled"
        wire:target="cambiarEstado({{ $rutina->id }})"
        class="inline-flex cursor-pointer items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition active:scale-95 disabled:cursor-wait disabled:opacity-60
            {{ $rutina->activa
                ? 'border-amber-800 bg-amber-950 text-amber-300 hover:bg-amber-900'
                : 'border-green-800 bg-green-950 text-green-300 hover:bg-green-900'
            }}"
    >
        {{ $rutina->activa ? 'Desactivar' : 'Activar' }}
    </button>

    <button
        type="button"
        wire:click="eliminar({{ $rutina->id }})"
        wire:confirm="¿Seguro que querés eliminar esta rutina? Esta acción no se puede deshacer."
        wire:loading.attr="disabled"
        wire:target="eliminar({{ $rutina->id }})"
        class="col-span-2 inline-flex cursor-pointer items-center justify-center rounded-xl border border-red-900 bg-red-950/80 px-4 py-2.5 text-sm font-semibold text-red-300 transition hover:bg-red-900 active:scale-95 disabled:cursor-wait disabled:opacity-60"
    >
        Eliminar rutina
    </button>

</div>

                    </article>

                @endforeach

            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-8">
                {{ $rutinas->links() }}
            </div>

        @else

            {{-- ESTADO VACÍO --}}
            <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-900 px-6 py-16 text-center shadow-xl">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-green-950 text-green-400">
                    <svg
                        class="h-8 w-8"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M6.5 6.5h11v11h-11z"/>
                        <path d="M9 3v3.5M15 3v3.5M9 17.5V21M15 17.5V21"/>
                        <path d="M3 9h3.5M17.5 9H21M3 15h3.5M17.5 15H21"/>
                    </svg>
                </div>

                <h2 class="mt-5 text-xl font-bold text-white">
                    No encontramos rutinas
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-400">
                    Creá tu primera rutina para comenzar a organizar días, ejercicios, series y repeticiones.
                </p>

                <a
                    href="/rutinas/nueva"
                    wire:navigate
                    class="mt-6 inline-flex cursor-pointer items-center justify-center rounded-xl bg-green-500 px-5 py-3 font-semibold text-zinc-950 transition hover:bg-green-400 active:scale-95"
                >
                    Crear primera rutina
                </a>

            </div>

        @endif

    </div>

</div>