<x-layouts::app :title="'Novedades'">

    <div class="max-w-7xl mx-auto p-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold">
                    📰 Novedades
                </h1>

                <p class="text-zinc-400 mt-1">
                    Administrá las publicaciones que verán los socios en la app.
                </p>
            </div>

            <a
                href="{{ route('novedades.create') }}"
                class="px-5 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition"
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

            <div class="bg-zinc-900 border border-zinc-800 rounded-xl shadow p-8 text-center text-zinc-400">
                Todavía no hay publicaciones.
            </div>

        @else

            <div class="space-y-4">

                @foreach ($novedades as $novedad)

                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs uppercase font-semibold text-orange-400">
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
