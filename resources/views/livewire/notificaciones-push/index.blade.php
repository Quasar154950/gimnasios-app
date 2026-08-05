<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="mb-1 text-sm font-medium uppercase tracking-widest text-orange-400">
                    Comunicación
                </p>

                <h1 class="text-3xl font-bold tracking-tight text-white">
                    📲 Notificaciones Push
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Enviá mensajes instantáneos a los socios del gimnasio.
                </p>

            </div>

            <a
                 href="{{ route('notificaciones-push.create') }}"
                 wire:navigate
                 class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-5 py-3 font-semibold text-zinc-950 shadow-lg shadow-orange-950/40 transition hover:bg-orange-400 active:scale-95"
            >
                ➕ Nueva notificación
            </a>

            </div>

        @if (session()->has('success'))

            <div class="mb-6 rounded-xl border border-green-800 bg-green-950/70 px-4 py-3 text-sm font-medium text-green-200">
                {{ session('success') }}
            </div>

        @endif

        {{-- FILTROS --}}
        <div class="mb-6 grid gap-4 rounded-2xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl sm:grid-cols-[1fr_220px]">

            <input
                type="search"
                wire:model.live.debounce.300ms="buscar"
                placeholder="Buscar por título o mensaje..."
                class="rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900"
            >

            <select
                wire:model.live="estado"
                class="rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900"
            >
                <option value="todos">Todas</option>
                <option value="pendiente">Pendientes</option>
                <option value="enviada">Enviadas</option>
                <option value="error">Con error</option>
            </select>

        </div>

        {{-- ESTADO VACÍO --}}
        @if($notificaciones->count() == 0)

            <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-900 px-6 py-16 text-center shadow-xl">

                <div class="text-6xl">
                    📲
                </div>

                <h2 class="mt-5 text-xl font-bold text-white">
                    Todavía no hay notificaciones
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-400">
                    Cuando envíes la primera Push aparecerá aquí el historial.
                </p>

            </div>

        @else

            <div class="mt-8">

                {{-- Después agregaremos las tarjetas aquí --}}

            </div>

        @endif

    </div>

</div>