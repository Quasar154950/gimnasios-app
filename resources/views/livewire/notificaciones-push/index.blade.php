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

        {{-- MENSAJES --}}
        @if (session()->has('success'))

            <div class="mb-6 rounded-xl border border-green-800 bg-green-950/70 px-4 py-3 text-sm font-medium text-green-200">
                {{ session('success') }}
            </div>

        @endif

        @if (session()->has('error'))

            <div class="mb-6 rounded-xl border border-red-800 bg-red-950/70 px-4 py-3 text-sm font-medium text-red-200">
                {{ session('error') }}
            </div>

        @endif

        {{-- FILTROS --}}
        <div class="mb-6 grid gap-4 rounded-2xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl sm:grid-cols-[1fr_220px]">

            <div>

                <label
                    for="buscar-notificacion"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Buscar
                </label>

                <input
                    id="buscar-notificacion"
                    type="search"
                    wire:model.live.debounce.300ms="buscar"
                    placeholder="Buscar por título o mensaje..."
                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition placeholder:text-zinc-500 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >

            </div>

            <div>

                <label
                    for="estado-notificacion"
                    class="mb-2 block text-sm font-semibold text-zinc-200"
                >
                    Estado
                </label>

                <select
                    id="estado-notificacion"
                    wire:model.live="estado"
                    class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >
                    <option value="todos">Todas</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="enviada">Enviadas</option>
                    <option value="error">Con error</option>
                    <option value="borrador">Borradores</option>
                </select>

            </div>

        </div>

        {{-- CARGANDO --}}
        <div
            wire:loading.flex
            wire:target="buscar,estado,eliminar"
            class="mb-5 items-center gap-3 rounded-xl border border-zinc-800 bg-zinc-900 px-4 py-3 text-sm text-zinc-300"
        >
            <svg
                class="h-5 w-5 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
            </svg>

            Actualizando notificaciones...
        </div>

        {{-- LISTADO --}}
        @if($notificaciones->count())

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

                @foreach($notificaciones as $notificacion)

                    @php
                        $estadoConfiguracion = match ($notificacion->estado) {
                            'enviada' => [
                                'texto' => 'Enviada',
                                'clase' => 'border-green-800 bg-green-950 text-green-300',
                            ],
                            'pendiente' => [
                                'texto' => 'Pendiente',
                                'clase' => 'border-amber-800 bg-amber-950 text-amber-300',
                            ],
                            'error' => [
                                'texto' => 'Error',
                                'clase' => 'border-red-800 bg-red-950 text-red-300',
                            ],
                            default => [
                                'texto' => 'Borrador',
                                'clase' => 'border-zinc-700 bg-zinc-800 text-zinc-300',
                            ],
                        };

                        $destinatarioTexto = match ($notificacion->destinatario) {
                            'cliente' => $notificacion->cliente
                                ? 'Socio: ' . $notificacion->cliente->nombre
                                : 'Socio específico',
                            'cuota_vencida' => 'Socios con cuota vencida',
                            default => 'Todos los socios',
                        };

                        $tipoTexto = $notificacion->tipo === 'automatica'
                            ? 'Automática'
                            : 'Manual';
                    @endphp

                    <article
                        wire:key="notificacion-{{ $notificacion->id }}"
                        class="flex flex-col overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-xl transition hover:-translate-y-1 hover:border-zinc-700 hover:shadow-2xl"
                    >

                        {{-- CABECERA --}}
                        <div class="border-b border-zinc-800 p-5">

                            <div class="mb-4 flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <h2 class="line-clamp-2 text-xl font-bold text-white">
                                        {{ $notificacion->titulo }}
                                    </h2>

                                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-orange-400">
                                        {{ $tipoTexto }}
                                    </p>

                                </div>

                                <span class="shrink-0 rounded-full border px-3 py-1 text-xs font-bold {{ $estadoConfiguracion['clase'] }}">
                                    {{ $estadoConfiguracion['texto'] }}
                                </span>

                            </div>

                            <p class="line-clamp-4 min-h-[80px] whitespace-pre-line text-sm leading-6 text-zinc-400">
                                {{ $notificacion->mensaje }}
                            </p>

                        </div>

                        {{-- INFORMACIÓN --}}
                        <div class="space-y-3 border-b border-zinc-800 bg-zinc-950/50 p-5 text-sm">

                            <div class="flex items-start justify-between gap-4">

                                <span class="text-zinc-500">
                                    Destinatario
                                </span>

                                <span class="text-right font-semibold text-zinc-200">
                                    {{ $destinatarioTexto }}
                                </span>

                            </div>

                            <div class="flex items-start justify-between gap-4">

                                <span class="text-zinc-500">
                                    Dispositivos
                                </span>

                                <span class="font-semibold text-zinc-200">
                                    {{ $notificacion->cantidad_enviada }}
                                </span>

                            </div>

                            <div class="flex items-start justify-between gap-4">

                                <span class="text-zinc-500">
                                    Creada
                                </span>

                                <span class="text-right font-semibold text-zinc-200">
                                    {{ $notificacion->created_at->format('d/m/Y H:i') }}
                                </span>

                            </div>

                            @if($notificacion->programada_para)

                                <div class="flex items-start justify-between gap-4">

                                    <span class="text-zinc-500">
                                        Programada
                                    </span>

                                    <span class="text-right font-semibold text-amber-300">
                                        {{ $notificacion->programada_para->format('d/m/Y H:i') }}
                                    </span>

                                </div>

                            @endif

                            @if($notificacion->enviada_at)

                                <div class="flex items-start justify-between gap-4">

                                    <span class="text-zinc-500">
                                        Enviada
                                    </span>

                                    <span class="text-right font-semibold text-green-300">
                                        {{ $notificacion->enviada_at->format('d/m/Y H:i') }}
                                    </span>

                                </div>

                            @endif

                        </div>

                        {{-- ERROR --}}
                        @if($notificacion->error)

                            <div class="border-b border-red-900 bg-red-950/40 px-5 py-4 text-sm text-red-300">
                                <span class="font-bold">
                                    Error:
                                </span>

                                {{ $notificacion->error }}
                            </div>

                        @endif

                        {{-- ACCIONES --}}
                        <div class="mt-auto grid grid-cols-2 gap-3 p-5">

                            <button
                                type="button"
                                disabled
                                class="inline-flex cursor-not-allowed items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-zinc-500 opacity-70"
                                title="Lo activaremos en el próximo paso"
                            >
                                🔁 Reenviar
                            </button>

                            <button
                                type="button"
                                wire:click="eliminar({{ $notificacion->id }})"
                                wire:confirm="¿Seguro que querés eliminar esta notificación del historial?"
                                wire:loading.attr="disabled"
                                wire:target="eliminar({{ $notificacion->id }})"
                                class="inline-flex items-center justify-center rounded-xl border border-red-900 bg-red-950/80 px-4 py-2.5 text-sm font-semibold text-red-300 transition hover:bg-red-900 active:scale-95 disabled:cursor-wait disabled:opacity-60"
                            >
                                🗑 Eliminar
                            </button>

                        </div>

                    </article>

                @endforeach

            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-8">
                {{ $notificaciones->links() }}
            </div>

        @else

            {{-- ESTADO VACÍO --}}
            <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-900 px-6 py-16 text-center shadow-xl">

                <div class="text-6xl">
                    📲
                </div>

                <h2 class="mt-5 text-xl font-bold text-white">
                    No encontramos notificaciones
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-400">
                    Creá la primera notificación o modificá los filtros de búsqueda.
                </p>

                <a
                    href="{{ route('notificaciones-push.create') }}"
                    wire:navigate
                    class="mt-6 inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 font-semibold text-zinc-950 transition hover:bg-orange-400 active:scale-95"
                >
                    ➕ Crear primera notificación
                </a>

            </div>

        @endif

    </div>

</div>