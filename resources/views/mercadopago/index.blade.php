<x-layouts::app :title="'Mercado Pago'">

    <div class="min-h-screen bg-slate-950 px-4 py-6">

        <div class="mx-auto max-w-4xl space-y-6">

            {{-- MENSAJES --}}
            @if (session('success'))

                <div class="rounded-2xl border border-emerald-800 bg-emerald-950/40 p-4 text-sm font-bold text-emerald-300 shadow-lg">
                    ✅ {{ session('success') }}
                </div>

            @endif

            @if (session('error'))

                <div class="rounded-2xl border border-red-800 bg-red-950/40 p-4 text-sm font-bold text-red-300 shadow-lg">
                    ⚠️ {{ session('error') }}
                </div>

            @endif


            {{-- ENCABEZADO --}}
            <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

                <div class="flex items-start gap-4">

                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sky-950 text-2xl shadow">
                        💳
                    </div>

                    <div>

                        <div class="mb-2 inline-flex rounded-full bg-sky-950 px-3 py-1 text-xs font-black uppercase text-sky-300">
                            🔐 Integración de pagos
                        </div>

                        <h1 class="text-3xl font-black text-white">
                            Mercado Pago
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-400">
                            Conectá la cuenta de Mercado Pago de tu gimnasio para recibir directamente los pagos de las cuotas de tus socios.
                        </p>

                    </div>

                </div>

            </section>


            {{-- CONEXIÓN MERCADO PAGO --}}
            <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

                <div class="mx-auto max-w-2xl text-center">

                    @if ($user->mercadopago_connected_at && $user->mercadopago_access_token)

                        {{-- CUENTA CONECTADA --}}
                        <div class="mb-5">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-950 text-2xl">
                                ✅
                            </div>

                            <h2 class="mt-4 text-2xl font-black text-white">
                                Cuenta conectada
                            </h2>

                            <p class="mt-2 text-sm text-zinc-400">
                                Tu gimnasio ya puede recibir pagos mediante Mercado Pago.
                            </p>

                        </div>


                        <div class="flex min-h-[92px] w-full items-center justify-center overflow-hidden rounded-2xl border border-emerald-800 bg-white px-4 shadow-lg">

                            <img
                                src="{{ asset('images/mp-logo-web.png') }}"
                                alt="Mercado Pago"
                                class="max-h-16 w-64 object-contain"
                            >

                        </div>


                        <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-emerald-800 bg-emerald-950/40 px-4 py-2">

                            <span class="h-3 w-3 rounded-full bg-emerald-500"></span>

                            <span class="text-sm font-black text-emerald-300">
                                Estado: Conectado
                            </span>

                        </div>


                        @if ($user->mercadopago_connected_at)

                            <p class="mt-3 text-xs text-zinc-500">
                                Conectado el
                                {{ $user->mercadopago_connected_at->format('d/m/Y H:i') }}
                            </p>

                        @endif


                        {{-- DESCONECTAR --}}
                        <form
                            method="POST"
                            action="{{ route('mercadopago.desconectar') }}"
                            class="mt-6"
                            onsubmit="
                                if (!confirm('¿Seguro que querés desconectar Mercado Pago? Los socios dejarán de poder pagar online.')) {
                                    return false;
                                }

                                const boton = this.querySelector('button');
                                const normal = this.querySelector('.mp-desconectar-normal');
                                const cargando = this.querySelector('.mp-desconectar-cargando');

                                boton.disabled = true;
                                boton.classList.add('cursor-wait', 'opacity-90');

                                normal.classList.add('hidden');

                                cargando.classList.remove('hidden');
                                cargando.classList.add('flex');

                                return true;
                            "
                        >
                            @csrf

                            <button
                                type="submit"
                                class="inline-flex min-h-[52px] w-full cursor-pointer items-center justify-center rounded-2xl border border-red-800 bg-red-950/30 px-5 py-3 text-sm font-black text-red-300 shadow transition duration-200 hover:-translate-y-0.5 hover:bg-red-950/60 hover:shadow-lg active:scale-[0.97] disabled:pointer-events-none sm:w-auto"
                            >

                                {{-- NORMAL --}}
                                <span class="mp-desconectar-normal">
                                    🔌 Desconectar Mercado Pago
                                </span>

                                {{-- CARGANDO --}}
                                <span class="mp-desconectar-cargando hidden items-center justify-center gap-3">

                                    <svg
                                        class="h-5 w-5 animate-spin text-red-300"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>

                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                        ></path>
                                    </svg>

                                    <span>
                                        Desconectando…
                                    </span>

                                </span>

                            </button>

                        </form>

                    @else

                        {{-- CUENTA NO CONECTADA --}}
                        <div class="mb-5">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-950 text-2xl">
                                🔗
                            </div>

                            <h2 class="mt-4 text-2xl font-black text-white">
                                Conectá tu cuenta
                            </h2>

                            <p class="mt-2 text-sm text-zinc-400">
                                Vinculá Mercado Pago para recibir los pagos de tus socios.
                            </p>

                        </div>


                        {{-- BOTÓN CONECTAR --}}
                        <a
                            href="{{ route('mercadopago.conectar') }}"
                            class="mp-conectar group flex min-h-[92px] w-full cursor-pointer items-center justify-center overflow-hidden rounded-2xl border border-sky-700 bg-white px-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl active:scale-[0.97]"
                            onclick="
                                const normal = this.querySelector('.mp-conectar-normal');
                                const cargando = this.querySelector('.mp-conectar-cargando');

                                this.classList.add('pointer-events-none', 'cursor-wait', 'opacity-90');

                                normal.classList.add('hidden');

                                cargando.classList.remove('hidden');
                                cargando.classList.add('flex');
                            "
                        >

                            {{-- NORMAL --}}
                            <div class="mp-conectar-normal flex items-center justify-center">

                                <img
                                    src="{{ asset('images/mp-logo-web.png') }}"
                                    alt="Conectar con Mercado Pago"
                                    class="max-h-16 w-64 object-contain transition duration-200 group-hover:scale-105"
                                >

                            </div>


                            {{-- CARGANDO --}}
                            <div class="mp-conectar-cargando hidden items-center justify-center gap-3">

                                <svg
                                    class="h-7 w-7 animate-spin text-sky-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >

                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>

                                </svg>

                                <span class="font-black text-sky-700">
                                    Conectando con Mercado Pago…
                                </span>

                            </div>

                        </a>


                        <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-red-800 bg-red-950/30 px-4 py-2">

                            <span class="h-3 w-3 rounded-full bg-red-500"></span>

                            <span class="text-sm font-black text-red-300">
                                Estado: No conectado
                            </span>

                        </div>

                    @endif


                    <div class="mt-5 flex items-center justify-center gap-2 text-sm text-zinc-500">

                        <span>🔒</span>

                        <span>
                            Conexión segura mediante Mercado Pago
                        </span>

                    </div>

                </div>

            </section>


            {{-- CÓMO FUNCIONA --}}
            <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

                <h2 class="text-xl font-black text-white">
                    ¿Cómo funciona?
                </h2>

                <p class="mt-1 text-sm text-zinc-500">
                    El proceso se realiza en tres pasos simples.
                </p>


                <div class="mt-5 grid gap-4 sm:grid-cols-3">

                    {{-- PASO 1 --}}
                    <div class="group rounded-2xl border border-zinc-800 bg-zinc-950 p-5 transition duration-200 hover:-translate-y-1 hover:border-sky-700 hover:shadow-xl">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-950 text-sm font-black text-sky-300">
                            1
                        </div>

                        <h3 class="mt-4 font-black text-white">
                            Conectás tu cuenta
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-zinc-500">
                            Autorizás la vinculación desde el sitio oficial de Mercado Pago.
                        </p>

                    </div>


                    {{-- PASO 2 --}}
                    <div class="group rounded-2xl border border-zinc-800 bg-zinc-950 p-5 transition duration-200 hover:-translate-y-1 hover:border-violet-700 hover:shadow-xl">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-950 text-sm font-black text-violet-300">
                            2
                        </div>

                        <h3 class="mt-4 font-black text-white">
                            El socio paga
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-zinc-500">
                            El socio podrá abonar su cuota online directamente desde la app.
                        </p>

                    </div>


                    {{-- PASO 3 --}}
                    <div class="group rounded-2xl border border-zinc-800 bg-zinc-950 p-5 transition duration-200 hover:-translate-y-1 hover:border-emerald-700 hover:shadow-xl">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-950 text-sm font-black text-emerald-300">
                            3
                        </div>

                        <h3 class="mt-4 font-black text-white">
                            Recibís el dinero
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-zinc-500">
                            El importe se acredita en la cuenta de Mercado Pago de tu gimnasio.
                        </p>

                    </div>

                </div>

            </section>


            {{-- SEGURIDAD --}}
            <section class="rounded-3xl border border-emerald-900 bg-emerald-950/30 p-6 shadow-lg">

                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-950 text-2xl">
                        🛡️
                    </div>

                    <div>

                        <h2 class="text-lg font-black text-emerald-200">
                            Conexión segura
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-emerald-300/80">
                            No tendrás que copiar ni compartir tu Public Key o Access Token.
                            La autorización se realiza mediante Mercado Pago OAuth.
                        </p>

                    </div>

                </div>

            </section>

        </div>

    </div>

</x-layouts::app>