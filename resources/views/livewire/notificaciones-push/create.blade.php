<div class="mx-auto max-w-4xl p-6">

    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            📲 Nueva notificación Push
        </h1>

        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
            Enviá una notificación a todos los socios o a un socio específico.
        </p>

    </div>

    <form wire:submit.prevent="guardar" class="space-y-6">

        <div>

            <label class="mb-2 block font-semibold">
                Título
            </label>

            <input
                type="text"
                wire:model="titulo"
                class="w-full rounded-xl border p-3"
            >

            @error('titulo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

        </div>

        <div>

            <label class="mb-2 block font-semibold">
                Mensaje
            </label>

            <textarea
                wire:model="mensaje"
                rows="5"
                class="w-full rounded-xl border p-3"
            ></textarea>

            @error('mensaje')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

        </div>

        <div>

            <label class="mb-2 block font-semibold">
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

        </div>

        @if($destinatario === 'cliente')

            <div>

                <label class="mb-2 block font-semibold">
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
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

            </div>

        @endif

        {{-- ========================================= --}}
        {{-- PANTALLA A ABRIR --}}
        {{-- ========================================= --}}

        <div>

            <label class="mb-2 block font-semibold">
                Abrir al tocar la notificación
            </label>

            <select
                wire:model="pantalla"
                class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
            >

                <option value="inicio">
                    🏠 Inicio
                </option>

                <option value="reservas">
                    📅 Reservas
                </option>

                <option value="cuota">
                    💳 Mi cuota
                </option>

                <option value="mensajes">
                    💬 Mensajes
                </option>

                <option value="rutinas">
                    🏋 Rutinas
                </option>

                <option value="novedades">
                    📰 Novedades
                </option>

                <option value="perfil">
                    👤 Mi perfil
                </option>

                <option value="qr">
                    📲 Mi QR
                </option>

                <option value="musculacion">
                    💪 Musculación
                </option>

            </select>

            @error('pantalla')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Cuando el socio toque la notificación se abrirá automáticamente esta sección de la app.
            </p>

        </div>

        {{-- ========================================= --}}
        {{-- ENVÍO --}}
        {{-- ========================================= --}}

        <div>

            <label class="mb-2 block font-semibold">
                Envío
            </label>

            <select
                wire:model.live="modoEnvio"
                class="w-full cursor-pointer rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
            >

                <option value="ahora">
                    Enviar ahora
                </option>

                <option value="programar">
                    Programar
                </option>

            </select>

        </div>

        @if($modoEnvio === 'programar')

            <div>

                <label class="mb-2 block font-semibold">
                    Fecha y hora
                </label>

                <input
                    type="datetime-local"
                    wire:model="programadaPara"
                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20"
                >

                @error('programadaPara')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

            </div>

        @endif

        <div class="flex justify-end gap-3">

            <a
                href="{{ route('notificaciones-push.index') }}"
                wire:navigate
                class="rounded-xl border px-5 py-3 font-semibold"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="rounded-xl bg-orange-600 px-6 py-3 font-bold text-white hover:bg-orange-700"
            >
                💾 Guardar
            </button>

        </div>

    </form>

</div>

