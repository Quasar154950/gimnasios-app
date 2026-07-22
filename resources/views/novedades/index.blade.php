<x-layouts::app :title="'Novedades'">

    <div class="mx-auto max-w-7xl p-6">

        <div class="mb-6 flex items-center justify-between">
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
                class="rounded-xl bg-orange-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-orange-700"
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

                    <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="text-xs font-semibold uppercase text-orange-400">
                                        {{ $novedad->tipo }}
                                    </span>

                                    @if ($novedad->destacado)
                                        <span class="text-xs text-yellow-400">
                                            ⭐ Destacada
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-xl font-bold text-white">
                                    {{ $novedad->titulo }}
                                </h2>

                                <p class="mt-2 text-zinc-400">
                                    {{ $novedad->descripcion }}
                                </p>
                            </div>

                            <span class="text-xs {{ $novedad->activo ? 'text-green-400' : 'text-red-400' }}">
                                {{ $novedad->activo ? 'Activa' : 'Inactiva' }}
                            </span>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</x-layouts::app>
