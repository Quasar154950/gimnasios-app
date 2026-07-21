<x-layouts::app :title="'Nuevo aviso'">

    <div class="-m-4 min-h-screen bg-slate-950 p-4 sm:-m-6 sm:p-6">

        <div class="mx-auto max-w-4xl">

            {{-- ENCABEZADO --}}
            <div class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm dark:border-stone-600 dark:bg-stone-800">

                <h1 class="text-2xl font-black text-stone-900 dark:text-stone-100">
                    📢 Nuevo aviso
                </h1>

                <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                    Este aviso será visible para todos los socios desde la aplicación.
                </p>

            </div>

            {{-- ERRORES --}}
            @if($errors->any())

                <div class="mt-6 rounded-xl border border-red-500/30 bg-red-500/20 p-5">

                    <h2 class="font-black text-red-700 dark:text-red-300">
                        Revisá los siguientes errores:
                    </h2>

                    <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-red-700 dark:text-red-300">

                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <form
                method="POST"
                action="{{ route('avisos.store') }}"
                class="mt-6 space-y-6"
            >

                @csrf

                <div class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm dark:border-stone-600 dark:bg-stone-800">

                    {{-- TÍTULO --}}
                    <div>

                        <label class="mb-2 block text-sm font-black text-stone-900 dark:text-stone-100">
                            Título
                        </label>

                        <input
                            type="text"
                            name="titulo"
                            value="{{ old('titulo') }}"
                            placeholder="Ejemplo: Cambio de horario"
                            class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3
                                   text-zinc-900 placeholder:text-zinc-500
                                   outline-none transition
                                   focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20
                                   dark:border-stone-600 dark:bg-zinc-950 dark:text-white
                                   dark:placeholder:text-zinc-500"
                            maxlength="255"
                            required
                        >

                    </div>

                    {{-- MENSAJE --}}
                    <div class="mt-5">

                        <label class="mb-2 block text-sm font-black text-stone-900 dark:text-stone-100">
                            Mensaje
                        </label>

                        <textarea
                            name="mensaje"
                            rows="6"
                            placeholder="Escribí aquí la información que recibirán los socios..."
                            class="w-full resize-y rounded-xl border border-stone-300 bg-white px-4 py-3
                                   text-zinc-900 placeholder:text-zinc-500
                                   outline-none transition
                                   focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20
                                   dark:border-stone-600 dark:bg-zinc-950 dark:text-white
                                   dark:placeholder:text-zinc-500"
                            required
                        >{{ old('mensaje') }}</textarea>

                    </div>

                    {{-- PRIORIDAD --}}
                    <div class="mt-5">

                        <label class="mb-2 block text-sm font-black text-stone-900 dark:text-stone-100">
                            Prioridad
                        </label>

                        <select
                            name="prioridad"
                            class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3
                                   text-zinc-900 outline-none transition
                                   focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20
                                   dark:border-stone-600 dark:bg-zinc-950 dark:text-white"
                            style="color-scheme: dark;"
                            required
                        >
                            <option
                                value="informativo"
                                @selected(old('prioridad', 'informativo') === 'informativo')
                            >
                                🟢 Informativo
                            </option>

                            <option
                                value="importante"
                                @selected(old('prioridad') === 'importante')
                            >
                                🟡 Importante
                            </option>

                            <option
                                value="urgente"
                                @selected(old('prioridad') === 'urgente')
                            >
                                🔴 Urgente
                            </option>
                        </select>

                    </div>

                    {{-- FECHAS --}}
                    <div class="mt-5 grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-2 block text-sm font-black text-stone-900 dark:text-stone-100">
                                Publicar desde
                            </label>

                            <input
                                type="datetime-local"
                                name="fecha_publicacion"
                                value="{{ old('fecha_publicacion') }}"
                                class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3
                                       text-zinc-900 outline-none transition
                                       focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20
                                       dark:border-stone-600 dark:bg-zinc-950 dark:text-white"
                                style="color-scheme: dark;"
                            >

                            <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                                Si queda vacío, se publicará inmediatamente.
                            </p>

                        </div>

                        <div>

                            <label class="mb-2 block text-sm font-black text-stone-900 dark:text-stone-100">
                                Visible hasta
                            </label>

                            <input
                                type="datetime-local"
                                name="fecha_vencimiento"
                                value="{{ old('fecha_vencimiento') }}"
                                class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3
                                       text-zinc-900 outline-none transition
                                       focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20
                                       dark:border-stone-600 dark:bg-zinc-950 dark:text-white"
                                style="color-scheme: dark;"
                            >

                            <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                                Si queda vacío, el aviso no vencerá automáticamente.
                            </p>

                        </div>

                    </div>

                    {{-- ACTIVO --}}
                    <div class="mt-6 rounded-xl border border-stone-300 bg-stone-100 p-4 dark:border-stone-600 dark:bg-stone-700">

                        <label class="inline-flex cursor-pointer items-center gap-3">

                            <input
                                type="checkbox"
                                name="activo"
                                value="1"
                                @checked(old('activo', true))
                                class="h-4 w-4 rounded border-stone-400 text-orange-500
                                       focus:ring-2 focus:ring-orange-500/30
                                       dark:border-stone-500 dark:bg-zinc-950"
                            >

                            <span class="font-black text-stone-900 dark:text-stone-100">
                                Publicar este aviso
                            </span>

                        </label>

                        <p class="mt-2 text-xs text-stone-500 dark:text-stone-300">
                            Si lo desmarcás, el aviso se guardará pero no será visible.
                        </p>

                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="flex flex-wrap gap-3">

                    <button
                        type="submit"
                        class="rounded-xl bg-orange-500 px-5 py-3 font-black text-white
                               transition hover:bg-orange-600
                               focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                    >
                        💾 Guardar aviso
                    </button>

                    <a
                        href="{{ route('avisos.index') }}"
                        class="rounded-xl bg-stone-600 px-5 py-3 font-black text-white
                               transition hover:bg-stone-700
                               focus:outline-none focus:ring-2 focus:ring-stone-500/40"
                    >
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</x-layouts::app>