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

            <div class="mb-6 rounded-xl border border-green-700 bg-green-950 p-4 font-semibold text-green-300">
                {{ session('success') }}
            </div>

        @endif

        @if ($novedades->isEmpty())

            <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-10 text-center shadow">

                <div class="text-5xl">
                    📰
                </div>

                <h2 class="mt-4 text-xl font-bold text-white">
                    Todavía no hay publicaciones
                </h2>

                <p class="mt-2 text-zinc-400">
                    Creá la primera novedad para comenzar a informar a tus socios.
                </p>

                <a
                    href="{{ route('novedades.create') }}"
                    class="mt-6 inline-flex rounded-xl bg-orange-600 px-5 py-3 font-bold text-white transition hover:bg-orange-700"
                >
                    ➕ Crear publicación
                </a>

            </div>

        @else

            <div class="grid gap-6 lg:grid-cols-2">

                @foreach ($novedades as $novedad)

                    @php
                        $tipoConfiguracion = match ($novedad->tipo) {
                            'promocion' => [
                                'nombre' => 'Promoción',
                                'emoji' => '🎁',
                                'clase' => 'bg-pink-950 text-pink-300',
                            ],
                            'evento' => [
                                'nombre' => 'Evento',
                                'emoji' => '📅',
                                'clase' => 'bg-blue-950 text-blue-300',
                            ],
                            'consejo' => [
                                'nombre' => 'Consejo',
                                'emoji' => '💡',
                                'clase' => 'bg-purple-950 text-purple-300',
                            ],
                            default => [
                                'nombre' => 'Novedad',
                                'emoji' => '📰',
                                'clase' => 'bg-orange-950 text-orange-300',
                            ],
                        };
                    @endphp

                    <article
                        class="overflow-hidden rounded-2xl border bg-zinc-900 shadow transition hover:-translate-y-0.5 hover:shadow-xl
                            {{ $novedad->destacado
                                ? 'border-yellow-600'
                                : 'border-zinc-800' }}"
                    >

                        @if ($novedad->imagen)

                            <div class="relative flex min-h-64 items-center justify-center overflow-hidden bg-zinc-950">

                                <img
                                    src="{{ $novedad->imagen }}"
                                    alt="{{ $novedad->titulo }}"
                                    loading="lazy"
                                    class="max-h-[32rem] w-full object-contain"
                                >

                                @if ($novedad->destacado)

                                    <div class="absolute left-3 top-3">

                                        <span class="rounded-full bg-yellow-500 px-3 py-1.5 text-xs font-black text-zinc-950 shadow">
                                            ⭐ DESTACADA
                                        </span>

                                    </div>

                                @endif

                                @unless ($novedad->activo)

                                    <div class="absolute inset-0 flex items-center justify-center bg-black/65">

                                        <span class="rounded-full border border-red-500 bg-red-950/90 px-4 py-2 text-sm font-bold text-red-300">
                                            👁 Publicación oculta
                                        </span>

                                    </div>

                                @endunless

                            </div>

                        @endif

                        <div class="p-5">

                            <div class="mb-3 flex flex-wrap items-center gap-2">

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold {{ $tipoConfiguracion['clase'] }}"
                                >
                                    {{ $tipoConfiguracion['emoji'] }}
                                    {{ $tipoConfiguracion['nombre'] }}
                                </span>

                                @if ($novedad->destacado && ! $novedad->imagen)

                                    <span class="rounded-full bg-yellow-950 px-3 py-1 text-xs font-bold text-yellow-300">
                                        ⭐ Destacada
                                    </span>

                                @endif

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ $novedad->activo
                                            ? 'bg-green-950 text-green-300'
                                            : 'bg-red-950 text-red-300' }}"
                                >
                                    {{ $novedad->activo
                                        ? '● Visible'
                                        : '● Oculta' }}
                                </span>

                            </div>

                            <h2 class="text-xl font-black leading-tight text-white">
                                {{ $novedad->titulo }}
                            </h2>

                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-zinc-400">
                                {{ $novedad->descripcion }}
                            </p>

                            <div class="mt-5 border-t border-zinc-800 pt-4">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                    <div class="text-xs text-zinc-500">

                                        @if ($novedad->fecha_publicacion)

                                            <span>
                                                📅 Publicada el
                                                {{ $novedad->fecha_publicacion->format('d/m/Y') }}
                                            </span>

                                        @else

                                            <span>
                                                📅 Sin fecha de publicación
                                            </span>

                                        @endif

                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">

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

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </div>

</x-layouts::app>
