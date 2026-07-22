<x-layouts::app :title="'Editar Novedad'">

    <div class="mx-auto max-w-4xl p-6">

        <div class="mb-6">

            <a
                href="{{ route('novedades.index') }}"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-200 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 dark:hover:text-white"
            >
                ← Volver a Novedades
            </a>

            <h1 class="mt-3 text-3xl font-black tracking-tight">
                ✏️ Editar publicación
            </h1>

            <p class="mt-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                Modificá la información que verán los socios en la aplicación.
            </p>

        </div>

        @if ($errors->any())

            <div class="mb-6 rounded-xl border border-red-800 bg-red-950 p-4 text-red-300">

                <p class="mb-2 font-semibold">
                    Revisá los siguientes campos:
                </p>

                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif

        <form
            action="{{ route('novedades.update', $novedad) }}"
            method="POST"
            class="space-y-6 rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow"
        >

            @csrf
            @method('PUT')

            <div>

                <label class="mb-2 block font-semibold text-white">
                    Tipo de publicación
                </label>

                <select
                    name="tipo"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >
                    <option value="novedad" @selected(old('tipo', $novedad->tipo) == 'novedad')>Novedad</option>
                    <option value="promocion" @selected(old('tipo', $novedad->tipo) == 'promocion')>Promoción</option>
                    <option value="evento" @selected(old('tipo', $novedad->tipo) == 'evento')>Evento</option>
                    <option value="consejo" @selected(old('tipo', $novedad->tipo) == 'consejo')>Consejo</option>
                </select>

            </div>

            <div>

                <label class="mb-2 block font-semibold text-white">
                    Título
                </label>

                <input
                    type="text"
                    name="titulo"
                    value="{{ old('titulo', $novedad->titulo) }}"
                    maxlength="255"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >

            </div>

            <div>

                <label class="mb-2 block font-semibold text-white">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    rows="7"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >{{ old('descripcion', $novedad->descripcion) }}</textarea>

            </div>

            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('novedades.index') }}"
                    class="rounded-xl border border-zinc-700 px-5 py-3 text-center font-semibold text-zinc-300 transition hover:border-zinc-500 hover:bg-zinc-800"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-orange-600 px-5 py-3 font-bold text-white shadow transition hover:bg-orange-700"
                >
                    💾 Guardar cambios
                </button>

            </div>

        </form>

    </div>

</x-layouts::app>
