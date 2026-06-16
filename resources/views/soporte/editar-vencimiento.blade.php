<x-layouts::app :title="'Editar vencimiento'">

    <div class="max-w-xl mx-auto space-y-5">

        <div class="rounded-xl border border-neutral-200 p-5 bg-white shadow-sm">
            <h1 class="text-2xl font-bold">
                ✏️ Editar vencimiento
            </h1>

            <p class="text-sm text-gray-500 mt-2">
                {{ $user->email }}
            </p>
        </div>

        <div class="rounded-xl border border-neutral-200 p-5 bg-white shadow-sm">

            <form method="POST" action="{{ route('soporte.guardar.vencimiento', $user) }}"
                  class="space-y-5">

                @csrf

                <div>
                    <label class="text-sm font-semibold">
                        Fecha de vencimiento
                    </label>

                    <input
                        type="date"
                        name="fecha_vencimiento"
                        value="{{ optional($user->fecha_vencimiento)->format('Y-m-d') }}"
                        class="w-full mt-2 border rounded-lg px-3 py-2"
                    >
                </div>

            <div>
    <label class="text-sm font-semibold">
        Plan
    </label>

    <select
        name="plan"
        class="w-full mt-2 border rounded-lg px-3 py-2"
    >
        <option value="basico" @selected($user->plan === 'basico')>Básico</option>
        <option value="pro" @selected($user->plan === 'pro')>Pro</option>
        <option value="premium" @selected($user->plan === 'premium')>Premium</option>
        <option value="personalizado" @selected($user->plan === 'personalizado')>Personalizado</option>
    </select>
</div>

<div>
    <label class="text-sm font-semibold">
        Precio suscripción
    </label>

    <input
        type="number"
        name="precio_suscripcion"
        value="{{ $user->precio_suscripcion }}"
        min="0"
        step="1"
        class="w-full mt-2 border rounded-lg px-3 py-2"
    >
</div>

                <button
                    class="px-5 py-2 rounded bg-green-600 hover:bg-green-700 text-white transition">
                    💾 Guardar cambios
                </button>

            </form>

        </div>

    </div>

</x-layouts::app>


