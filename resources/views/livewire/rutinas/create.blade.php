<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-4xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="mb-1 text-sm font-medium uppercase tracking-widest text-green-400">
                    Entrenamiento
                </p>

                <h1 class="text-3xl font-bold tracking-tight text-white">
                    Nueva rutina
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Creá la información general del plan de entrenamiento.
                </p>
            </div>

            <a
                href="{{ route('rutinas.index') }}"
                wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-sm font-semibold text-zinc-200 transition hover:bg-zinc-800 active:scale-95"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m15 18-6-6 6-6"/>
                </svg>

                Volver a rutinas
            </a>

        </div>

        <form wire:submit="guardar">

            <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-2xl">

                {{-- CABECERA DEL FORMULARIO --}}
                <div class="border-b border-zinc-800 bg-zinc-900 px-6 py-5">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-950 text-green-400">
                            <svg
                                class="h-6 w-6"
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

                        <div>
                            <h2 class="text-lg font-bold text-white">
                                Datos de la rutina
                            </h2>

                            <p class="mt-1 text-sm text-zinc-400">
                                Más adelante agregaremos los días y ejercicios.
                            </p>
                        </div>

                    </div>

                </div>

                {{-- CAMPOS --}}
                <div class="space-y-6 p-6 sm:p-8">

                    {{-- NOMBRE --}}
                    <div>
                        <label
                            for="nombre"
                            class="mb-2 block text-sm font-semibold text-zinc-200"
                        >
                            Nombre de la rutina
                            <span class="text-red-400">*</span>
                        </label>

                        <input
                            id="nombre"
                            type="text"
                            wire:model.blur="nombre"
                            placeholder="Ejemplo: Hipertrofia intermedia"
                            autofocus
                            class="w-full rounded-xl border bg-white px-4 py-3 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:ring-4
                                @error('nombre')
                                    border-red-500 focus:border-red-500 focus:ring-red-500/20
                                @else
                                    border-zinc-300 focus:border-green-500 focus:ring-green-500/20
                                @enderror"
                        >

                        @error('nombre')
                            <p class="mt-2 text-sm font-medium text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- OBJETIVO Y DURACIÓN --}}
                    <div class="grid gap-6 md:grid-cols-2">

                        <div>
                            <label
                                for="objetivo"
                                class="mb-2 block text-sm font-semibold text-zinc-200"
                            >
                                Objetivo
                            </label>

                            <input
                                id="objetivo"
                                type="text"
                                wire:model.blur="objetivo"
                                placeholder="Ejemplo: Ganancia muscular"
                                class="w-full rounded-xl border bg-white px-4 py-3 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:ring-4
                                    @error('objetivo')
                                        border-red-500 focus:border-red-500 focus:ring-red-500/20
                                    @else
                                        border-zinc-300 focus:border-green-500 focus:ring-green-500/20
                                    @enderror"
                            >

                            @error('objetivo')
                                <p class="mt-2 text-sm font-medium text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="duracion-semanas"
                                class="mb-2 block text-sm font-semibold text-zinc-200"
                            >
                                Duración estimada
                            </label>

                            <div class="relative">
                                <input
                                    id="duracion-semanas"
                                    type="number"
                                    min="1"
                                    max="52"
                                    wire:model.blur="duracion_semanas"
                                    placeholder="8"
                                    class="w-full rounded-xl border bg-white px-4 py-3 pr-24 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:ring-4
                                        @error('duracion_semanas')
                                            border-red-500 focus:border-red-500 focus:ring-red-500/20
                                        @else
                                            border-zinc-300 focus:border-green-500 focus:ring-green-500/20
                                        @enderror"
                                >

                                <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-zinc-500">
                                    semanas
                                </span>
                            </div>

                            @error('duracion_semanas')
                                <p class="mt-2 text-sm font-medium text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div>
                        <label
                            for="descripcion"
                            class="mb-2 block text-sm font-semibold text-zinc-200"
                        >
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            wire:model.blur="descripcion"
                            rows="5"
                            placeholder="Describí brevemente la rutina, su enfoque o cualquier indicación general..."
                            class="w-full resize-y rounded-xl border bg-white px-4 py-3 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:ring-4
                                @error('descripcion')
                                    border-red-500 focus:border-red-500 focus:ring-red-500/20
                                @else
                                    border-zinc-300 focus:border-green-500 focus:ring-green-500/20
                                @enderror"
                        ></textarea>

                        <div class="mt-2 flex items-start justify-between gap-4">

                            @error('descripcion')
                                <p class="text-sm font-medium text-red-400">
                                    {{ $message }}
                                </p>
                            @else
                                <p class="text-xs text-zinc-500">
                                    Podrás modificar esta información más adelante.
                                </p>
                            @enderror

                            <span class="shrink-0 text-xs text-zinc-500">
                                Máximo 2000 caracteres
                            </span>

                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/70 p-4">

                        <label class="flex cursor-pointer items-start gap-4">

                            <input
                                type="checkbox"
                                wire:model="activa"
                                class="mt-1 h-5 w-5 cursor-pointer rounded border-zinc-600 bg-zinc-800 text-green-500 focus:ring-green-500 focus:ring-offset-zinc-950"
                            >

                            <span>
                                <span class="block text-sm font-semibold text-white">
                                    Rutina activa
                                </span>

                                <span class="mt-1 block text-sm leading-5 text-zinc-400">
                                    Las rutinas activas podrán asignarse a los socios. Podés desactivarla cuando quieras.
                                </span>
                            </span>

                        </label>

                        @error('activa')
                            <p class="mt-2 text-sm font-medium text-red-400">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    {{-- INFORMACIÓN --}}
                    <div class="rounded-xl border border-blue-900 bg-blue-950/40 px-4 py-4">

                        <div class="flex items-start gap-3">

                            <svg
                                class="mt-0.5 h-5 w-5 shrink-0 text-blue-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 11v5"/>
                                <path d="M12 8h.01"/>
                            </svg>

                            <p class="text-sm leading-6 text-blue-200">
                                En este paso guardaremos solamente los datos generales. Luego podrás organizar los días y agregar ejercicios desde la Biblioteca.
                            </p>

                        </div>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="flex flex-col-reverse gap-3 border-t border-zinc-800 bg-zinc-950/50 px-6 py-5 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('rutinas.index') }}"
                        wire:navigate
                        class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700 active:scale-95"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="guardar"
                        class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-green-500 px-6 py-3 font-semibold text-zinc-950 shadow-lg shadow-green-950/40 transition hover:bg-green-400 active:scale-95 disabled:cursor-wait disabled:opacity-60"
                    >
                        <svg
                            wire:loading.remove
                            wire:target="guardar"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M5 12l4 4L19 6"/>
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
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>

                        <span wire:loading.remove wire:target="guardar">
                            Crear rutina
                        </span>

                        <span wire:loading wire:target="guardar">
                            Guardando...
                        </span>
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>