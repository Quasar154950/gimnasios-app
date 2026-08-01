<x-layouts::app :title="__('Editar Socio')">

    <div class="space-y-6">

        {{-- CABECERA --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm">

            <h1 class="text-2xl font-bold text-stone-900">
                Editar Socio
            </h1>

            <p class="mt-2 text-sm text-stone-600">
                Modificá los datos del socio seleccionado.
            </p>

        </div>

        {{-- FORMULARIO --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm">

            <x-alert-error />

            <form method="POST"
                  action="{{ route('clientes.update', $cliente->id) }}"
                  class="space-y-4"
                  x-data="{ cargando: false }"
                  x-on:submit="cargando = true">

                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div>

                    <label for="nombre"
                           class="block text-sm font-bold text-stone-700">
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre', $cliente->nombre) }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                    >

                </div>

{{-- DNI --}}
<div>

    <label for="dni"
           class="block text-sm font-bold text-stone-700">
        DNI
    </label>

    <input
        type="text"
        name="dni"
        id="dni"
        value="{{ old('dni', $cliente->dni) }}"
        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
    >

</div>

                {{-- TELÉFONO --}}
                <div>

                    <label for="telefono"
                           class="block text-sm font-bold text-stone-700">
                        Teléfono
                    </label>

                    <input
                        type="text"
                        name="telefono"
                        id="telefono"
                        value="{{ old('telefono', $cliente->telefono) }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                    >

                </div>

                {{-- EMAIL --}}
                <div>

                    <label for="email"
                           class="block text-sm font-bold text-stone-700">
                        Email
                    </label>

                    <input
                        type="text"
                        name="email"
                        id="email"
                        value="{{ old('email', $cliente->user?->email ?? $cliente->email) }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                    >

                </div>

                {{-- DIRECCIÓN --}}
                <div>

                    <label for="direccion"
                           class="block text-sm font-bold text-stone-700">
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        id="direccion"
                        value="{{ old('direccion', $cliente->direccion) }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                    >

                </div>

                {{-- VENCIMIENTO CUOTA --}}
                <div>

                    <label for="fecha_vencimiento_cuota"
                           class="block text-sm font-bold text-stone-700">
                        Vencimiento de cuota
                    </label>

                    <input
                        type="date"
                        name="fecha_vencimiento_cuota"
                        id="fecha_vencimiento_cuota"
                        value="{{ old('fecha_vencimiento_cuota', $cliente->fecha_vencimiento_cuota) }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                    >

                </div>

                {{-- MONTO DE CUOTA --}}
                <div>

                    <label for="monto_cuota"
                           class="block text-sm font-bold text-stone-700">
                        Monto de la cuota mensual
                    </label>

                    <input
                        type="number"
                        name="monto_cuota"
                        id="monto_cuota"
                        min="0"
                        step="0.01"
                        value="{{ old('monto_cuota', $cliente->monto_cuota) }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                        placeholder="Ej: 80000"
                    >

                </div>

                {{-- BOTONES --}}
                <div class="flex gap-2 pt-2 flex-wrap font-bold">

                    <button
                        type="submit"
                        :disabled="cargando"
                        class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm text-white hover:bg-orange-600 transition cursor-pointer active:scale-95 disabled:opacity-50"
                    >

                        <span x-show="!cargando">
                            ✔ Actualizar socio
                        </span>

                        <span x-show="cargando" style="display: none;">
                            ⏳ Actualizando...
                        </span>

                    </button>

                    <a
                        href="{{ route('clientes.index') }}"
                        style="background:black;color:white;border-radius:14px;padding:8px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;"
                    >
                        ← Volver
                    </a>

                </div>

            </form>

        </div>

    </div>

</x-layouts::app>
