<x-layouts::app :title="'Nueva notificación Push'">

    <div class="mx-auto max-w-4xl p-6">

        <div class="mb-8">

            <h1 class="text-3xl font-bold">
                📲 Nueva notificación Push
            </h1>

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Enviá una notificación a todos los socios o a un socio específico.
            </p>

        </div>

        <form wire:submit="guardar" class="space-y-6">

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
                    class="w-full rounded-xl border p-3"
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
                        class="w-full rounded-xl border p-3"
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

            <div>

                <label class="mb-2 block font-semibold">
                    Envío
                </label>

                <select
                    wire:model.live="modoEnvio"
                    class="w-full rounded-xl border p-3"
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
                        class="w-full rounded-xl border p-3"
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

</x-layouts::app>