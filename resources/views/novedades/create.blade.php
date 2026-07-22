<x-layouts.app>

    <div class="max-w-4xl mx-auto p-6">

        <div class="mb-6">
            <a
                href="{{ route('novedades.index') }}"
                class="text-sm text-zinc-400 hover:text-white"
            >
                ← Volver a Novedades
            </a>

            <h1 class="text-3xl font-bold mt-3">
                ➕ Nueva publicación
            </h1>

            <p class="text-zinc-400 mt-1">
                Creá una novedad para mostrar en la app de los socios.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-950 border border-red-800 p-4 text-red-300">
                <p class="font-semibold mb-2">
                    Revisá los siguientes campos:
                </p>

                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('novedades.store') }}"
            method="POST"
            class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow p-6 space-y-6"
        >

            @csrf

            <div>
                <label for="tipo" class="block font-semibold mb-2">
                    Tipo de publicación
                </label>

                <select
                    name="tipo"
                    id="tipo"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >
                    <option value="">Seleccionar tipo</option>

                    <option value="novedad" @selected(old('tipo') === 'novedad')>
                        Novedad
                    </option>

                    <option value="promocion" @selected(old('tipo') === 'promocion')>
                        Promoción
                    </option>

                    <option value="evento" @selected(old('tipo') === 'evento')>
                        Evento
                    </option>

                    <option value="consejo" @selected(old('tipo') === 'consejo')>
                        Consejo
                    </option>
                </select>
            </div>

            <div>
                <label for="titulo" class="block font-semibold mb-2">
                    Título
                </label>

                <input
                    type="text"
                    name="titulo"
                    id="titulo"
                    value="{{ old('titulo') }}"
                    maxlength="255"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    placeholder="Ejemplo: Nueva clase de funcional"
                    required
                >
            </div>

            <div>
                <label for="descripcion" class="block font-semibold mb-2">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    rows="7"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    placeholder="Escribí el contenido de la publicación..."
                    required
                >{{ old('descripcion') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">

                <a
                    href="{{ route('novedades.index') }}"
                    class="px-5 py-3 rounded-xl border border-zinc-700 text-zinc-300 hover:bg-zinc-800"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-orange-600 text-white font-semibold hover:bg-orange-700"
                >
                    Guardar publicación
                </button>

            </div>

        </form>

    </div>

</x-layouts.app>