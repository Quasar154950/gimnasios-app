<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-6xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8">

            <a
                href="{{ route('rutinas.show', $rutina) }}"
                wire:navigate
                class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-zinc-400 transition hover:text-white"
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
                    <path d="m15 18-6-6 6-6"/>
                </svg>

                Volver a la rutina
            </a>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-green-400">
                        {{ $rutina->nombre }}
                    </p>

                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        {{ $dia->nombre }}
                    </h1>

                    @if ($dia->descripcion)
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-400">
                            {{ $dia->descripcion }}
                        </p>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="abrirBiblioteca"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-green-500 active:scale-95"
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

                    Agregar ejercicio
                </button>

            </div>

        </div>

        {{-- MENSAJES --}}
        @if (session()->has('mensaje'))
            <div class="mb-6 rounded-xl border border-green-800 bg-green-950/60 px-4 py-3 text-sm font-semibold text-green-300">
                {{ session('mensaje') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 rounded-xl border border-red-800 bg-red-950/60 px-4 py-3 text-sm font-semibold text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- EJERCICIOS DEL DÍA --}}
        <div class="space-y-4">

            @forelse ($ejerciciosDelDia as $rutinaEjercicio)

                <article
                    wire:key="rutina-ejercicio-{{ $rutinaEjercicio->id }}"
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl"
                >

                    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                        <div class="flex items-start gap-4">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-green-950 text-lg font-black text-green-400">
                                {{ $rutinaEjercicio->orden }}
                            </div>

                            <div>
                                <h2 class="text-xl font-bold text-white">
                                    {{ $rutinaEjercicio->nombre_ejercicio }}
                                </h2>

                                @if ($rutinaEjercicio->ejercicioBiblioteca?->grupo_muscular)
                                    <p class="mt-1 text-sm font-semibold text-green-400">
                                        {{ $rutinaEjercicio->ejercicioBiblioteca->grupo_muscular }}
                                    </p>
                                @endif

                                @if ($rutinaEjercicio->observaciones)
                                    <p class="mt-2 max-w-xl text-sm leading-6 text-zinc-400">
                                        {{ $rutinaEjercicio->observaciones }}
                                    </p>
                                @endif
                            </div>

                        </div>

                        <div class="flex flex-wrap items-center gap-3">

                            <div class="rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2 text-center">
                                <p class="text-xs uppercase tracking-wide text-zinc-500">
                                    Series
                                </p>

                                <p class="mt-1 font-bold text-white">
                                    {{ $rutinaEjercicio->series }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2 text-center">
                                <p class="text-xs uppercase tracking-wide text-zinc-500">
                                    Repeticiones
                                </p>

                                <p class="mt-1 font-bold text-white">
                                    {{ $rutinaEjercicio->repeticiones }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2 text-center">
                                <p class="text-xs uppercase tracking-wide text-zinc-500">
                                    Descanso
                                </p>

                                <p class="mt-1 font-bold text-white">
                                    {{ $rutinaEjercicio->descanso_segundos }} s
                                </p>
                            </div>

                            @if ($rutinaEjercicio->peso !== null)
                                <div class="rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2 text-center">
                                    <p class="text-xs uppercase tracking-wide text-zinc-500">
                                        Peso
                                    </p>

                                    <p class="mt-1 font-bold text-white">
                                        {{ $rutinaEjercicio->peso }} kg
                                    </p>
                                </div>
                            @endif

                            {{-- EDITAR --}}
                            <button
                                type="button"
                                wire:click="editarEjercicio({{ $rutinaEjercicio->id }})"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-blue-800 bg-blue-950/40 text-blue-400 transition hover:bg-blue-950 active:scale-95"
                                title="Editar ejercicio"
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
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                                </svg>
                            </button>

                            {{-- ELIMINAR --}}
                            <button
                                type="button"
                                wire:click="eliminarEjercicio({{ $rutinaEjercicio->id }})"
                                wire:confirm="¿Seguro que querés quitar este ejercicio del día?"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-red-900 bg-red-950/40 text-red-400 transition hover:bg-red-950 active:scale-95"
                                title="Eliminar ejercicio"
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
                                    <path d="M3 6h18"/>
                                    <path d="M8 6V4h8v2"/>
                                    <path d="M19 6l-1 14H6L5 6"/>
                                </svg>
                            </button>

                        </div>

                    </div>

                </article>

            @empty

                <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-900/60 px-6 py-14 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 text-zinc-400">
                        <svg
                            class="h-7 w-7"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                    </div>

                    <h2 class="mt-5 text-xl font-bold">
                        Todavía no hay ejercicios
                    </h2>

                    <p class="mt-2 text-sm text-zinc-400">
                        Agregá ejercicios desde la biblioteca para comenzar a organizar este día.
                    </p>

                    <button
                        type="button"
                        wire:click="abrirBiblioteca"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-green-500 active:scale-95"
                    >
                        Abrir biblioteca
                    </button>

                </div>

            @endforelse

        </div>

        {{-- MODAL EDITAR EJERCICIO --}}
        @if ($mostrarEditor)

            <div
                class="fixed inset-0 z-[60] overflow-y-auto bg-black/80 px-4 py-8"
                wire:key="modal-editar-ejercicio"
            >

                <div class="mx-auto max-w-2xl overflow-hidden rounded-2xl border border-zinc-700 bg-zinc-900 shadow-2xl">

                    <div class="flex items-center justify-between border-b border-zinc-800 px-5 py-4">

                        <div>
                            <h2 class="text-xl font-bold text-white">
                                Editar ejercicio
                            </h2>

                            <p class="mt-1 text-sm text-zinc-400">
                                Configurá las indicaciones para este ejercicio.
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="cerrarEditor"
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-800 text-zinc-400 transition hover:bg-zinc-700 hover:text-white"
                            title="Cerrar"
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
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
                        </button>

                    </div>

                    <form wire:submit="guardarEjercicio">

                        <div class="grid gap-5 p-5 sm:grid-cols-2">

                            {{-- SERIES --}}
                            <div>
                                <label
                                    for="series"
                                    class="mb-2 block text-sm font-semibold text-zinc-300"
                                >
                                    Series
                                </label>

                                <input
                                    id="series"
                                    type="number"
                                    min="1"
                                    max="50"
                                    wire:model="series"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition focus:border-green-600"
                                >

                                @error('series')
                                    <p class="mt-2 text-sm font-semibold text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- REPETICIONES --}}
                            <div>
                                <label
                                    for="repeticiones"
                                    class="mb-2 block text-sm font-semibold text-zinc-300"
                                >
                                    Repeticiones
                                </label>

                                <input
                                    id="repeticiones"
                                    type="number"
                                    min="1"
                                    max="500"
                                    wire:model="repeticiones"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition focus:border-green-600"
                                >

                                @error('repeticiones')
                                    <p class="mt-2 text-sm font-semibold text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- PESO --}}
                            <div>
                                <label
                                    for="peso"
                                    class="mb-2 block text-sm font-semibold text-zinc-300"
                                >
                                    Peso en kilogramos
                                </label>

                                <input
                                    id="peso"
                                    type="number"
                                    min="0"
                                    max="9999"
                                    step="0.01"
                                    wire:model="peso"
                                    placeholder="Opcional"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition placeholder:text-zinc-600 focus:border-green-600"
                                >

                                <p class="mt-2 text-xs text-zinc-500">
                                    Puede dejarse vacío si no corresponde.
                                </p>

                                @error('peso')
                                    <p class="mt-2 text-sm font-semibold text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- DESCANSO --}}
                            <div>
                                <label
                                    for="descansoSegundos"
                                    class="mb-2 block text-sm font-semibold text-zinc-300"
                                >
                                    Descanso en segundos
                                </label>

                                <input
                                    id="descansoSegundos"
                                    type="number"
                                    min="0"
                                    max="3600"
                                    wire:model="descansoSegundos"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition focus:border-green-600"
                                >

                                @error('descansoSegundos')
                                    <p class="mt-2 text-sm font-semibold text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- OBSERVACIONES --}}
                            <div class="sm:col-span-2">
                                <label
                                    for="observaciones"
                                    class="mb-2 block text-sm font-semibold text-zinc-300"
                                >
                                    Observaciones
                                </label>

                                <textarea
                                    id="observaciones"
                                    rows="4"
                                    maxlength="1000"
                                    wire:model="observaciones"
                                    placeholder="Ejemplo: mantener la espalda apoyada y controlar el movimiento."
                                    class="w-full resize-none rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition placeholder:text-zinc-600 focus:border-green-600"
                                ></textarea>

                                @error('observaciones')
                                    <p class="mt-2 text-sm font-semibold text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-zinc-800 px-5 py-4 sm:flex-row sm:justify-end">

                            <button
                                type="button"
                                wire:click="cerrarEditor"
                                class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 px-5 py-3 text-sm font-bold text-zinc-200 transition hover:bg-zinc-700 active:scale-95"
                            >
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="guardarEjercicio"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-green-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg
                                    wire:loading.remove
                                    wire:target="guardarEjercicio"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>

                                <svg
                                    wire:loading
                                    wire:target="guardarEjercicio"
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
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"
                                    ></path>
                                </svg>

                                <span wire:loading.remove wire:target="guardarEjercicio">
                                    Guardar cambios
                                </span>

                                <span wire:loading wire:target="guardarEjercicio">
                                    Guardando...
                                </span>
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        @endif

        {{-- BIBLIOTECA --}}
        @if ($mostrarBiblioteca)

            <div class="fixed inset-0 z-50 overflow-y-auto bg-black/80 px-4 py-8">

                <div class="mx-auto max-w-5xl overflow-hidden rounded-2xl border border-zinc-700 bg-zinc-900 shadow-2xl">

                    <div class="flex items-center justify-between border-b border-zinc-800 px-5 py-4">

                        <div>
                            <h2 class="text-xl font-bold text-white">
                                Biblioteca de ejercicios
                            </h2>

                            <p class="mt-1 text-sm text-zinc-400">
                                Seleccioná los ejercicios para {{ $dia->nombre }}.
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="cerrarBiblioteca"
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-800 text-zinc-400 transition hover:bg-zinc-700 hover:text-white"
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
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
                        </button>

                    </div>

                    <div class="grid gap-4 border-b border-zinc-800 p-5 md:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-zinc-300">
                                Buscar ejercicio
                            </label>

                            <input
                                type="text"
                                wire:model.live.debounce.300ms="buscar"
                                placeholder="Ejemplo: Press banca"
                                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition placeholder:text-zinc-600 focus:border-green-600"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-zinc-300">
                                Grupo muscular
                            </label>

                            <select
                                wire:model.live="grupoMuscular"
                                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none transition focus:border-green-600"
                            >
                                <option value="">
                                    Todos los grupos
                                </option>

                                @foreach ($gruposMusculares as $grupo)
                                    <option value="{{ $grupo }}">
                                        {{ $grupo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="max-h-[60vh] overflow-y-auto p-5">

                        <div class="grid gap-4 md:grid-cols-2">

                            @forelse ($biblioteca as $ejercicio)

                                @php
                                    $yaAgregado = in_array(
                                        $ejercicio->id,
                                        $ejerciciosAgregadosIds
                                    );
                                @endphp

                                <article
                                    wire:key="biblioteca-ejercicio-{{ $ejercicio->id }}"
                                    class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4"
                                >

                                    <div class="flex items-start justify-between gap-4">

                                        <div>
                                            <h3 class="font-bold text-white">
                                                {{ $ejercicio->nombre }}
                                            </h3>

                                            <p class="mt-1 text-sm font-semibold text-green-400">
                                                {{ $ejercicio->grupo_muscular }}
                                            </p>

                                            @if ($ejercicio->descripcion)
                                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-zinc-400">
                                                    {{ $ejercicio->descripcion }}
                                                </p>
                                            @endif
                                        </div>

                                        @if ($yaAgregado)
                                            <span class="shrink-0 rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                                                Agregado
                                            </span>
                                        @endif

                                    </div>

                                    <button
                                        type="button"
                                        wire:click="agregarEjercicio({{ $ejercicio->id }})"
                                        @disabled($yaAgregado)
                                        class="mt-4 inline-flex w-full items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold transition active:scale-95
                                            {{ $yaAgregado
                                                ? 'cursor-not-allowed border border-zinc-700 bg-zinc-800 text-zinc-500'
                                                : 'bg-green-600 text-white hover:bg-green-500'
                                            }}"
                                    >
                                        {{ $yaAgregado
                                            ? 'Ya está en el día'
                                            : 'Agregar ejercicio'
                                        }}
                                    </button>

                                </article>

                            @empty

                                <div class="col-span-full rounded-2xl border border-dashed border-zinc-700 p-10 text-center text-zinc-400">
                                    No se encontraron ejercicios con esos filtros.
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>