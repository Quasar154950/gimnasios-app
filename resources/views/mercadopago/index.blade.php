<x-layouts::app :title="'Mercado Pago'">

    <div class="max-w-3xl mx-auto p-4 space-y-6">

        {{-- ENCABEZADO --}}
        <div class="bg-stone-200 dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl shadow-sm p-6">

            <h1 class="text-2xl font-black text-stone-900 dark:text-stone-100">
                💳 Mercado Pago
            </h1>

            <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                Configurá tu cuenta de Mercado Pago para cobrar cuotas online desde la app.
            </p>

        </div>

        {{-- ALERTA --}}
        <div class="rounded-xl border border-blue-300 bg-blue-50 text-blue-800 p-4 text-sm">

            ⚠️ Próximamente vas a poder cobrar cuotas online directamente desde la app del gimnasio.

        </div>

        {{-- FORMULARIO --}}
        <form
            method="POST"
            action="{{ route('mercadopago.update') }}"
            class="bg-stone-200 dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl shadow-sm p-6 space-y-6"
        >
            @csrf

            {{-- ACTIVAR --}}
            <div class="flex items-center justify-between gap-4">

                <div>

                    <h2 class="font-black text-stone-900 dark:text-stone-100">
                        Activar pagos online
                    </h2>

                    <p class="text-sm text-stone-600 dark:text-stone-300 mt-1">
                        Permití que tus socios paguen cuotas online.
                    </p>

                </div>

                <label class="inline-flex items-center cursor-pointer">

                    <input
                        type="checkbox"
                        name="mercadopago_enabled"
                        class="rounded border-stone-300 text-orange-500 focus:ring-orange-500"
                        {{ $user->mercadopago_enabled ? 'checked' : '' }}
                    >

                </label>

            </div>

            {{-- PUBLIC KEY --}}
            <div>

                <label class="block text-sm font-black text-stone-700 dark:text-stone-200 mb-2">
                    Public Key
                </label>

                <input
                    type="text"
                    name="mercadopago_public_key"
                    value="{{ old('mercadopago_public_key', $user->mercadopago_public_key) }}"
                    class="w-full rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 px-4 py-3 text-sm text-stone-900 dark:text-stone-100"
                    placeholder="APP_USR-XXXXXXXXXXXX"
                >

            </div>

            {{-- ACCESS TOKEN --}}
            <div>

                <label class="block text-sm font-black text-stone-700 dark:text-stone-200 mb-2">
                    Access Token
                </label>

                <textarea
                    name="mercadopago_access_token"
                    rows="4"
                    class="w-full rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 px-4 py-3 text-sm text-stone-900 dark:text-stone-100"
                    placeholder="APP_USR-XXXXXXXXXXXX"
                >{{ old('mercadopago_access_token', $user->mercadopago_access_token) }}</textarea>

            </div>

            {{-- SANDBOX --}}
            <div class="flex items-center justify-between gap-4">

                <div>

                    <h2 class="font-black text-stone-900 dark:text-stone-100">
                        Modo prueba (Sandbox)
                    </h2>

                    <p class="text-sm text-stone-600 dark:text-stone-300 mt-1">
                        Usar Mercado Pago en modo prueba.
                    </p>

                </div>

                <label class="inline-flex items-center cursor-pointer">

                    <input
                        type="checkbox"
                        name="mercadopago_sandbox"
                        class="rounded border-stone-300 text-orange-500 focus:ring-orange-500"
                        {{ $user->mercadopago_sandbox ? 'checked' : '' }}
                    >

                </label>

            </div>

            {{-- BOTÓN --}}
            <button
                type="submit"
                class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 transition px-5 py-3 text-sm font-black text-white"
            >
                Guardar configuración
            </button>

        </form>

    </div>

</x-layouts::app>
