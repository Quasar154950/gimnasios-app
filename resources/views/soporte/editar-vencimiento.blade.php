<x-layouts::app :title="'Editar suscripción'">

    <div class="max-w-xl mx-auto space-y-5">

        <div class="rounded-xl border border-gray-200 p-5 bg-white shadow-sm">
            <h1 class="text-2xl font-bold" style="color: #111827 !important;">
                ✏️ Editar suscripción
            </h1>

            <p class="text-sm mt-2" style="color: #374151 !important;">
                {{ $user->email }}
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 p-5 bg-white shadow-sm">

            <form method="POST"
                  action="{{ route('soporte.guardar.vencimiento', $user) }}"
                  class="space-y-5">

                @csrf

                <div>
                    <label class="block text-sm font-bold"
                           style="color: #111827 !important;">
                        Fecha de vencimiento
                    </label>

                    <input
                        type="date"
                        name="fecha_vencimiento"
                        value="{{ optional($user->fecha_vencimiento)->format('Y-m-d') }}"
                        class="w-full mt-2 border border-gray-400 rounded-lg px-3 py-2 font-semibold"
                        style="color: #111827 !important;
                               background-color: #ffffff !important;"
                    >
                </div>

                <div>
                    <label class="block text-sm font-bold"
                           style="color: #111827 !important;">
                        Plan
                    </label>

                    <select
                        name="plan"
                        class="w-full mt-2 border border-gray-400 rounded-lg px-3 py-2 font-semibold"
                        style="color: #111827 !important;
                               background-color: #ffffff !important;"
                    >
                        <option value="basico" @selected($user->plan === 'basico')>
                            Básico
                        </option>

                        <option value="pro" @selected($user->plan === 'pro')>
                            Pro
                        </option>

                        <option value="premium" @selected($user->plan === 'premium')>
                            Premium
                        </option>

                        <option value="personalizado" @selected($user->plan === 'personalizado')>
                            Personalizado
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold"
                           style="color: #111827 !important;">
                        Precio suscripción
                    </label>

                    <input
                        type="number"
                        name="precio_suscripcion"
                        value="{{ $user->precio_suscripcion }}"
                        min="0"
                        step="1"
                        class="w-full mt-2 border border-gray-400 rounded-lg px-3 py-2 font-semibold"
                        style="color: #111827 !important;
                               background-color: #ffffff !important;"
                    >

                    <p class="text-xs mt-1"
                       style="color: #374151 !important;">
                        Ingresar solo números. Ejemplo: 15000
                    </p>
                </div>

                <button
                    class="px-5 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-semibold transition">
                    💾 Guardar cambios
                </button>

            </form>

        </div>

    </div>

</x-layouts::app>


