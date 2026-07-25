<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Editar ejercicio
        </h1>

        <p class="mt-1 text-sm text-gray-600">
            Modificá los datos del ejercicio y guardá los cambios.
        </p>
    </div>

    <form
        wire:submit="guardar"
        class="space-y-6 rounded-xl bg-white p-6 shadow-sm"
    >

        <div class="grid gap-5 md:grid-cols-2">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Nombre
                </label>

                <input
                    type="text"
                    wire:model="nombre"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Grupo muscular
                </label>

                <select
                    wire:model="grupo_muscular"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">
                        Seleccionar
                    </option>

                    @foreach ($gruposMusculares as $grupo)
                        <option value="{{ $grupo }}">
                            {{ $grupo }}
                        </option>
                    @endforeach
                </select>

                @error('grupo_muscular')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Descripción
            </label>

            <textarea
                wire:model="descripcion"
                rows="4"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            ></textarea>

            @error('descripcion')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Video
            </label>

            <input
                type="url"
                wire:model="video_url"
                placeholder="https://..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >

            @error('video_url')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="grid gap-5 md:grid-cols-2">

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Imagen actual
                </label>

                @if ($ejercicio->hasMedia('imagen'))
                    <img
                        src="{{ $ejercicio->getFirstMediaUrl('imagen') }}"
                        alt="{{ $ejercicio->nombre }}"
                        class="h-48 w-full rounded-lg object-cover"
                    >
                @else
                    <div class="flex h-48 items-center justify-center rounded-lg bg-gray-100 text-sm text-gray-400">
                        Sin imagen
                    </div>
                @endif
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Reemplazar imagen
                </label>

                <input
                    type="file"
                    wire:model="imagen"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="block w-full text-sm text-gray-700"
                >

                <p class="mt-2 text-xs text-gray-500">
                    JPG, JPEG, PNG o WEBP. Máximo 5 MB.
                </p>

                @error('imagen')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @if ($imagen)
                    <div class="mt-4">
                        <p class="mb-2 text-sm font-medium text-gray-700">
                            Nueva imagen
                        </p>

                        <img
                            src="{{ $imagen->temporaryUrl() }}"
                            class="h-40 w-full rounded-lg object-cover"
                        >
                    </div>
                @endif
            </div>

        </div>

        <div>
            <label class="inline-flex items-center gap-2">
                <input
                    type="checkbox"
                    wire:model="activo"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                >

                <span class="text-sm font-medium text-gray-700">
                    Ejercicio activo
                </span>
            </label>
        </div>

        <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-5">

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="guardar"
                class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="guardar">
                    Guardar cambios
                </span>

                <span wire:loading wire:target="guardar">
                    Guardando...
                </span>
            </button>

            <a
                href="{{ route('ejercicios.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Cancelar
            </a>

        </div>

    </form>

</div>
