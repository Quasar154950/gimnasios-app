<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-4xl">

        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="mb-1 text-sm font-medium uppercase tracking-widest text-orange-400">
                    Comunicación
                </p>

                <h1 class="text-3xl font-bold tracking-tight">
                    ✏ Editar notificación
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Modificá los datos antes de que la notificación sea enviada.
                </p>
            </div>

            <a
                href="{{ route('notificaciones-push.index') }}"
                wire:navigate
                class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-900 px-5 py-3 font-semibold text-zinc-200 transition hover:bg-zinc-800"
            >
                ← Volver
            </a>

        </div>

        <form
            wire:submit.prevent="guardar"
            class="space-y-6 rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-2xl"
        >

            <div>
                <label class="mb-2 block font-semibold text-zinc-200">
                    Título
                </label>

                <input
                    type="text"
                    wire:model="titulo"
                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >

                @error('titulo')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block font-semibold text-zinc-200">
                    Mensaje
                </label>

                <textarea
                    wire:model="mensaje"
                    rows="6"
                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                ></textarea>

                @error('mensaje')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block font-semibold text-zinc-200">
                    Destinatario
                </label>

                <select
                    wire:model.live="destinatario"
                    class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >
                    <option value="todos">
                        Todos los socios
                    </option>

                    <option value="cliente">
                        Un socio específico
                    </option>

                    <option value="cuota_vencida">
                        Socios con cuota vencida
                    </option>
                </select>

                @error('destinatario')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            @if($destinatario === 'cliente')

                <div>
                    <label class="mb-2 block font-semibold text-zinc-200">
                        Socio
                    </label>

                    <select
                        wire:model="clienteId"
                        class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                    >
                        <option value="">
                            Seleccionar...
                        </option>

                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>

                    @error('clienteId')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            @endif

            <div>
                <label class="mb-2 block font-semibold text-zinc-200">
                    Abrir al tocar la notificación
                </label>

                <select
                    wire:model="pantalla"
                    class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >
                    <option value="inicio">🏠 Inicio</option>
                    <option value="reservas">📅 Reservas</option>
                    <option value="cuota">💳 Mi cuota</option>
                    <option value="mensajes">💬 Mensajes</option>
                    <option value="rutinas">🏋 Rutinas</option>
                    <option value="novedades">📰 Novedades</option>
                    <option value="perfil">👤 Mi perfil</option>
                    <option value="qr">📲 Mi QR</option>
                    <option value="musculacion">💪 Musculación</option>
                </select>

                @error('pantalla')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block font-semibold text-zinc-200">
                    Fecha y hora programada
                </label>

                <input
                    type="datetime-local"
                    wire:model="programadaPara"
                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >

                @error('programadaPara')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-sm text-zinc-500">
                    Si quitás la fecha, la notificación seguirá pendiente hasta que uses “Enviar ahora”.
                </p>
            </div>

            <div class="flex flex-col gap-3 border-t border-zinc-800 pt-6 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('notificaciones-push.index') }}"
                    wire:navigate
                    class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-950 px-5 py-3 font-semibold text-zinc-200 transition hover:bg-zinc-800"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="guardar"
                    class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-6 py-3 font-bold text-zinc-950 transition hover:bg-orange-400 disabled:cursor-wait disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="guardar">
                        💾 Guardar cambios
                    </span>

                    <span wire:loading wire:target="guardar">
                        Guardando...
                    </span>
                </button>

            </div>

        </form>

    </div>

</div>