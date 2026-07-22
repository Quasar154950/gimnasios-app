<x-layouts::app :title="'Novedades'">

    <div class="mx-auto max-w-7xl p-6">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-3xl font-bold">
                    📰 Novedades
                </h1>

                <p class="mt-1 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                    Administrá las publicaciones que verán los socios en la app.
                </p>
            </div>

            <a
                href="{{ route('novedades.create') }}"
                class="rounded-xl bg-orange-600 px-5 py-3 text-center font-bold text-white shadow-sm transition hover:bg-orange-700"
            >
                ➕ Nueva publicación
            </a>

        </div>

        @if (session('success'))

            <div class="mb-6 rounded-xl border border-green-700 bg-green-950 p-4 text-green-300">
                {{ session('success') }}
            </div>

        @endif

        @if ($novedades->isEmpty())

            <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center text-zinc-400 shadow">
                Todavía no hay publicaciones.
            </div>

        @else

            <div class="space-y-4">

                @foreach ($novedades as $novedad)

                    <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow">

                        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                            <div class="min-w-0 flex-1">

                                <div class="mb-2 flex flex-wrap items-center gap-2">

                                    <span class="text-xs font-semibold uppercase text-orange-400">
                                        {{ $novedad->tipo }}
                                    </span>

                                    @if ($novedad->destacado)
                                        <span class="text-xs font-semibold text-yellow-400">
                                            ⭐ Destacada
                                        </span>
                                    @endif

                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{ $novedad->activo
                                                ? 'bg-green-950 text-green-400'
                                                : 'bg-red-950 text-red-400' }}"
                                    >
                                        {{ $novedad->activo ? 'Activa' : 'Inactiva' }}
                                    </span>

                                </div>

                                <h2 class="text-xl font-bold text-white">
                                    {{ $novedad->titulo }}
                                </h2>

                                <p class="mt-2 whitespace-pre-line text-zinc-400">
                                    {{ $novedad->descripcion }}
                                </p>

                                @if ($novedad->fecha_publicacion)

                                    <p class="mt-4 text-xs text-zinc-500">
                                        Publicada:
                                        {{ \Carbon\Carbon::parse($novedad->fecha_publicacion)->format('d/m/Y H:i') }}
                                    </p>

                                @endif

                            </div>

                            <div class="flex shrink-0 flex-wrap items-center gap-2">

                                <a
                                    href="{{ route('novedades.edit', $novedad) }}"
                                    class="rounded-xl border border-zinc-700 px-4 py-2 text-sm font-semibold text-zinc-200 transition hover:border-orange-500 hover:bg-orange-600 hover:text-white"
                                >
                                    ✏️ Editar
                                </a>

                                <form
                                    action="{{ route('novedades.destroy', $novedad) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Seguro que querés eliminar esta publicación? Esta acción no se puede deshacer.')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-xl border border-red-800 px-4 py-2 text-sm font-semibold text-red-400 transition hover:bg-red-700 hover:text-white"
                                    >
                                        🗑 Eliminar
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</x-layouts::app>
