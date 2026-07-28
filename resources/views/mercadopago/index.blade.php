<x-layouts::app :title="'Mercado Pago'">

    <div class="max-w-3xl mx-auto p-4 space-y-6">

        {{-- ENCABEZADO --}}
        <div class="rounded-2xl border border-stone-300 dark:border-stone-700 bg-stone-200 dark:bg-stone-800 shadow-sm p-6">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-2xl shadow-sm">
                    💳
                </div>

                <div>

                    <h1 class="text-2xl font-black text-stone-900 dark:text-stone-100">
                        Mercado Pago
                    </h1>

                    <p class="mt-2 text-sm leading-6 text-stone-600 dark:text-stone-300">
                        Conectá la cuenta de Mercado Pago de tu gimnasio para recibir
                        directamente los pagos de las cuotas de tus socios.
                    </p>

                </div>

            </div>

        </div>

        {{-- CONEXIÓN MERCADO PAGO --}}
<div class="rounded-2xl border border-stone-300 dark:border-stone-700 bg-stone-200 dark:bg-stone-800 shadow-sm p-6">

    <div class="max-w-xl mx-auto text-center">

        <h2 class="text-xl font-black text-stone-900 dark:text-stone-100">
            Conectá tu cuenta
        </h2>

        <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
            Vinculá Mercado Pago para recibir los pagos de tus socios.
        </p>

        {{-- BOTÓN VISUAL: EL OAUTH SE AGREGARÁ DESPUÉS --}}
        <button
            type="button"
            disabled
            title="La conexión estará disponible próximamente"
            class="mt-6 flex w-full items-center justify-center rounded-2xl
                   border border-stone-300 dark:border-stone-600
                   bg-white px-6 py-4
                   shadow-sm opacity-80 cursor-not-allowed"
        >
            <img
    src="{{ asset('images/mp-logo-web.png') }}"
    alt="Mercado Pago"
    class="w-72 h-auto"
>
        </button>

        <div class="mt-5 inline-flex items-center gap-2 rounded-full
                    border border-red-300 dark:border-red-800
                    bg-red-50 dark:bg-red-950/30
                    px-4 py-2">

            <span class="h-3 w-3 rounded-full bg-red-500"></span>

            <span class="text-sm font-black text-red-700 dark:text-red-300">
                Estado: No conectado
            </span>

        </div>

        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-stone-500 dark:text-stone-400">
            <span>🔒</span>

            <span>
                Conexión segura mediante Mercado Pago
            </span>
        </div>

    </div>

</div>

        {{-- FUNCIONAMIENTO --}}
        <div class="rounded-2xl border border-stone-300 dark:border-stone-700 bg-stone-200 dark:bg-stone-800 shadow-sm p-6">

            <h2 class="text-lg font-black text-stone-900 dark:text-stone-100">
                ¿Cómo funcionará?
            </h2>

            <div class="mt-5 grid gap-4 sm:grid-cols-3">

                {{-- PASO 1 --}}
                <div class="rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-stone-900 p-4">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-950 text-sm font-black text-sky-700 dark:text-sky-300">
                        1
                    </div>

                    <h3 class="mt-3 font-black text-stone-900 dark:text-stone-100">
                        Conectás tu cuenta
                    </h3>

                    <p class="mt-2 text-sm leading-5 text-stone-600 dark:text-stone-300">
                        Autorizás la vinculación desde el sitio oficial de Mercado Pago.
                    </p>

                </div>

                {{-- PASO 2 --}}
                <div class="rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-stone-900 p-4">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-950 text-sm font-black text-sky-700 dark:text-sky-300">
                        2
                    </div>

                    <h3 class="mt-3 font-black text-stone-900 dark:text-stone-100">
                        El socio paga
                    </h3>

                    <p class="mt-2 text-sm leading-5 text-stone-600 dark:text-stone-300">
                        El socio podrá abonar su cuota online directamente desde la app.
                    </p>

                </div>

                {{-- PASO 3 --}}
                <div class="rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-stone-900 p-4">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-950 text-sm font-black text-sky-700 dark:text-sky-300">
                        3
                    </div>

                    <h3 class="mt-3 font-black text-stone-900 dark:text-stone-100">
                        Recibís el dinero
                    </h3>

                    <p class="mt-2 text-sm leading-5 text-stone-600 dark:text-stone-300">
                        El importe se acredita en la cuenta de Mercado Pago de tu gimnasio.
                    </p>

                </div>

            </div>

        </div>


        {{-- SEGURIDAD --}}
        <div class="rounded-2xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 p-5">

            <div class="flex items-start gap-3">

                <span class="text-xl">🛡️</span>

                <div>

                    <h2 class="font-black text-emerald-900 dark:text-emerald-200">
                        Conexión segura
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-emerald-800 dark:text-emerald-300">
                        No tendrás que copiar ni compartir tu Public Key o Access Token.
                        La autorización se realizará mediante Mercado Pago OAuth.
                    </p>

                </div>

            </div>

        </div>


        {{-- PRÓXIMAMENTE --}}
        <div class="rounded-2xl border border-blue-300 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/30 p-5">

            <div class="flex items-start gap-3">

                <span class="text-xl">🚧</span>

                <div>

                    <h2 class="font-black text-blue-900 dark:text-blue-200">
                        Integración en preparación
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-blue-800 dark:text-blue-300">
                        La pantalla ya está preparada. En la próxima etapa activaremos
                        la conexión, las pruebas y el cobro real desde Flutter.
                    </p>

                </div>

            </div>

        </div>

    </div>

</x-layouts::app>
