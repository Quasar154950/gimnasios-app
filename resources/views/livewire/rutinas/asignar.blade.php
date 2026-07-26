<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

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

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

                <div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-green-400">
                        Asignación de entrenamiento
                    </p>

                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Asignar rutina
                    </h1>

                    <p class="mt-2 text-base font-semibold text-green-400">
                        {{ $rutina->nombre }}
                    </p>

                    <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-400">
                        Seleccioná uno o varios socios y definí las fechas de la
                        planificación.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-900 px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Socios seleccionados
                    </p>

                    <p class="mt-1 text-2xl font-black text-green-400">
                        {{ count($clientesSeleccionados) }}
                    </p>
                </div>

            </div>

        </div>

        {{-- MENSAJES DE ERROR --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-900/60 bg-red-950/40 px-5 py-4">

                <div class="flex items-start gap-3">

                    <svg
                        class="mt-0.5 h-5 w-5 shrink-0 text-red-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v5"/>
                        <path d="M12 17h.01"/>
                    </svg>

                    <div>
                        <p class="font-bold text-red-300">
                            Revisá los siguientes datos
                        </p>

                        <ul class="mt-2 space-y-1 text-sm text-red-200/80">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>

            </div>
        @endif

        <form wire:submit="guardar">

            <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">

                {{-- SOCIOS --}}
                <section class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900">

                    <div class="border-b border-zinc-800 p-5 sm:p-6">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <h2 class="text-xl font-bold text-white">
                                    Seleccionar socios
                                </h2>

                                <p class="mt-1 text-sm text-zinc-400">
                                    Podés buscar por nombre, DNI o correo electrónico.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">

                                <button
                                    type="button"
                                    wire:click="seleccionarTodos"
                                    class="rounded-xl border border-green-800 bg-green-950 px-4 py-2 text-sm font-bold text-green-300 transition hover:bg-green-900 active:scale-95"
                                >
                                    Seleccionar visibles
                                </button>

                                <button
                                    type="button"
                                    wire:click="quitarSeleccion"
                                    class="rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-bold text-zinc-300 transition hover:bg-zinc-700 active:scale-95"
                                >
                                    Quitar selección
                                </button>

                            </div>

                        </div>

                        <div class="relative mt-5">

                            <svg
                                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-zinc-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            >
                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-3.5-3.5"/>
                            </svg>

                            <input
                                type="search"
                                wire:model.live.debounce.350ms="buscar"
                                placeholder="Buscar socio..."
                                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 py-3 pl-12 pr-4 text-sm text-white outline-none transition placeholder:text-zinc-600 focus:border-green-600 focus:ring-2 focus:ring-green-900"
                            >

                        </div>

                    </div>

                    <div class="max-h-[620px] overflow-y-auto p-4 sm:p-5">

                        <div wire:loading.delay wire:target="buscar" class="mb-4">
                            <div class="rounded-xl border border-zinc-800 bg-zinc-950 px-4 py-3 text-sm font-semibold text-zinc-400">
                                Buscando socios...
                            </div>
                        </div>

                        <div class="space-y-3">

                            @forelse ($clientes as $cliente)

                                <label
                                    wire:key="cliente-asignacion-{{ $cliente->id }}"
                                    class="flex cursor-pointer items-start gap-4 rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-green-800 hover:bg-zinc-900"
                                >

                                    <input
                                        type="checkbox"
                                        wire:model.live="clientesSeleccionados"
                                        value="{{ $cliente->id }}"
                                        class="mt-1 h-5 w-5 rounded border-zinc-600 bg-zinc-900 text-green-600 focus:ring-green-600 focus:ring-offset-zinc-950"
                                    >

                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                                            <p class="truncate font-bold text-white">
                                                {{ $cliente->nombre }}
                                            </p>

                                            @if (in_array((string) $cliente->id, $clientesSeleccionados, true))
                                                <span class="w-fit rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                                                    Seleccionado
                                                </span>
                                            @endif

                                        </div>

                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500">

                                            @if ($cliente->dni)
                                                <span>
                                                    DNI: {{ $cliente->dni }}
                                                </span>
                                            @endif

                                            @if ($cliente->email)
                                                <span>
                                                    {{ $cliente->email }}
                                                </span>
                                            @endif

                                            @if ($cliente->telefono)
                                                <span>
                                                    {{ $cliente->telefono }}
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </label>

                            @empty

                                <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-950 px-6 py-12 text-center">

                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-900 text-zinc-500">
                                        <svg
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M19 8v6"/>
                                            <path d="M22 11h-6"/>
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 font-bold text-white">
                                        No se encontraron socios
                                    </h3>

                                    <p class="mt-2 text-sm text-zinc-500">
                                        Probá con otro nombre, DNI o correo electrónico.
                                    </p>

                                </div>

                            @endforelse

                        </div>

                    </div>

                </section>

                {{-- CONFIGURACIÓN --}}
                <aside class="space-y-6">

                    <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 sm:p-6">

                        <h2 class="text-xl font-bold text-white">
                            Configuración
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-zinc-400">
                            Definí la vigencia y el seguimiento de la rutina.
                        </p>

                        <div class="mt-6 space-y-5">

                            <div>
                                <label
                                    for="fechaInicio"
                                    class="mb-2 block text-sm font-bold text-zinc-300"
                                >
                                    Fecha de inicio
                                </label>

                                <input
                                    id="fechaInicio"
                                    type="date"
                                    wire:model="fechaInicio"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-white outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-900"
                                >
                            </div>

                            <div>
                                <label
                                    for="fechaFin"
                                    class="mb-2 block text-sm font-bold text-zinc-300"
                                >
                                    Fecha de finalización
                                </label>

                                <input
                                    id="fechaFin"
                                    type="date"
                                    wire:model="fechaFin"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-white outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-900"
                                >

                                <p class="mt-2 text-xs text-zinc-500">
                                    Es opcional. Podés dejarla sin definir.
                                </p>
                            </div>

                            <div>
                                <label
                                    for="fechaRevision"
                                    class="mb-2 block text-sm font-bold text-zinc-300"
                                >
                                    Fecha de revisión
                                </label>

                                <input
                                    id="fechaRevision"
                                    type="date"
                                    wire:model="fechaRevision"
                                    class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-white outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-900"
                                >

                                <p class="mt-2 text-xs text-zinc-500">
                                    Sirve para recordar cuándo evaluar el progreso.
                                </p>
                            </div>

                            <div>
                                <label
                                    for="observaciones"
                                    class="mb-2 block text-sm font-bold text-zinc-300"
                                >
                                    Observaciones
                                </label>

                                <textarea
                                    id="observaciones"
                                    wire:model="observaciones"
                                    rows="5"
                                    placeholder="Ejemplo: controlar técnica, ajustar cargas o evitar determinados ejercicios..."
                                    class="w-full resize-none rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-zinc-600 focus:border-green-600 focus:ring-2 focus:ring-green-900"
                                ></textarea>
                            </div>

                        </div>

                    </section>

                    <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 sm:p-6">

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="guardar"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-4 text-sm font-black text-white transition hover:bg-green-500 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg
                                wire:loading.remove
                                wire:target="guardar"
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
                                wire:target="guardar"
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
                                    d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"
                                />
                            </svg>

                            <span wire:loading.remove wire:target="guardar">
                                Guardar asignación
                            </span>

                            <span wire:loading wire:target="guardar">
                                Guardando...
                            </span>
                        </button>

                        <p class="mt-3 text-center text-xs leading-5 text-zinc-500">
                            Si un socio ya tiene esta rutina asignada, sus fechas y
                            observaciones serán actualizadas.
                        </p>

                    </section>

                    {{-- ASIGNACIONES ACTIVAS --}}
                    <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 sm:p-6">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <h2 class="font-bold text-white">
                                    Asignaciones activas
                                </h2>

                                <p class="mt-1 text-xs text-zinc-500">
                                    Socios que ya poseen esta rutina.
                                </p>
                            </div>

                            <span class="rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                                {{ $asignacionesActivas->count() }}
                            </span>

                        </div>

                        <div class="mt-5 space-y-3">

                            @forelse ($asignacionesActivas as $asignacion)

                                <div
                                    wire:key="asignacion-activa-{{ $asignacion->id }}"
                                    class="rounded-xl border border-zinc-800 bg-zinc-950 px-4 py-3"
                                >
                                    <p class="font-bold text-white">
                                        {{ $asignacion->cliente?->nombre ?? 'Socio no disponible' }}
                                    </p>

                                    <div class="mt-2 space-y-1 text-xs text-zinc-500">

                                        <p>
                                            Inicio:
                                            {{ $asignacion->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}
                                        </p>

                                        @if ($asignacion->fecha_fin)
                                            <p>
                                                Finalización:
                                                {{ $asignacion->fecha_fin->format('d/m/Y') }}
                                            </p>
                                        @endif

                                        @if ($asignacion->fecha_revision)
                                            <p>
                                                Revisión:
                                                {{ $asignacion->fecha_revision->format('d/m/Y') }}
                                            </p>
                                        @endif

                                    </div>
                                </div>

                            @empty

                                <div class="rounded-xl border border-dashed border-zinc-700 bg-zinc-950 px-4 py-6 text-center">
                                    <p class="text-sm font-semibold text-zinc-400">
                                        Esta rutina todavía no está asignada.
                                    </p>
                                </div>

                            @endforelse

                        </div>

                    </section>

                </aside>

            </div>

        </form>

    </div>

</div>