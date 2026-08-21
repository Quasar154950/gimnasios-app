<x-layouts::app :title="'Ficha de ' . $cliente->nombre">

    @php
        $hoy = \Carbon\Carbon::today();
        $vencimientoCuota = $cliente->fecha_vencimiento_cuota
            ? \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota)
            : null;
    @endphp

    <div class="w-full space-y-6 px-0 pb-10">

        {{-- CABECERA --}}
        <div class="overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900 shadow-xl">
            <div class="border-b border-zinc-800 bg-gradient-to-r from-zinc-950 via-zinc-900 to-orange-950/30 p-5 md:p-7">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                    <div class="min-w-0">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-orange-900/60 bg-orange-950/40 px-3 py-1 text-xs font-black text-orange-300">
                            👤 Ficha del socio
                        </div>

                        <h1 class="break-words text-2xl font-black text-white md:text-3xl">
                            {{ $cliente->nombre }}
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm text-zinc-400">
                            Información general, acceso, pagos, mensajes y notas internas del socio.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($vencimientoCuota)
                            @if($vencimientoCuota->lt($hoy))
                                <span class="inline-flex items-center rounded-full border border-red-900/70 bg-red-950/50 px-3 py-1.5 text-xs font-black text-red-300">
                                    🔴 Cuota vencida
                                </span>
                            @elseif($vencimientoCuota->lte($hoy->copy()->addDays(5)))
                                <span class="inline-flex items-center rounded-full border border-yellow-900/70 bg-yellow-950/50 px-3 py-1.5 text-xs font-black text-yellow-300">
                                    🟡 Próxima a vencer
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full border border-green-900/70 bg-green-950/50 px-3 py-1.5 text-xs font-black text-green-300">
                                    🟢 Cuota al día
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-black text-zinc-400">
                                ⚪ Sin vencimiento
                            </span>
                        @endif

                        @if($cliente->user)
                            <span class="inline-flex items-center rounded-full border border-emerald-900/70 bg-emerald-950/50 px-3 py-1.5 text-xs font-black text-emerald-300">
                                ✅ Acceso activado
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-black text-zinc-400">
                                ⏳ Sin acceso
                            </span>
                        @endif
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-1 gap-px bg-zinc-800 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-zinc-900 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">DNI</p>
                    <p class="mt-1 font-bold text-zinc-200">{{ $cliente->dni ?: 'Sin cargar' }}</p>
                </div>

                <div class="bg-zinc-900 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Teléfono</p>
                    <p class="mt-1 font-bold text-zinc-200">{{ $cliente->telefono ?: 'Sin cargar' }}</p>
                </div>

                <div class="bg-zinc-900 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Cuota mensual</p>
                    <p class="mt-1 font-bold text-zinc-200">
                        @if(! is_null($cliente->monto_cuota))
                            $ {{ number_format($cliente->monto_cuota, 0, ',', '.') }}
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>

                <div class="bg-zinc-900 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Vencimiento</p>
                    <p class="mt-1 font-bold text-zinc-200">
                        @if($vencimientoCuota)
                            {{ $vencimientoCuota->format('d/m/Y') }}
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <x-alert-success>{{ session('success') }}</x-alert-success>
        @endif

        @if(session('nueva_password'))
            <div class="rounded-2xl border border-blue-900/70 bg-blue-950/40 p-4 shadow-lg">
                <p class="mb-2 text-sm font-black text-blue-300">
                    🔑 Nueva contraseña generada
                </p>

                <p class="text-sm text-zinc-300">
                    Guardá esta contraseña y compartila con el socio:
                </p>

                <p class="mt-3 inline-block rounded-xl border border-blue-800 bg-zinc-950 px-4 py-2 font-mono text-sm font-black text-blue-300">
                    {{ session('nueva_password') }}
                </p>
            </div>
        @endif

        <x-alert-error />

        {{-- DATOS DEL SOCIO --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl md:p-6">

            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="mb-2 inline-flex items-center rounded-full bg-orange-950 px-3 py-1 text-xs font-black text-orange-300">
                        👤 Información personal
                    </div>

                    <h2 class="text-xl font-black text-white">
                        Datos del socio
                    </h2>

                    <p class="mt-1 text-sm text-zinc-400">
                        Información principal registrada en el sistema.
                    </p>
                </div>

                <a
                    href="{{ route('clientes.pagos', $cliente->id) }}"
                    style="cursor: pointer !important;"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200 shadow-sm transition duration-150 hover:-translate-y-0.5 hover:border-orange-800 hover:bg-orange-950 hover:text-orange-300 hover:shadow-lg active:scale-[0.97]"
                >
                    💰 Ver historial de pagos
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Nombre</p>
                    <p class="text-base font-black text-white">{{ $cliente->nombre }}</p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">DNI</p>
                    <p class="text-base font-bold text-zinc-200">{{ $cliente->dni ?: 'Sin cargar' }}</p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Fecha de nacimiento</p>
                    <p class="text-base font-bold text-zinc-200">
                        @if($cliente->fecha_nacimiento)
                            {{ \Carbon\Carbon::parse($cliente->fecha_nacimiento)->format('d/m/Y') }}
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Teléfono</p>
                    <p class="text-base font-bold text-zinc-200">{{ $cliente->telefono ?: 'Sin cargar' }}</p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Peso</p>
                    <p class="text-base font-bold text-zinc-200">
                        @if($cliente->peso)
                            {{ number_format($cliente->peso, 2, ',', '.') }} kg
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Altura</p>
                    <p class="text-base font-bold text-zinc-200">
                        @if($cliente->altura)
                            {{ $cliente->altura }} cm
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700 md:col-span-2 xl:col-span-1">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Email</p>
                    <p class="break-all text-base font-bold text-zinc-200">
                        {{ $cliente->user?->email ?? $cliente->email }}
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Dirección</p>
                    <p class="text-base font-bold text-zinc-200">{{ $cliente->direccion ?: 'Sin cargar' }}</p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Contacto de emergencia</p>
                    <p class="text-base font-bold text-zinc-200">{{ $cliente->contacto_emergencia ?: 'Sin cargar' }}</p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700">
                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Monto de la cuota mensual</p>
                    <p class="text-base font-black text-orange-300">
                        @if(! is_null($cliente->monto_cuota))
                            $ {{ number_format($cliente->monto_cuota, 0, ',', '.') }}
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:border-zinc-700 md:col-span-2">
                    <p class="mb-2 text-[10px] font-black uppercase tracking-wider text-zinc-500">Vencimiento de cuota</p>

                    @if($vencimientoCuota)
                        <div class="flex flex-wrap items-center gap-3">
                            <p class="text-base font-black text-white">
                                📅 {{ $vencimientoCuota->format('d/m/Y') }}
                            </p>

                            @if($vencimientoCuota->lt($hoy))
                                <span class="rounded-full bg-red-950 px-2.5 py-1 text-[10px] font-black text-red-300">🔴 Vencida</span>
                            @elseif($vencimientoCuota->lte($hoy->copy()->addDays(5)))
                                <span class="rounded-full bg-yellow-950 px-2.5 py-1 text-[10px] font-black text-yellow-300">🟡 Próxima a vencer</span>
                            @else
                                <span class="rounded-full bg-green-950 px-2.5 py-1 text-[10px] font-black text-green-300">🟢 Al día</span>
                            @endif
                        </div>
                    @else
                        <p class="text-base font-bold text-zinc-400">Sin vencimiento cargado</p>
                    @endif
                </div>

            </div>
        </div>

        {{-- ACCESO DEL SOCIO --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl md:p-6">

            <div class="mb-5">
                <div class="mb-2 inline-flex items-center rounded-full bg-purple-950 px-3 py-1 text-xs font-black text-purple-300">
                    🔐 Seguridad
                </div>

                <h2 class="text-xl font-black text-white">
                    Acceso del socio
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Creá o administrá el acceso del socio a su panel.
                </p>
            </div>

            @if($cliente->user)

                <div class="rounded-2xl border border-emerald-900/60 bg-emerald-950/20 p-4 md:p-5">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                        <div class="min-w-0 flex-1">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-emerald-950 px-3 py-1 text-xs font-black text-emerald-300">
                                    ✅ Acceso creado
                                </span>
                            </div>

                            <p class="text-sm text-zinc-300">
                                Este socio ya tiene un usuario vinculado.
                            </p>

                            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div class="rounded-xl border border-zinc-800 bg-zinc-950 px-4 py-3">
                                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Usuario</p>
                                    <p class="text-sm font-bold text-white">{{ $cliente->user->name }}</p>
                                </div>

                                <div class="rounded-xl border border-zinc-800 bg-zinc-950 px-4 py-3">
                                    <p class="mb-1 text-[10px] font-black uppercase tracking-wider text-zinc-500">Email de acceso</p>
                                    <p class="break-all text-sm font-bold text-white">{{ $cliente->user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-2 lg:w-auto">
                            <form method="POST" action="{{ route('clientes.resetPassword', $cliente->id) }}">
                                @csrf

                                <button
                                    type="submit"
                                    style="cursor: pointer !important;"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.97]"
                                >
                                    🔑 Restablecer contraseña
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('clientes.quitarAcceso', $cliente->id) }}"
                                onsubmit="return confirm('¿Seguro que querés quitar el acceso de este socio?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    style="cursor: pointer !important;"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-900/70 bg-red-950/40 px-4 py-2.5 text-sm font-bold text-red-300 transition duration-150 hover:-translate-y-0.5 hover:bg-red-950 hover:text-red-200 active:scale-[0.97]"
                                >
                                    🗑 Quitar acceso
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            @else

                <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 md:p-5">

                    <div class="mb-4">
                        <div class="mb-2 inline-flex items-center rounded-full bg-zinc-800 px-3 py-1 text-xs font-black text-zinc-300">
                            ⏳ Acceso pendiente
                        </div>

                        <label class="block text-sm font-black text-white">
                            Crear acceso nuevo para este socio
                        </label>

                        <p class="mt-2 text-sm text-zinc-500">
                            El acceso se creará utilizando automáticamente el email registrado del socio. Solo debés definir la contraseña inicial.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('clientes.crearAcceso', $cliente->id) }}" class="space-y-4" autocomplete="off">
                        @csrf

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <input
                                type="password"
                                name="password_acceso"
                                placeholder="Contraseña"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-white outline-none placeholder:text-zinc-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                            >

                            <input
                                type="password"
                                name="password_acceso_confirmation"
                                placeholder="Confirmar contraseña"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm text-white outline-none placeholder:text-zinc-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                            >
                        </div>

                        <button
                            type="submit"
                            style="cursor: pointer !important;"
                            class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.97]"
                        >
                            ✔ Crear acceso socio
                        </button>
                    </form>

                </div>

            @endif
        </div>

        {{-- MENSAJES --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl md:p-6">
            <div class="mb-5">
                <div class="mb-2 inline-flex items-center rounded-full bg-blue-950 px-3 py-1 text-xs font-black text-blue-300">
                    💬 Comunicación
                </div>

                <h2 class="text-xl font-black text-white">
                    Mensajes con el socio
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Comunicación interna entre el gimnasio y el socio.
                </p>
            </div>

            <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-3 md:p-4">
                <livewire:clientes.mensajes-cliente :cliente="$cliente" />
            </div>
        </div>

        {{-- NOTAS INTERNAS --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl md:p-6">
            <div class="mb-5">
                <div class="mb-2 inline-flex items-center rounded-full bg-yellow-950 px-3 py-1 text-xs font-black text-yellow-300">
                    📝 Uso interno
                </div>

                <h2 class="text-xl font-black text-white">
                    Notas internas
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Observaciones privadas del gimnasio sobre este socio.
                </p>
            </div>

            <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-3 md:p-4">
                @livewire('actions.gestion-notas', ['cliente' => $cliente])
            </div>
        </div>

        {{-- BOTONES FINALES --}}
        <div class="flex flex-col gap-3 border-t border-zinc-800 pt-6 sm:flex-row sm:flex-wrap">

            <a
                href="{{ route('clientes.index') }}"
                style="cursor: pointer !important;"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200 shadow-sm transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-700 hover:shadow-lg active:scale-[0.97]"
            >
                ← Volver al listado
            </a>

            <a
                href="{{ route('clientes.edit', $cliente->id) }}"
                style="cursor: pointer !important;"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.97]"
            >
                ✏️ Editar socio
            </a>

            <a
                href="{{ route('clientes.pagos', $cliente->id) }}"
                style="cursor: pointer !important;"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200 shadow-sm transition duration-150 hover:-translate-y-0.5 hover:border-emerald-900 hover:bg-emerald-950 hover:text-emerald-300 hover:shadow-lg active:scale-[0.97]"
            >
                💰 Historial de pagos
            </a>

            <a
                href="{{ route('clientes.qr', $cliente->id) }}"
                style="cursor: pointer !important;"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200 shadow-sm transition duration-150 hover:-translate-y-0.5 hover:border-purple-900 hover:bg-purple-950 hover:text-purple-300 hover:shadow-lg active:scale-[0.97]"
            >
                📱 Ver QR
            </a>

        </div>

    </div>

</x-layouts::app>
