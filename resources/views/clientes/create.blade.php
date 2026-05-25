<x-layouts::app :title="__('Nuevo Socio')">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm">

            <h1 class="text-2xl font-bold text-stone-900">
                Nuevo Socio
            </h1>

            <p class="mt-2 text-sm text-stone-600">
                Completá los datos para agregar un nuevo socio al sistema.
            </p>

        </div>

        {{-- FORMULARIO --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm">

            <x-alert-error />

            <form method="POST"
                  action="{{ route('clientes.store') }}"
                  class="space-y-4"
                  x-data="{ cargando: false }"
                  x-on:submit="cargando = true">

                @csrf

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
                        value="{{ old('nombre') }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                        required
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
        value="{{ old('dni') }}"
        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
        required
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
                        value="{{ old('telefono') }}"
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
                        value="{{ old('email') }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                        required
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
                        value="{{ old('direccion') }}"
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
                        value="{{ old('fecha_vencimiento_cuota') }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500 transition"
                    >

                </div>

                {{-- BOTONES --}}
                <div class="flex gap-2 pt-2 flex-wrap font-bold">

                    <button
                        type="submit"
                        :disabled="cargando"
                        class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm text-white hover:bg-orange-600 transition cursor-pointer active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    >

                        <span x-show="!cargando">
                            ✔ Guardar socio
                        </span>

                        <span x-show="cargando" style="display: none;">
                            ⏳ Guardando...
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