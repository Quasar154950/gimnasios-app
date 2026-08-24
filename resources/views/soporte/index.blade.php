<x-layouts::app :title="'MCTandil · Soporte SaaS'">

    <div class="space-y-6 text-left">

        {{-- CABECERA --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl md:p-6">

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">

                    <div class="mb-2 inline-flex items-center rounded-full border border-blue-900 bg-blue-950/40 px-3 py-1 text-xs font-black text-blue-300">
                        🛠 MCTandil · Soporte SaaS
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-white md:text-3xl">
                        Centro de operaciones
                    </h1>

                    <p class="mt-1 max-w-2xl text-sm text-zinc-400">
                        Administración central de clientes, suscripciones, accesos, pagos y servicios SaaS.
                    </p>

                </div>

                <div class="flex shrink-0 items-center gap-3">

                    <div class="rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-right shadow-sm">
                        <div class="text-[10px] font-black uppercase tracking-wider text-zinc-500">
                            Plataforma
                        </div>

                        <div class="mt-1 text-sm font-black text-zinc-200">
                            MCTandil
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- MENSAJE PASSWORD GENERADA --}}
        @if(session('password_generada'))

            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 7000)"
                class="rounded-2xl border border-blue-800 bg-blue-950/40 p-4 text-sm font-bold text-blue-300 shadow-lg"
            >
                🔑 Nueva contraseña generada:
                <span class="font-black text-white">
                    {{ session('password_generada') }}
                </span>
            </div>

        @endif


        {{-- SUCCESS POR SESSION --}}
        @if(session('success'))

            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                class="rounded-2xl border border-green-800 bg-green-950/40 p-4 text-sm font-bold text-green-300 shadow-lg"
            >
                ✅ {{ session('success') }}
            </div>

        @endif


        {{-- ERROR POR SESSION --}}
        @if(session('error'))

            <div class="rounded-2xl border border-red-800 bg-red-950/40 p-4 text-sm font-bold text-red-300 shadow-lg">
                ❌ {{ session('error') }}
            </div>

        @endif


        {{-- SUCCESS POR QUERY STRING --}}
        @if(request('success'))

            <div class="rounded-2xl border border-green-800 bg-green-950/40 p-4 text-sm font-bold text-green-300 shadow-lg">
                ✅ {{ request('success') }}
            </div>

        @endif


        {{-- ERROR POR QUERY STRING --}}
        @if(request('error'))

            <div class="rounded-2xl border border-red-800 bg-red-950/40 p-4 text-sm font-bold text-red-300 shadow-lg">
                ❌ {{ request('error') }}
            </div>

        @endif


        {{-- MÉTRICAS --}}
        @php

            $users = \App\Models\User::where('role', 'abogado')
                ->where('email', '!=', 'soporte@tuempresa.com')
                ->orderBy('email')
                ->get();

            $totalEstudios = $users->count();

            $activos = $users
                ->where('activo', true)
                ->count();

            $inactivos = $users
                ->where('activo', false)
                ->count();

            $vencidos = $users->filter(
                fn ($u) =>
                    $u->fecha_vencimiento &&
                    now()->greaterThan($u->fecha_vencimiento)
            )->count();

            $porVencer = $users->filter(function ($u) {

                if (!$u->fecha_vencimiento) {
                    return false;
                }

                $dias = max(
                    0,
                    now()
                        ->startOfDay()
                        ->diffInDays(
                            $u->fecha_vencimiento->startOfDay(),
                            false
                        )
                );

                return $dias > 0 && $dias <= 7;

            })->count();

            $abogados = $users
                ->where('tipo_app', 'abogados')
                ->count();

            $gimnasios = $users
                ->where('tipo_app', 'gimnasios')
                ->count();

        @endphp


        {{-- TARJETAS MÉTRICAS --}}
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-7">

            {{-- TOTAL --}}
            <div class="rounded-2xl border border-blue-900/60 bg-blue-950/30 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-blue-400">
                            Total
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $totalEstudios }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        🧩
                    </div>

                </div>

            </div>


            {{-- ACTIVOS --}}
            <div class="rounded-2xl border border-green-900/60 bg-green-950/30 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-green-400">
                            Activos
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $activos }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        🟢
                    </div>

                </div>

            </div>


            {{-- INACTIVOS --}}
            <div class="rounded-2xl border border-zinc-700 bg-zinc-800/70 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-zinc-400">
                            Inactivos
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $inactivos }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        ⚪
                    </div>

                </div>

            </div>


            {{-- VENCIDOS --}}
            <div class="rounded-2xl border border-red-900/60 bg-red-950/30 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-red-400">
                            Vencidos
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $vencidos }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        🔴
                    </div>

                </div>

            </div>


            {{-- POR VENCER --}}
            <div class="rounded-2xl border border-yellow-900/60 bg-yellow-950/30 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-yellow-400">
                            Por vencer
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $porVencer }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        🟡
                    </div>

                </div>

            </div>


            {{-- ABOGADOS --}}
            <div class="rounded-2xl border border-indigo-900/60 bg-indigo-950/30 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-indigo-400">
                            Abogados
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $abogados }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        ⚖️
                    </div>

                </div>

            </div>


            {{-- GIMNASIOS --}}
            <div class="rounded-2xl border border-orange-900/60 bg-orange-950/30 p-4 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">

                <div class="flex items-center justify-between">

                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-orange-400">
                            Gimnasios
                        </div>

                        <div class="mt-1 text-2xl font-black text-white">
                            {{ $gimnasios }}
                        </div>
                    </div>

                    <div class="text-2xl">
                        🏋️
                    </div>

                </div>

            </div>

        </div>


        {{-- CLIENTES SAAS --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl md:p-6">

            {{-- TITULO --}}
            <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">

                <div>

                    <div class="mb-2 inline-flex items-center rounded-full bg-zinc-800 px-3 py-1 text-xs font-black text-zinc-300">
                        🧑‍💼 Clientes SaaS
                    </div>

                    <h2 class="text-xl font-black text-white">
                        Administración de clientes
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Gestioná accesos, vencimientos, cobros y servicios desde un solo lugar.
                    </p>

                </div>

                <div class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-xs font-black text-zinc-400">
                    {{ $totalEstudios }} clientes
                </div>

            </div>


            {{-- LISTADO --}}
            <div class="space-y-4">

                @foreach($users as $user)

                    @php

                        $vencido =
                            $user->fecha_vencimiento &&
                            now()->greaterThan(
                                $user->fecha_vencimiento
                            );

                        $diasRestantes =
                            $user->fecha_vencimiento
                                ? max(
                                    0,
                                    now()
                                        ->startOfDay()
                                        ->diffInDays(
                                            $user
                                                ->fecha_vencimiento
                                                ->startOfDay(),
                                            false
                                        )
                                )
                                : null;

                        if (!$user->activo) {
                            $estado = 'Inactivo';
                        } elseif ($vencido) {
                            $estado = 'Vencido';
                        } else {
                            $estado = 'Vigente';
                        }

                        if (is_null($diasRestantes)) {
                            $colorEstado =
                                'text-zinc-400 bg-zinc-800 border-zinc-700';
                        } elseif ($diasRestantes > 10) {
                            $colorEstado =
                                'text-green-300 bg-green-950/40 border-green-900';
                        } elseif ($diasRestantes > 3) {
                            $colorEstado =
                                'text-yellow-300 bg-yellow-950/40 border-yellow-900';
                        } else {
                            $colorEstado =
                                'text-red-300 bg-red-950/40 border-red-900';
                        }

                    @endphp


                    <div
                        class="rounded-3xl border border-zinc-800 bg-zinc-950 p-4 shadow-lg transition duration-200 hover:border-zinc-700 hover:shadow-xl md:p-5"
                    >

                        {{-- CABECERA CLIENTE --}}
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                            <div class="min-w-0">

                                {{-- EMAIL + TIPO --}}
                                <div class="flex flex-wrap items-center gap-2">

                                    <div class="min-w-0 break-all text-base font-black text-white">
                                        {{ $user->email }}
                                    </div>

                                    @if($user->tipo_app === 'abogados')

                                        <span class="inline-flex items-center rounded-full border border-indigo-900 bg-indigo-950/50 px-2.5 py-1 text-[10px] font-black text-indigo-300">
                                            ⚖️ ABOGADOS
                                        </span>

                                    @endif


                                    @if($user->tipo_app === 'gimnasios')

                                        <span class="inline-flex items-center rounded-full border border-orange-900 bg-orange-950/50 px-2.5 py-1 text-[10px] font-black text-orange-300">
                                            🏋️ GIMNASIOS
                                        </span>

                                    @endif

                                </div>


                                {{-- PLAN + PRECIO --}}
                                <div class="mt-3 flex flex-wrap items-center gap-2">

                                    <span class="inline-flex items-center rounded-full border border-zinc-700 bg-zinc-900 px-2.5 py-1 text-[10px] font-bold text-zinc-300">
                                        📦 {{ strtoupper($user->plan) }}
                                    </span>

                                    <span class="inline-flex items-center rounded-full border border-emerald-900 bg-emerald-950/40 px-2.5 py-1 text-[10px] font-bold text-emerald-300">
                                        💰 ${{ number_format($user->precio_suscripcion, 0, ',', '.') }}
                                    </span>

                                </div>


                                {{-- VENCIMIENTO --}}
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">

                                    <span class="text-zinc-500">
                                        📅 Vence:
                                    </span>

                                    <span class="font-black text-zinc-300">
                                        {{ $user->fecha_vencimiento
                                            ? $user->fecha_vencimiento->format('d/m/Y')
                                            : 'Sin fecha'
                                        }}
                                    </span>

                                </div>


                                {{-- DIAS RESTANTES --}}
                                <div class="mt-2">

                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-black {{ $colorEstado }}"
                                    >
                                        ⏳
                                        {{ is_null($diasRestantes)
                                            ? 'Sin vencimiento definido'
                                            : $diasRestantes . ' días restantes'
                                        }}
                                    </span>

                                </div>

                            </div>


                            {{-- ESTADO --}}
                            <div>

                                @if(!$user->activo)

                                    <span class="inline-flex items-center rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-black text-zinc-300">
                                        ⚪ INACTIVO
                                    </span>

                                @elseif($vencido)

                                    <span class="inline-flex items-center rounded-full border border-red-900 bg-red-950/40 px-3 py-1.5 text-xs font-black text-red-300">
                                        🔴 VENCIDO
                                    </span>

                                @else

                                    <span class="inline-flex items-center rounded-full border border-green-900 bg-green-950/40 px-3 py-1.5 text-xs font-black text-green-300">
                                        🟢 VIGENTE
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- SEPARADOR --}}
                        <div class="my-4 border-t border-zinc-800"></div>


                        {{-- ACCIONES --}}
                        <div class="client-actions overflow-x-auto overflow-y-hidden pb-2">

                            <div class="flex min-w-max flex-nowrap items-center gap-2 whitespace-nowrap">

                                {{-- RENOVAR --}}
                                <form
                                    method="POST"
                                    action="{{ route('renovar.suscripcion', $user) }}"
                                    class="shrink-0 support-action-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="
                                            if (!confirm('¿Seguro querés renovar 30 días?')) {
                                                return false;
                                            }

                                            supportProcessingButton(this, '⏳ Renovando...');
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-green-500 hover:shadow-xl
                                               active:scale-[0.95] disabled:cursor-wait disabled:opacity-60"
                                    >
                                        🔄 Renovar
                                    </button>

                                </form>


                                {{-- ACTIVAR / SUSPENDER --}}
                                <form
                                    method="POST"
                                    action="{{ route('toggle.activo', $user) }}"
                                    class="shrink-0 support-action-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="
                                            if (!confirm('¿Seguro querés cambiar el estado?')) {
                                                return false;
                                            }

                                            supportProcessingButton(
                                                this,
                                                '{{ $user->activo ? '⏳ Suspendiendo...' : '⏳ Activando...' }}'
                                            );
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:shadow-xl
                                               active:scale-[0.95] disabled:cursor-wait disabled:opacity-60
                                               {{ $user->activo
                                                    ? 'bg-red-600 hover:bg-red-500'
                                                    : 'bg-blue-600 hover:bg-blue-500'
                                               }}"
                                    >
                                        {{ $user->activo
                                            ? '⛔ Suspender'
                                            : '✅ Activar'
                                        }}
                                    </button>

                                </form>


                                {{-- RESET --}}
                                <form
                                    method="POST"
                                    action="{{ route('soporte.reset.password', $user) }}"
                                    class="shrink-0 support-action-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="
                                            if (!confirm('¿Resetear contraseña de este usuario?')) {
                                                return false;
                                            }

                                            supportProcessingButton(this, '⏳ Generando...');
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-blue-500 hover:shadow-xl
                                               active:scale-[0.95] disabled:cursor-wait disabled:opacity-60"
                                    >
                                        🔑 Reset
                                    </button>

                                </form>


                                {{-- EDITAR VENCIMIENTO --}}
                                <a
                                    href="{{ route('soporte.editar.vencimiento', $user) }}"
                                    style="cursor: pointer !important;"
                                    class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-yellow-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                           transition duration-150 hover:-translate-y-0.5 hover:bg-yellow-500 hover:shadow-xl
                                           active:scale-[0.95]"
                                >
                                    ✏️ Editar vencimiento
                                </a>


                                {{-- COPIAR ACCESO --}}
                                <button
                                    type="button"
                                    onclick="
                                        navigator.clipboard.writeText(
                                            '{{ url('/estudio/' . $user->slug_estudio) }}'
                                        );

                                        supportCopiedButton(
                                            this,
                                            '✅ Acceso copiado'
                                        );
                                    "
                                    style="cursor: pointer !important;"
                                    class="support-action-button shrink-0 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                           transition duration-150 hover:-translate-y-0.5 hover:bg-indigo-500 hover:shadow-xl
                                           active:scale-[0.95]"
                                >
                                    📩 Copiar acceso
                                </button>


                                {{-- VER COMO USUARIO --}}
                                <form
                                    method="POST"
                                    action="{{ route('soporte.ver-como', $user) }}"
                                    class="shrink-0 support-action-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="
                                            if (!confirm('¿Entrar como este usuario?')) {
                                                return false;
                                            }

                                            supportProcessingButton(
                                                this,
                                                '⏳ Ingresando...'
                                            );
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-violet-500 hover:shadow-xl
                                               active:scale-[0.95] disabled:cursor-wait disabled:opacity-60"
                                    >
                                        👁 Ver usuario
                                    </button>

                                </form>


                                {{-- COBRAR SAAS --}}
                                <form
                                    method="POST"
                                    action="{{ route('soporte.saas.pagar', $user) }}"
                                    class="shrink-0 support-action-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="
                                            if (!confirm('¿Generar pago SaaS para este cliente?')) {
                                                return false;
                                            }

                                            supportProcessingButton(
                                                this,
                                                '⏳ Generando pago...'
                                            );
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-emerald-500 hover:shadow-xl
                                               active:scale-[0.95] disabled:cursor-wait disabled:opacity-60"
                                    >
                                        💳 Cobrar SaaS
                                    </button>

                                </form>


                                @php
                                    $ultimoPago = $user
                                        ->saasPagos()
                                        ->latest()
                                        ->first();
                                @endphp


                                @if($ultimoPago && $ultimoPago->checkout_url)

                                    {{-- ABRIR LINK --}}
                                    <a
                                        href="{{ $ultimoPago->checkout_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style="cursor: pointer !important;"
                                        class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-lime-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-lime-500 hover:shadow-xl
                                               active:scale-[0.95]"
                                    >
                                        🔗 Abrir link pago
                                    </a>


                                    {{-- COPIAR LINK --}}
                                    <button
                                        type="button"
                                        onclick="
                                            navigator.clipboard.writeText(
                                                '{{ $ultimoPago->checkout_url }}'
                                            );

                                            supportCopiedButton(
                                                this,
                                                '✅ Link copiado'
                                            );
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button shrink-0 inline-flex items-center gap-2 rounded-xl bg-zinc-700 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-600 hover:shadow-xl
                                               active:scale-[0.95]"
                                    >
                                        📋 Copiar link
                                    </button>

                                @endif


                                {{-- BACKUP --}}
                                <form
                                    method="POST"
                                    action="{{ route('soporte.backup') }}"
                                    class="shrink-0 support-action-form"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        onclick="
                                            if (!confirm('¿Generar backup del sistema?')) {
                                                return false;
                                            }

                                            supportProcessingButton(
                                                this,
                                                '⏳ Generando backup...'
                                            );
                                        "
                                        style="cursor: pointer !important;"
                                        class="support-action-button inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2.5 text-sm font-bold text-white shadow-md
                                               transition duration-150 hover:-translate-y-0.5 hover:bg-cyan-500 hover:shadow-xl
                                               active:scale-[0.95] disabled:cursor-wait disabled:opacity-60"
                                    >
                                        💾 Backup
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>


    {{-- ESTILOS --}}
    <style>

        .client-actions {
            scrollbar-width: thin;
            scrollbar-color: #52525b transparent;
        }

        .client-actions::-webkit-scrollbar {
            height: 6px;
        }

        .client-actions::-webkit-scrollbar-track {
            background: transparent;
        }

        .client-actions::-webkit-scrollbar-thumb {
            background: #52525b;
            border-radius: 999px;
        }

        .client-actions::-webkit-scrollbar-thumb:hover {
            background: #71717a;
        }

        .support-action-button {
            transform-origin: center;
        }

    </style>


    {{-- FEEDBACK VISUAL BOTONES --}}
    <script>

        function supportProcessingButton(button, processingText) {

            if (!button) {
                return;
            }

            button.dataset.originalText = button.innerHTML;

            button.innerHTML = processingText;

        }


        function supportCopiedButton(button, copiedText) {

            if (!button) {
                return;
            }

            const originalText = button.innerHTML;

            button.innerHTML = copiedText;

            button.disabled = true;

            setTimeout(function () {

                button.innerHTML = originalText;

                button.disabled = false;

            }, 1800);
        }

    </script>

</x-layouts::app>


