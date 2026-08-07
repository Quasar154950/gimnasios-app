<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-4xl">

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

            $pantallaTexto = match ($notificacion->pantalla) {
                'inicio' => '🏠 Inicio',
                'reservas' => '📅 Reservas',
                'cuota' => '💳 Mi cuota',
                'mensajes' => '💬 Mensajes',
                'rutinas' => '🏋 Rutinas',
                'novedades' => '📰 Novedades',
                'perfil' => '👤 Mi perfil',
                'qr' => '📲 Mi QR',
                'musculacion' => '💪 Musculación',
                default => ucfirst($notificacion->pantalla),
            };
        @endphp

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="mb-1 text-sm font-medium uppercase tracking-widest text-orange-400">
                    Comunicación
                </p>

                <h1 class="text-3xl font-bold tracking-tight">
                    👁 Detalle de notificación
                </h1>
            </div>

            <a
                href="{{ route('notificaciones-push.index') }}"
                wire:navigate
                class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-900 px-5 py-3 font-semibold text-zinc-200 transition hover:bg-zinc-800"
            >
                ← Volver
            </a>

        </div>

        <article class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-2xl">

            <div class="border-b border-zinc-800 p-6">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            {{ $notificacion->titulo }}
                        </h2>

                        <p class="mt-2 text-sm font-semibold uppercase tracking-wide text-orange-400">
                            {{ $notificacion->tipo === 'automatica' ? 'Automática' : 'Manual' }}
                        </p>
                    </div>

                    <span class="w-fit rounded-full border px-3 py-1 text-xs font-bold {{ $estadoConfiguracion['clase'] }}">
                        {{ $estadoConfiguracion['texto'] }}
                    </span>

                </div>

                <div class="mt-6 rounded-xl border border-zinc-800 bg-zinc-950/60 p-5">
                    <p class="whitespace-pre-line leading-7 text-zinc-300">
                        {{ $notificacion->mensaje }}
                    </p>
                </div>

            </div>

            <div class="grid gap-px bg-zinc-800 sm:grid-cols-2">

                <div class="bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Destinatario
                    </p>

                    <p class="mt-2 font-semibold text-zinc-200">
                        {{ $destinatarioTexto }}
                    </p>
                </div>

                <div class="bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Pantalla
                    </p>

                    <p class="mt-2 font-semibold text-zinc-200">
                        {{ $pantallaTexto }}
                    </p>
                </div>

                <div class="bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Creada
                    </p>

                    <p class="mt-2 font-semibold text-zinc-200">
                        {{ $notificacion->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Dispositivos
                    </p>

                    <p class="mt-2 font-semibold text-zinc-200">
                        {{ $notificacion->cantidad_enviada }}
                    </p>
                </div>

                <div class="bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Programada
                    </p>

                    <p class="mt-2 font-semibold text-amber-300">
                        {{ $notificacion->programada_para?->format('d/m/Y H:i') ?? 'No programada' }}
                    </p>
                </div>

                <div class="bg-zinc-900 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Enviada
                    </p>

                    <p class="mt-2 font-semibold text-green-300">
                        {{ $notificacion->enviada_at?->format('d/m/Y H:i') ?? 'Todavía no enviada' }}
                    </p>
                </div>

            </div>

            @if($notificacion->error)

                <div class="border-t border-red-900 bg-red-950/40 p-5 text-sm text-red-300">
                    <span class="font-bold">Error:</span>
                    {{ $notificacion->error }}
                </div>

            @endif

            <div class="flex flex-col gap-3 border-t border-zinc-800 p-5 sm:flex-row sm:justify-end">

                @if($notificacion->estado === 'pendiente')

                    <a
                        href="{{ route('notificaciones-push.edit', $notificacion) }}"
                        wire:navigate
                        class="inline-flex items-center justify-center rounded-xl border border-indigo-800 bg-indigo-950/80 px-5 py-3 font-semibold text-indigo-300 transition hover:bg-indigo-900"
                    >
                        ✏ Editar
                    </a>

                @endif

                <a
                    href="{{ route('notificaciones-push.index') }}"
                    wire:navigate
                    class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 font-semibold text-zinc-950 transition hover:bg-orange-400"
                >
                    Volver al historial
                </a>

            </div>

        </article>

    </div>

</div>