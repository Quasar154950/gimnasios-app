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
            enctype="multipart/form-data"
            class="space-y-6 rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow"
        >

            @csrf
            @method('PUT')

            <div>

                <label
                    for="tipo"
                    class="mb-2 block font-semibold text-white"
                >
                    Tipo de publicación
                </label>

                <select
                    name="tipo"
                    id="tipo"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >
                    <option
                        value="novedad"
                        @selected(old('tipo', $novedad->tipo) === 'novedad')
                    >
                        Novedad
                    </option>

                    <option
                        value="promocion"
                        @selected(old('tipo', $novedad->tipo) === 'promocion')
                    >
                        Promoción
                    </option>

                    <option
                        value="evento"
                        @selected(old('tipo', $novedad->tipo) === 'evento')
                    >
                        Evento
                    </option>

                    <option
                        value="consejo"
                        @selected(old('tipo', $novedad->tipo) === 'consejo')
                    >
                        Consejo
                    </option>
                </select>

                @error('tipo')
                    <p class="mt-2 text-sm font-semibold text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div>

                <label
                    for="titulo"
                    class="mb-2 block font-semibold text-white"
                >
                    Título
                </label>

                <input
                    type="text"
                    name="titulo"
                    id="titulo"
                    value="{{ old('titulo', $novedad->titulo) }}"
                    maxlength="255"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >

                @error('titulo')
                    <p class="mt-2 text-sm font-semibold text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div>

                <label
                    for="descripcion"
                    class="mb-2 block font-semibold text-white"
                >
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    rows="7"
                    class="w-full rounded-xl border-zinc-700 bg-zinc-950 text-white"
                    required
                >{{ old('descripcion', $novedad->descripcion) }}</textarea>

                @error('descripcion')
                    <p class="mt-2 text-sm font-semibold text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div>

                <label
                    for="imagen"
                    class="mb-2 block font-semibold text-white"
                >
                    Imagen opcional
                </label>

                @if ($novedad->imagen)

                    <div
                        id="imagenActual"
                        class="mb-4 overflow-hidden rounded-2xl border border-zinc-700 bg-zinc-950"
                    >
                        <img
                            src="{{ $novedad->imagen }}"
                            alt="{{ $novedad->titulo }}"
                            class="max-h-96 w-full bg-zinc-950 object-contain"
                        >

                        <div class="flex items-center justify-between gap-3 p-4">

                            <p class="text-sm font-semibold text-zinc-300">
                                Imagen actual
                            </p>

                            <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold text-red-400">

                                <input
                                    type="checkbox"
                                    name="eliminar_imagen"
                                    id="eliminar_imagen"
                                    value="1"
                                    @checked(old('eliminar_imagen'))
                                    class="rounded border-zinc-600 bg-zinc-900 text-red-600 focus:ring-red-600"
                                >

                                Eliminar imagen

                            </label>

                        </div>

                    </div>

                @endif

                <input
                    type="file"
                    name="imagen"
                    id="imagen"
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full cursor-pointer rounded-xl border border-zinc-700 bg-zinc-950 text-sm text-zinc-300 file:mr-4 file:border-0 file:bg-orange-600 file:px-4 file:py-3 file:font-bold file:text-white hover:file:bg-orange-700"
                >

                <p class="mt-2 text-sm text-zinc-400">
                    Seleccioná una imagen solo si querés agregarla o reemplazar la actual.
                    Formatos: JPG, PNG o WEBP. Máximo: 5 MB.
                </p>

                @error('imagen')
                    <p class="mt-2 text-sm font-semibold text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                <div
                    id="contenedorVistaPrevia"
                    class="mt-4 hidden"
                >
                    <p class="mb-2 text-sm font-semibold text-zinc-300">
                        Vista previa de la nueva imagen
                    </p>

                    <img
                        id="vistaPrevia"
                        src=""
                        alt="Vista previa de la imagen"
                        class="max-h-[32rem] w-full rounded-2xl border border-zinc-700 bg-zinc-950 object-contain"
                    >
                </div>

            </div>

            <div class="grid gap-4 sm:grid-cols-2">

                <label
                    for="destacado"
                    class="flex cursor-pointer items-start gap-3 rounded-xl border border-zinc-700 bg-zinc-950 p-4 transition hover:border-orange-600"
                >
                    <input
                        type="checkbox"
                        name="destacado"
                        id="destacado"
                        value="1"
                        @checked(old('destacado', $novedad->destacado))
                        class="mt-1 rounded border-zinc-600 bg-zinc-900 text-orange-600 focus:ring-orange-600"
                    >

                    <span>
                        <span class="block font-bold text-white">
                            ⭐ Publicación destacada
                        </span>

                        <span class="mt-1 block text-sm text-zinc-400">
                            Aparecerá primero y tendrá mayor visibilidad.
                        </span>
                    </span>
                </label>

                <label
                    for="activo"
                    class="flex cursor-pointer items-start gap-3 rounded-xl border border-zinc-700 bg-zinc-950 p-4 transition hover:border-orange-600"
                >
                    <input
                        type="checkbox"
                        name="activo"
                        id="activo"
                        value="1"
                        @checked(old('activo', $novedad->activo))
                        class="mt-1 rounded border-zinc-600 bg-zinc-900 text-orange-600 focus:ring-orange-600"
                    >

                    <span>
                        <span class="block font-bold text-white">
                            👁 Publicación visible
                        </span>

                        <span class="mt-1 block text-sm text-zinc-400">
                            Los socios podrán verla en la aplicación.
                        </span>
                    </span>
                </label>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputImagen = document.getElementById('imagen');
            const vistaPrevia = document.getElementById('vistaPrevia');
            const contenedor = document.getElementById('contenedorVistaPrevia');
            const eliminarImagen = document.getElementById('eliminar_imagen');

            inputImagen.addEventListener('change', function (event) {
                const archivo = event.target.files[0];

                if (!archivo) {
                    vistaPrevia.src = '';
                    contenedor.classList.add('hidden');
                    return;
                }

                const lector = new FileReader();

                lector.onload = function (evento) {
                    vistaPrevia.src = evento.target.result;
                    contenedor.classList.remove('hidden');
                };

                lector.readAsDataURL(archivo);

                if (eliminarImagen) {
                    eliminarImagen.checked = false;
                }
            });

            if (eliminarImagen) {
                eliminarImagen.addEventListener('change', function () {
                    if (this.checked) {
                        inputImagen.value = '';
                        vistaPrevia.src = '';
                        contenedor.classList.add('hidden');
                    }
                });
            }
        });
    </script>

</x-layouts::app>
