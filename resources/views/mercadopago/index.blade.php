<x-layouts::app :title="'Mercado Pago'">

    <div class="max-w-3xl mx-auto p-4 space-y-6">

        {{-- MENSAJES --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800
                        dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-300 bg-red-50 p-4 text-sm font-bold text-red-800
                        dark:border-red-800 dark:bg-red-950/30 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- ENCABEZADO --}}
        <div class="rounded-2xl border border-stone-300 dark:border-stone-700
                    bg-stone-200 dark:bg-stone-800 shadow-sm p-6">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center
                            rounded-xl bg-sky-500 text-2xl shadow-sm">
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
        <div class="rounded-2xl border border-stone-300 dark:border-stone-700
                    bg-stone-200 dark:bg-stone-800 shadow-sm p-6">

            <div class="max-w-xl mx-auto text-center">

                @if ($user->mercadopago_connected_at && $user->mercadopago_access_token)

                    {{-- CUENTA CONECTADA --}}
                    <h2 class="text-xl font-black text-stone-900 dark:text-stone-100">
                        Cuenta conectada
                    </h2>

                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                        Tu gimnasio ya puede recibir pagos mediante Mercado Pago.
                    </p>

                    <div class="mt-6 flex h-20 w-full items-center justify-center
                                overflow-hidden rounded-2xl border border-emerald-300
                                dark:border-emerald-700 bg-white px-4 shadow-sm">

                        <img
                            src="{{ asset('images/mp-logo-web.png') }}"
                            alt="Mercado Pago"
                            class="w-64 max-h-16 object-contain"
                        >
                    </div>

                    <div class="mt-5 inline-flex items-center gap-2 rounded-full
                                border border-emerald-300 dark:border-emerald-800
                                bg-emerald-50 dark:bg-emerald-950/30
                                px-4 py-2">

                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>

                        <span class="text-sm font-black text-emerald-700 dark:text-emerald-300">
                            Estado: Conectado
                        </span>
                    </div>

                    @if ($user->mercadopago_connected_at)
                        <p class="mt-3 text-xs text-stone-500 dark:text-stone-400">
                            Conectado el
                            {{ $user->mercadopago_connected_at->format('d/m/Y H:i') }}
                        </p>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('mercadopago.desconectar') }}"
                        class="mt-6"
                        onsubmit="return confirm('¿Seguro que querés desconectar Mercado Pago? Los socios dejarán de poder pagar online.');"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl
                                   border border-red-300 dark:border-red-800
                                   bg-red-50 dark:bg-red-950/30
                                   px-5 py-3 text-sm font-black
                                   text-red-700 dark:text-red-300
                                   transition hover:bg-red-100 dark:hover:bg-red-950/60"
                        >
                            Desconectar Mercado Pago
                        </button>
                    </form>

                @else

                    {{-- CUENTA NO CONECTADA --}}
                    <h2 class="text-xl font-black text-stone-900 dark:text-stone-100">
                        Conectá tu cuenta
                    </h2>

                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-300">
                        Vinculá Mercado Pago para recibir los pagos de tus socios.
                    </p>

                    <a
                        href="{{ route('mercadopago.conectar') }}"
                        class="mt-6 flex h-20 w-full items-center justify-center
                               overflow-hidden rounded-2xl
                               border border-sky-300 dark:border-sky-700
                               bg-white px-4 shadow-sm
                               transition hover:scale-[1.01] hover:shadow-md"
                    >
                        <img
                            src="{{ asset('images/mp-logo-web.png') }}"
                            alt="Conectar con Mercado Pago"
                            class="w-64 max-h-16 object-contain"
                        >
                    </a>

                    <div class="mt-5 inline-flex items-center gap-2 rounded-full
                                border border-red-300 dark:border-red-800
                                bg-red-50 dark:bg-red-950/30
                                px-4 py-2">

                        <span class="h-3 w-3 rounded-full bg-red-500"></span>

                        <span class="text-sm font-black text-red-700 dark:text-red-300">
                            Estado: No conectado
                        </span>
                    </div>

                @endif

                <div class="mt-4 flex items-center justify-center gap-2
                            text-sm text-stone-500 dark:text-stone-400">

                    <span>🔒</span>

                    <span>
                        Conexión segura mediante Mercado Pago
                    </span>
                </div>

            </div>

        </div>

        {{-- FUNCIONAMIENTO --}}
        <div class="rounded-2xl border border-stone-300 dark:border-stone-700
                    bg-stone-200 dark:bg-stone-800 shadow-sm p-6">

            <h2 class="text-lg font-black text-stone-900 dark:text-stone-100">
                ¿Cómo funciona?
            </h2>

            <div class="mt-5 grid gap-4 sm:grid-cols-3">

                {{-- PASO 1 --}}
                <div class="rounded-xl border border-stone-300 dark:border-stone-700
                            bg-white dark:bg-stone-900 p-4">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full
                                bg-sky-100 dark:bg-sky-950 text-sm font-black
                                text-sky-700 dark:text-sky-300">
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
                <div class="rounded-xl border border-stone-300 dark:border-stone-700
                            bg-white dark:bg-stone-900 p-4">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full
                                bg-sky-100 dark:bg-sky-950 text-sm font-black
                                text-sky-700 dark:text-sky-300">
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
                <div class="rounded-xl border border-stone-300 dark:border-stone-700
                            bg-white dark:bg-stone-900 p-4">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full
                                bg-sky-100 dark:bg-sky-950 text-sm font-black
                                text-sky-700 dark:text-sky-300">
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
        <div class="rounded-2xl border border-emerald-300 dark:border-emerald-800
                    bg-emerald-50 dark:bg-emerald-950/30 p-5">

            <div class="flex items-start gap-3">

                <span class="text-xl">🛡️</span>

                <div>
                    <h2 class="font-black text-emerald-900 dark:text-emerald-200">
                        Conexión segura
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-emerald-800 dark:text-emerald-300">
                        No tendrás que copiar ni compartir tu Public Key o Access Token.
                        La autorización se realiza mediante Mercado Pago OAuth.
                    </p>
                </div>

            </div>

        </div>

    </div>

</x-layouts::app>
