<div class="min-h-screen bg-slate-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-4xl">

        {{-- ENCABEZADO --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="mb-1 text-sm font-medium text-emerald-400">
                    Biblioteca de ejercicios
                </p>

                <h1 class="text-2xl font-bold sm:text-3xl">
                    Nuevo ejercicio
                </h1>

                <p class="mt-2 text-sm text-slate-400">
                    Registrá un ejercicio para reutilizarlo posteriormente en las rutinas.
                </p>
            </div>

            <a
                href="{{ route('ejercicios.index') }}"
                wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-xl
                       border border-slate-700 bg-slate-900 px-4 py-2.5
                       text-sm font-semibold text-slate-200 transition
                       hover:border-slate-600 hover:bg-slate-800"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                Volver
            </a>
        </div>

        {{-- FORMULARIO --}}
        <form wire:submit="guardar">

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-xl">

                <div class="space-y-6 p-5 sm:p-7">

                    {{-- NOMBRE --}}
                    <div>
                        <label
                            for="nombre"
                            class="mb-2 block text-sm font-semibold text-slate-200"
                        >
                            Nombre del ejercicio
                            <span class="text-red-400">*</span>
                        </label>

                        <input
                            id="nombre"
                            type="text"
                            wire:model.blur="nombre"
                            placeholder="Ejemplo: Press de banca"
                            autofocus
                            class="w-full rounded-xl border bg-slate-950 px-4 py-3
                                   text-white outline-none transition
                                   placeholder:text-slate-600
                                   {{ $errors->has('nombre')
                                        ? 'border-red-500 focus:border-red-400 focus:ring-2 focus:ring-red-500/20'
                                        : 'border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20' }}"
                        >

                        @error('nombre')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- GRUPO MUSCULAR --}}
                    <div>
                        <label
                            for="grupo_muscular"
                            class="mb-2 block text-sm font-semibold text-slate-200"
                        >
                            Grupo muscular principal
                            <span class="text-red-400">*</span>
                        </label>

                        <select
                            id="grupo_muscular"
                            wire:model="grupo_muscular"
                            class="w-full rounded-xl border bg-slate-950 px-4 py-3
                                   text-white outline-none transition
                                   {{ $errors->has('grupo_muscular')
                                        ? 'border-red-500 focus:border-red-400 focus:ring-2 focus:ring-red-500/20'
                                        : 'border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20' }}"
                        >
                            <option value="">Seleccionar grupo muscular</option>
                            <option value="Pecho">Pecho</option>
                            <option value="Espalda">Espalda</option>
                            <option value="Hombros">Hombros</option>
                            <option value="Bíceps">Bíceps</option>
                            <option value="Tríceps">Tríceps</option>
                            <option value="Antebrazos">Antebrazos</option>
                            <option value="Abdominales">Abdominales</option>
                            <option value="Glúteos">Glúteos</option>
                            <option value="Cuádriceps">Cuádriceps</option>
                            <option value="Isquiotibiales">Isquiotibiales</option>
                            <option value="Gemelos">Gemelos</option>
                            <option value="Piernas">Piernas</option>
                            <option value="Cuerpo completo">Cuerpo completo</option>
                            <option value="Cardio">Cardio</option>
                            <option value="Movilidad">Movilidad</option>
                            <option value="Otro">Otro</option>
                        </select>

                        @error('grupo_muscular')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                     
                    {{-- IMAGEN --}}
<div>
    <label
        for="imagen"
        class="mb-2 block text-sm font-semibold text-slate-200"
    >
        Imagen del ejercicio
    </label>

    <input
        id="imagen"
        type="file"
        wire:model="imagen"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        class="block w-full cursor-pointer rounded-xl border
               border-slate-700 bg-slate-950 text-sm text-slate-300
               file:mr-4 file:border-0 file:bg-emerald-500
               file:px-4 file:py-3 file:font-semibold
               file:text-slate-950 hover:file:bg-emerald-400"
    >

    <p class="mt-2 text-xs text-slate-500">
        Formatos permitidos: JPG, PNG o WEBP. Máximo 5 MB.
    </p>

    @error('imagen')
        <p class="mt-2 text-sm text-red-400">
            {{ $message }}
        </p>
    @enderror

    <div
        wire:loading
        wire:target="imagen"
        class="mt-3 text-sm text-emerald-400"
    >
        Cargando imagen...
    </div>

    @if ($imagen)
        <div class="mt-4">
            <p class="mb-2 text-sm font-semibold text-slate-300">
                Vista previa
            </p>

            <img
                src="{{ $imagen->temporaryUrl() }}"
                alt="Vista previa del ejercicio"
                class="h-56 w-full rounded-xl border border-slate-700
                       bg-slate-950 object-contain"
            >
        </div>
    @endif
</div>
                    
                    {{-- DESCRIPCIÓN --}}
                    <div>
                        <label
                            for="descripcion"
                            class="mb-2 block text-sm font-semibold text-slate-200"
                        >
                            Descripción e indicaciones
                        </label>

                        <textarea
                            id="descripcion"
                            wire:model.blur="descripcion"
                            rows="6"
                            placeholder="Describí cómo se realiza el ejercicio, postura, recorrido y recomendaciones..."
                            class="w-full resize-y rounded-xl border bg-slate-950
                                   px-4 py-3 text-white outline-none transition
                                   placeholder:text-slate-600
                                   {{ $errors->has('descripcion')
                                        ? 'border-red-500 focus:border-red-400 focus:ring-2 focus:ring-red-500/20'
                                        : 'border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20' }}"
                        ></textarea>

                        @error('descripcion')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- VIDEO --}}
                    <div>
                        <label
                            for="video_url"
                            class="mb-2 block text-sm font-semibold text-slate-200"
                        >
                            Enlace del video
                        </label>

                        <input
                            id="video_url"
                            type="url"
                            wire:model.blur="video_url"
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="w-full rounded-xl border bg-slate-950 px-4 py-3
                                   text-white outline-none transition
                                   placeholder:text-slate-600
                                   {{ $errors->has('video_url')
                                        ? 'border-red-500 focus:border-red-400 focus:ring-2 focus:ring-red-500/20'
                                        : 'border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20' }}"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Opcional. Puede ser un enlace de YouTube u otra plataforma.
                        </p>

                        @error('video_url')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ESTADO --}}
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">

                        <label class="flex cursor-pointer items-start gap-3">

                            <input
                                type="checkbox"
                                wire:model="activo"
                                class="mt-1 h-5 w-5 rounded border-slate-600
                                       bg-slate-900 text-emerald-500
                                       focus:ring-emerald-500 focus:ring-offset-slate-950"
                            >

                            <span>
                                <span class="block font-semibold text-slate-200">
                                    Ejercicio activo
                                </span>

                                <span class="mt-1 block text-sm text-slate-500">
                                    Los ejercicios activos estarán disponibles para incorporarlos a las rutinas.
                                </span>
                            </span>

                        </label>

                        @error('activo')
                            <p class="mt-2 text-sm text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="flex flex-col-reverse gap-3 border-t border-slate-800
                            bg-slate-950/40 p-5 sm:flex-row sm:justify-end sm:p-7">

                    <a
                        href="{{ route('ejercicios.index') }}"
                        wire:navigate
                        class="inline-flex items-center justify-center rounded-xl
                               border border-slate-700 px-5 py-3 text-sm
                               font-semibold text-slate-300 transition
                               hover:bg-slate-800"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="guardar"
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                               bg-emerald-500 px-5 py-3 text-sm font-bold text-slate-950
                               transition hover:bg-emerald-400
                               disabled:cursor-not-allowed disabled:opacity-60"
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
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
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
                            ></circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            ></path>
                        </svg>

                        <span wire:loading.remove wire:target="guardar">
                            Guardar ejercicio
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
