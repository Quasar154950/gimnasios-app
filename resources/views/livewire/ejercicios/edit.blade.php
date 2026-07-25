<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">
            Editar ejercicio
        </h1>

        <p class="mt-1 text-sm text-zinc-400">
            Modificá los datos del ejercicio y guardá los cambios.
        </p>
    </div>

    <form
        wire:submit="guardar"
        class="space-y-6 rounded-xl border border-zinc-800 bg-zinc-900 p-6 shadow-lg"
    >

        <div class="grid gap-5 md:grid-cols-2">

            {{-- NOMBRE --}}
            <div>

                <label
                    for="nombre"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Nombre
                </label>

                <input
                    id="nombre"
                    type="text"
                    wire:model="nombre"
                    autocomplete="off"
                    class="w-full rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none placeholder:text-zinc-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                >

                @error('nombre')
                    <p class="mt-2 text-sm font-medium text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- GRUPO MUSCULAR --}}
            <div>

                <label
                    for="grupo-muscular"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Grupo muscular
                </label>

                <select
                    id="grupo-muscular"
                    wire:model="grupo_muscular"
                    class="w-full cursor-pointer rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                >
                    <option
                        value=""
                        class="bg-white text-zinc-950"
                    >
                        Seleccionar
                    </option>

                    @foreach ($gruposMusculares as $grupo)

                        <option
                            value="{{ $grupo }}"
                            class="bg-white text-zinc-950"
                        >
                            {{ $grupo }}
                        </option>

                    @endforeach
                </select>

                @error('grupo_muscular')
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
                wire:model="descripcion"
                rows="4"
                placeholder="Explicá brevemente cómo se realiza el ejercicio"
                class="w-full resize-y rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none placeholder:text-zinc-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
            ></textarea>

            @error('descripcion')
                <p class="mt-2 text-sm font-medium text-red-400">
                    {{ $message }}
                </p>
            @enderror

        </div>

        {{-- VIDEO --}}
        <div>

            <label
                for="video-url"
                class="mb-2 block text-sm font-semibold text-zinc-200"
            >
                Video
            </label>

            <input
                id="video-url"
                type="url"
                wire:model="video_url"
                placeholder="https://..."
                class="w-full rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none placeholder:text-zinc-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
            >

            <p class="mt-2 text-xs text-zinc-500">
                Podés agregar un enlace de YouTube u otra plataforma.
            </p>

            @error('video_url')
                <p class="mt-2 text-sm font-medium text-red-400">
                    {{ $message }}
                </p>
            @enderror

        </div>

        {{-- IMÁGENES --}}
        <div class="grid gap-6 md:grid-cols-2">

            {{-- IMAGEN ACTUAL --}}
            <div>

                <p class="mb-2 block text-sm font-semibold text-zinc-200">
                    Imagen actual
                </p>

                <div class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-800">

                    @if ($ejercicio->hasMedia('imagen'))

                        <img
                            src="{{ $ejercicio->getFirstMediaUrl('imagen') }}"
                            alt="{{ $ejercicio->nombre }}"
                            class="h-56 w-full object-cover"
                        >

                    @else

                        <div class="flex h-56 flex-col items-center justify-center text-zinc-500">

                            <span class="text-4xl">
                                🏋️
                            </span>

                            <span class="mt-2 text-sm">
                                Sin imagen
                            </span>

                        </div>

                    @endif

                </div>

            </div>

            {{-- REEMPLAZAR IMAGEN --}}
            <div>

                <p class="mb-2 block text-sm font-semibold text-zinc-200">
                    Reemplazar imagen
                </p>

                <label
                    for="imagen"
                    class="group flex min-h-56 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-blue-600 bg-blue-950/40 px-5 py-6 text-center shadow-inner transition hover:border-blue-400 hover:bg-blue-950/70"
                >

                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-900 text-2xl transition group-hover:scale-105">
                        📷
                    </div>

                    <span class="mt-4 text-sm font-bold text-blue-200">
                        Hacé clic para elegir otra imagen
                    </span>

                    <span class="mt-2 text-xs leading-5 text-zinc-400">
                        La imagen actual se reemplazará solamente cuando guardes los cambios.
                    </span>

                    <span class="mt-3 rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-xs font-semibold text-zinc-300">
                        JPG, JPEG, PNG o WEBP · Máximo 5 MB
                    </span>

                    <input
                        id="imagen"
                        type="file"
                        wire:model="imagen"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="sr-only"
                    >

                </label>

                <div
                    wire:loading
                    wire:target="imagen"
                    class="mt-3 w-full rounded-lg border border-blue-800 bg-blue-950 px-4 py-3 text-sm font-medium text-blue-200"
                >
                    Cargando imagen seleccionada...
                </div>

                @error('imagen')
                    <p class="mt-2 text-sm font-medium text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

        {{-- VISTA PREVIA DE NUEVA IMAGEN --}}
        @if ($imagen)

            <div class="rounded-xl border border-green-700 bg-green-950/40 p-4">

                <div class="mb-3 flex items-center gap-2">

                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-green-900 text-sm">
                        ✓
                    </span>

                    <div>
                        <p class="text-sm font-bold text-green-200">
                            Nueva imagen seleccionada
                        </p>

                        <p class="text-xs text-green-400">
                            Esta imagen reemplazará a la actual al guardar.
                        </p>
                    </div>

                </div>

                <img
                    src="{{ $imagen->temporaryUrl() }}"
                    alt="Vista previa de la nueva imagen"
                    class="h-64 w-full rounded-lg border border-green-800 object-cover"
                >

                <label
                    for="imagen"
                    class="mt-3 inline-flex cursor-pointer items-center rounded-lg border border-green-700 bg-green-950 px-4 py-2 text-sm font-semibold text-green-200 transition hover:bg-green-900"
                >
                    Elegir otra imagen
                </label>

            </div>

        @endif

        {{-- ACTIVO --}}
        <div class="rounded-lg border border-zinc-700 bg-zinc-800 p-4">

            <label
                for="activo"
                class="inline-flex cursor-pointer items-center gap-3"
            >

                <input
                    id="activo"
                    type="checkbox"
                    wire:model="activo"
                    class="h-5 w-5 cursor-pointer rounded border-zinc-500 bg-white text-blue-600 focus:ring-2 focus:ring-blue-500"
                >

                <span class="text-sm font-semibold text-zinc-200">
                    Ejercicio activo
                </span>

            </label>

            <p class="mt-1 pl-8 text-xs text-zinc-500">
                Los ejercicios inactivos no estarán disponibles para agregar a nuevas rutinas.
            </p>

        </div>

        {{-- ACCIONES --}}
        <div class="flex flex-wrap gap-3 border-t border-zinc-800 pt-5">

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="guardar"
                class="cursor-pointer rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span
                    wire:loading.remove
                    wire:target="guardar"
                >
                    Guardar cambios
                </span>

                <span
                    wire:loading
                    wire:target="guardar"
                >
                    Guardando...
                </span>
            </button>

            <a
                href="{{ route('ejercicios.index') }}"
                wire:navigate
                class="cursor-pointer rounded-lg border border-zinc-600 bg-zinc-800 px-5 py-2.5 text-sm font-semibold text-zinc-200 transition hover:bg-zinc-700"
            >
                Cancelar
            </a>

        </div>

    </form>

</div>
