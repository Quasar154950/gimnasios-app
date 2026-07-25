<div class="space-y-6">

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-white">
                Biblioteca de ejercicios
            </h1>

            <p class="mt-1 text-sm text-zinc-400">
                Administrá los ejercicios que después podrás usar en las rutinas.
            </p>
        </div>

        <a
            href="{{ route('ejercicios.create') }}"
            wire:navigate
            class="inline-flex cursor-pointer items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-blue-500"
        >
            + Nuevo ejercicio
        </a>

    </div>

    @if (session()->has('mensaje'))

        <div class="rounded-lg border border-green-700 bg-green-950 px-4 py-3 text-sm text-green-200">
            {{ session('mensaje') }}
        </div>

    @endif

    @if (session()->has('success'))

        <div class="rounded-lg border border-green-700 bg-green-950 px-4 py-3 text-sm text-green-200">
            {{ session('success') }}
        </div>

    @endif

    @if (session()->has('error'))

        <div class="rounded-lg border border-red-700 bg-red-950 px-4 py-3 text-sm text-red-200">
            {{ session('error') }}
        </div>

    @endif

    {{-- FILTROS --}}
    <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-4 shadow-lg">

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            {{-- BUSCAR --}}
            <div>

                <label
                    for="buscar"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Buscar
                </label>

                <input
                    id="buscar"
                    type="text"
                    wire:model.live.debounce.400ms="buscar"
                    placeholder="Nombre, descripción o grupo"
                    autocomplete="off"
                    class="w-full rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none placeholder:text-zinc-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                >

            </div>

            {{-- GRUPO MUSCULAR --}}
            <div>

                <label
                    for="grupo-muscular"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Grupo muscular
                </label>

                <select
                    id="grupo-muscular"
                    wire:model.live="grupoMuscular"
                    class="w-full cursor-pointer rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                >
                    <option value="">Todos</option>

                    @foreach ($gruposMusculares as $grupo)

                        <option
                            value="{{ $grupo }}"
                            class="bg-white text-zinc-950"
                        >
                            {{ $grupo }}
                        </option>

                    @endforeach

                </select>

            </div>

            {{-- ESTADO --}}
            <div>

                <label
                    for="estado"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Estado
                </label>

                <select
                    id="estado"
                    wire:model.live="estado"
                    class="w-full cursor-pointer rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                >
                    <option
                        value="activos"
                        class="bg-white text-zinc-950"
                    >
                        Activos
                    </option>

                    <option
                        value="inactivos"
                        class="bg-white text-zinc-950"
                    >
                        Inactivos
                    </option>

                    <option
                        value="todos"
                        class="bg-white text-zinc-950"
                    >
                        Todos
                    </option>
                </select>

            </div>

            {{-- ORDEN --}}
            <div>

                <label
                    for="orden"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Orden
                </label>

                <select
                    id="orden"
                    wire:model.live="orden"
                    class="w-full cursor-pointer rounded-lg border border-zinc-600 bg-white px-3 py-2.5 text-sm text-zinc-950 shadow-inner outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                >
                    <option
                        value="nombre"
                        class="bg-white text-zinc-950"
                    >
                        Nombre
                    </option>

                    <option
                        value="recientes"
                        class="bg-white text-zinc-950"
                    >
                        Más recientes
                    </option>

                    <option
                        value="antiguos"
                        class="bg-white text-zinc-950"
                    >
                        Más antiguos
                    </option>
                </select>

            </div>

        </div>

        <div class="mt-4 flex justify-end">

            <button
                type="button"
                wire:click="limpiarFiltros"
                wire:loading.attr="disabled"
                class="cursor-pointer rounded-lg border border-zinc-600 bg-zinc-800 px-4 py-2 text-sm font-semibold text-zinc-200 shadow-sm transition hover:border-zinc-500 hover:bg-zinc-700 disabled:cursor-not-allowed disabled:opacity-50"
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
                    class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900 shadow-lg"
                >

                    <div class="flex h-48 items-center justify-center bg-zinc-800">

                        @if ($ejercicio->hasMedia('imagen'))

                            <img
                                src="{{ $ejercicio->getFirstMediaUrl('imagen') }}"
                                alt="{{ $ejercicio->nombre }}"
                                class="h-full w-full object-cover"
                            >

                        @else

                            <div class="text-center text-zinc-500">

                                <div class="text-4xl">
                                    🏋️
                                </div>

                                <p class="mt-2 text-sm">
                                    Sin imagen
                                </p>

                            </div>

                        @endif

                    </div>

                    <div class="space-y-4 p-5">

                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <h2 class="text-lg font-bold text-white">
                                    {{ $ejercicio->nombre }}
                                </h2>

                                <p class="mt-1 text-sm font-semibold text-blue-400">
                                    {{ $ejercicio->grupo_muscular }}
                                </p>

                            </div>

                            @if ($ejercicio->activo)

                                <span class="rounded-full border border-green-700 bg-green-950 px-3 py-1 text-xs font-semibold text-green-300">
                                    Activo
                                </span>

                            @else

                                <span class="rounded-full border border-zinc-600 bg-zinc-800 px-3 py-1 text-xs font-semibold text-zinc-300">
                                    Inactivo
                                </span>

                            @endif

                        </div>

                        @if ($ejercicio->descripcion)

                            <p class="line-clamp-3 text-sm leading-6 text-zinc-400">
                                {{ $ejercicio->descripcion }}
                            </p>

                        @else

                            <p class="text-sm italic text-zinc-500">
                                Sin descripción.
                            </p>

                        @endif

                        <div class="flex flex-wrap gap-2 border-t border-zinc-800 pt-4">

                            <a
                                href="{{ route('ejercicios.edit', $ejercicio) }}"
                                wire:navigate
                                class="cursor-pointer rounded-lg border border-blue-700 bg-blue-950 px-3 py-2 text-sm font-semibold text-blue-300 transition hover:bg-blue-900"
                            >
                                Editar
                            </a>

                            <button
                                type="button"
                                wire:click="cambiarEstado({{ $ejercicio->id }})"
                                wire:loading.attr="disabled"
                                class="cursor-pointer rounded-lg border border-amber-700 bg-amber-950 px-3 py-2 text-sm font-semibold text-amber-300 transition hover:bg-amber-900 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{ $ejercicio->activo ? 'Desactivar' : 'Activar' }}
                            </button>

                            <button
                                type="button"
                                wire:click="eliminar({{ $ejercicio->id }})"
                                wire:confirm="¿Seguro que querés eliminar este ejercicio?"
                                wire:loading.attr="disabled"
                                class="cursor-pointer rounded-lg border border-red-700 bg-red-950 px-3 py-2 text-sm font-semibold text-red-300 transition hover:bg-red-900 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Eliminar
                            </button>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

        <div class="text-zinc-200">
            {{ $ejercicios->links() }}
        </div>

    @else

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 px-6 py-16 text-center shadow-lg">

            <div class="text-5xl">
                🏋️
            </div>

            <h2 class="mt-4 text-lg font-bold text-white">
                No hay ejercicios para mostrar
            </h2>

            <p class="mt-2 text-sm text-zinc-400">
                Creá el primer ejercicio o modificá los filtros de búsqueda.
            </p>

        </div>

    @endif

</div>
