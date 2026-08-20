<x-layouts::app>

    <div class="min-h-screen bg-slate-950 px-4 py-6">

        <div class="mx-auto max-w-4xl space-y-6">

            {{-- CABECERA --}}
            <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="mb-2 inline-flex items-center rounded-full bg-blue-950 px-3 py-1 text-xs font-black uppercase text-blue-300">
                            🔐 Suscripción
                        </div>

                        <h1 class="text-3xl font-black text-white">
                            Mi Suscripción
                        </h1>

                        <p class="mt-2 text-sm text-zinc-400">
                            Consultá el estado de tu plan y renovalo de forma segura.
                        </p>

                    </div>

                    <div>

                        @if($user->activo)

                            <span class="inline-flex items-center gap-2 rounded-full bg-green-950 px-4 py-2 text-sm font-black text-green-300">
                                ● Suscripción activa
                            </span>

                        @else

                            <span class="inline-flex items-center gap-2 rounded-full bg-red-950 px-4 py-2 text-sm font-black text-red-300">
                                ● Suscripción suspendida
                            </span>

                        @endif

                    </div>

                </div>

            </div>


            {{-- CÁLCULO DE DÍAS RESTANTES --}}
            @php

                if ($user->fecha_vencimiento) {

                    $dias = \Carbon\Carbon::now()
                        ->startOfDay()
                        ->diffInDays(
                            \Carbon\Carbon::parse($user->fecha_vencimiento)->startOfDay(),
                            false
                        );

                } else {

                    $dias = null;

                }

            @endphp


            {{-- INFORMACIÓN DEL PLAN --}}
            <div class="grid gap-4 md:grid-cols-2">

                {{-- PLAN --}}
                <div
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl"
                >

                    <div class="text-xs font-black uppercase text-zinc-500">
                        Plan contratado
                    </div>

                    <div class="mt-3 text-2xl font-black text-white">
                        {{ strtoupper($user->plan ?? 'Sin plan') }}
                    </div>

                </div>


                {{-- PRECIO --}}
                <div
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl"
                >

                    <div class="text-xs font-black uppercase text-zinc-500">
                        Precio mensual
                    </div>

                    <div class="mt-3 text-3xl font-black text-white">
                        ${{ number_format($user->precio_suscripcion ?? 0, 0, ',', '.') }}
                    </div>

                </div>


                {{-- VENCIMIENTO --}}
                <div
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl"
                >

                    <div class="text-xs font-black uppercase text-zinc-500">
                        Fecha de vencimiento
                    </div>

                    <div class="mt-3 text-xl font-black text-white">

                        {{ $user->fecha_vencimiento
                            ? \Carbon\Carbon::parse($user->fecha_vencimiento)->format('d/m/Y')
                            : 'Sin fecha' }}

                    </div>

                </div>


                {{-- TIEMPO RESTANTE --}}
                <div
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl"
                >

                    <div class="text-xs font-black uppercase text-zinc-500">
                        Tiempo restante
                    </div>

                    @if($dias === null)

                        <div class="mt-3 text-xl font-black text-zinc-400">
                            —
                        </div>

                    @elseif($dias < 0)

                        <div class="mt-3 text-xl font-black text-red-400">
                            Suscripción vencida
                        </div>

                    @elseif($dias <= 5)

                        <div class="mt-3 text-xl font-black text-yellow-300">
                            Te quedan {{ $dias }} días
                        </div>

                    @else

                        <div class="mt-3 text-xl font-black text-green-300">
                            Te quedan {{ $dias }} días
                        </div>

                    @endif

                </div>

            </div>


            {{-- BLOQUE DE PAGO --}}
            @if($user->precio_suscripcion > 0)

                <div class="rounded-3xl border border-blue-900/60 bg-zinc-900 p-6 shadow-xl">

                    {{-- CABECERA PAGO --}}
                    <div class="mb-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-950 text-2xl">
                                🛡️
                            </div>

                            <div>

                                <h2 class="text-xl font-black text-white">
                                    Renovar suscripción
                                </h2>

                                <p class="mt-1 text-sm text-zinc-400">
                                    Pago procesado de forma segura mediante Mercado Pago.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- IMPORTE --}}
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-5">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <div class="text-sm text-zinc-500">
                                    Importe a pagar
                                </div>

                                <div class="mt-1 text-3xl font-black text-white">
                                    ${{ number_format($user->precio_suscripcion ?? 0, 0, ',', '.') }}
                                </div>

                                <div class="mt-2 text-xs text-zinc-500">
                                    Renovación mensual de tu plan.
                                </div>

                            </div>


                            <div class="sm:text-right">

                                <div class="inline-flex items-center gap-2 rounded-full bg-green-950 px-3 py-1.5 text-xs font-bold text-green-300">
                                    🔒 Pago seguro
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- BOTÓN MERCADO PAGO --}}
                    <form
                        method="POST"
                        action="{{ route('suscripcion.pagar') }}"
                        class="mt-5"
                        onsubmit="
                            const boton = this.querySelector('button');
                            const normal = this.querySelector('.mp-normal');
                            const cargando = this.querySelector('.mp-cargando');

                            boton.disabled = true;
                            boton.classList.add('cursor-wait', 'opacity-90');

                            normal.classList.add('hidden');

                            cargando.classList.remove('hidden');
                            cargando.classList.add('flex');
                        "
                    >

                        @csrf

                        <button
                            type="submit"
                            class="group flex min-h-[78px] w-full cursor-pointer items-center justify-center
                                   rounded-2xl border border-blue-700 bg-white px-5 py-4
                                   shadow-lg transition duration-200
                                   hover:-translate-y-0.5 hover:shadow-xl
                                   active:scale-[0.97]
                                   disabled:pointer-events-none"
                        >

                            {{-- ESTADO NORMAL --}}
                            <div class="mp-normal flex items-center justify-center">

                                <img
                                    src="{{ asset('images/mp-logo-web.png') }}"
                                    alt="Pagar con Mercado Pago"
                                    class="max-h-14 w-56 object-contain transition duration-200 group-hover:scale-105"
                                >

                            </div>


                            {{-- ESTADO CONECTANDO --}}
                            <div class="mp-cargando hidden items-center justify-center gap-3">

                                <svg
                                    class="h-7 w-7 animate-spin text-blue-600"
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

                                <span class="text-sm font-black text-blue-700 sm:text-base">
                                    Conectando con Mercado Pago…
                                </span>

                            </div>

                        </button>

                    </form>


                    {{-- SEGURIDAD --}}
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs text-zinc-500">

                        <span>
                            🔐 Conexión segura
                        </span>

                        <span>
                            💳 Mercado Pago
                        </span>

                        <span>
                            ✅ Confirmación automática
                        </span>

                    </div>

                </div>

            @endif


        </div>

    </div>

</x-layouts::app>