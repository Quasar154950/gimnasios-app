<div class="space-y-6">

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Biblioteca de ejercicios
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Administrá los ejercicios que después podrás usar en las rutinas.
            </p>
        </div>

        <a
            href="{{ route('ejercicios.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
        >
            + Nuevo ejercicio
        </a>
    </div>

    @if (session()->has('mensaje'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('mensaje') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Buscar
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.400ms="buscar"
                    placeholder="Nombre, descripción o grupo muscular"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Grupo muscular
                </label>

                <select
                    wire:model.live="grupoMuscular"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">Todos</option>

                    @foreach ($gruposMusculares as $grupo)
                        <option value="{{ $grupo }}">
                            {{ $grupo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Estado
                </label>

                <select
                    wire:model.live="estado"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    <option value="activos">Activos</option>
                    <option value="inactivos">Inactivos</option>
                    <option value="todos">Todos</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Orden
                </label>

                <select
                    wire:model.live="orden"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    <option value="nombre">Nombre</option>
                    <option value="recientes">Más recientes</option>
                    <option value="antiguos">Más antiguos</option>
                </select>
            </div>

        </div>

        <div class="mt-4 flex justify-end">
            <button
                type="button"
                wire:click="limpiarFiltros"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Limpiar filtros
            </button>
        </div>
    </div>

    @if ($ejercicios->count())

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

            @foreach ($ejercicios as $ejercicio)

                <article
                    wire:key="ejercicio-{{ $ejercicio->id }}"
                    class="overflow-hidden rounded-xl bg-white shadow-sm"
                >

                    <div class="flex h-48 items-center justify-center bg-gray-100">

                        @if ($ejercicio->hasMedia('imagen'))
                            <img
                                src="{{ $ejercicio->getFirstMediaUrl('imagen') }}"
                                alt="{{ $ejercicio->nombre }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="text-center text-gray-400">
                                <div class="text-4xl">🏋️</div>
                                <p class="mt-2 text-sm">Sin imagen</p>
                            </div>
                        @endif

                    </div>

                    <div class="space-y-4 p-5">

                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <h2 class="text-lg font-bold text-gray-900">
                                    {{ $ejercicio->nombre }}
                                </h2>

                                <p class="mt-1 text-sm font-medium text-blue-600">
                                    {{ $ejercicio->grupo_muscular }}
                                </p>
                            </div>

                            @if ($ejercicio->activo)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Activo
                                </span>
                            @else
                                <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">
                                    Inactivo
                                </span>
                            @endif

                        </div>

                        @if ($ejercicio->descripcion)
                            <p class="line-clamp-3 text-sm leading-6 text-gray-600">
                                {{ $ejercicio->descripcion }}
                            </p>
                        @else
                            <p class="text-sm italic text-gray-400">
                                Sin descripción.
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-2 border-t border-gray-100 pt-4">

                            <a
                                href="{{ route('ejercicios.edit', $ejercicio) }}"
                                class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                            >
                                Editar
                            </a>

                            <button
                                type="button"
                                wire:click="cambiarEstado({{ $ejercicio->id }})"
                                wire:loading.attr="disabled"
                                class="rounded-lg border border-amber-200 px-3 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-50"
                            >
                                {{ $ejercicio->activo ? 'Desactivar' : 'Activar' }}
                            </button>

                            <button
                                type="button"
                                wire:click="eliminar({{ $ejercicio->id }})"
                                wire:confirm="¿Seguro que querés eliminar este ejercicio?"
                                wire:loading.attr="disabled"
                                class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50"
                            >
                                Eliminar
                            </button>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

        <div>
            {{ $ejercicios->links() }}
        </div>

    @else

        <div class="rounded-xl bg-white px-6 py-16 text-center shadow-sm">
            <div class="text-5xl">🏋️</div>

            <h2 class="mt-4 text-lg font-bold text-gray-900">
                No hay ejercicios para mostrar
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Creá el primer ejercicio o modificá los filtros de búsqueda.
            </p>
        </div>

    @endif

</div>
