<x-layouts::app :title="'Ficha de ' . $cliente->nombre">

    <div class="space-y-6 pb-10 px-0 w-full">

        {{-- CABECERA --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm">

            <h1 class="text-2xl font-bold text-stone-900">
                Ficha del Socio
            </h1>

            <p class="mt-2 text-sm text-stone-600">
                Información general, acceso, pagos, mensajes y notas internas del socio.
            </p>

        </div>

        @if(session('success'))
            <x-alert-success>{{ session('success') }}</x-alert-success>
        @endif

        @if(session('nueva_password'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-bold text-blue-700 mb-2">
                    🔑 Nueva contraseña generada
                </p>

                <p class="text-sm text-stone-700">
                    Guardá esta contraseña y compartila con el socio:
                </p>

                <p class="mt-2 inline-block rounded-lg bg-white border border-blue-200 px-3 py-2 text-sm font-mono font-bold text-blue-700">
                    {{ session('nueva_password') }}
                </p>
            </div>
        @endif

        <x-alert-error />

        {{-- DATOS DEL SOCIO --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm text-left">

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">

                <div>
                    <h2 class="text-xl font-bold text-stone-900">
                        👤 Datos del socio
                    </h2>

                    <p class="mt-1 text-sm text-stone-600">
                        Información principal registrada en el sistema.
                    </p>
                </div>

                <a
                    href="{{ route('clientes.pagos', $cliente->id) }}"
                    style="background:black;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;"
                >
                    💰 Ver historial de pagos
                </a>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">Nombre</p>
                    <p class="text-lg font-semibold text-stone-900">{{ $cliente->nombre }}</p>
                </div>

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
                        DNI
                    </p>

                    <p class="text-lg text-stone-900">
                        {{ $cliente->dni ?: 'Sin cargar' }}
                    </p>
                </div>
                    <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
        Fecha de nacimiento
    </p>

    <p class="text-lg text-stone-900">
        @if($cliente->fecha_nacimiento)
            {{ \Carbon\Carbon::parse($cliente->fecha_nacimiento)->format('d/m/Y') }}
        @else
            Sin cargar
        @endif
    </p>
</div>


                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">Teléfono</p>
                    <p class="text-lg text-stone-900">{{ $cliente->telefono }}</p>
                </div>

                    <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
        Peso
    </p>

    <p class="text-lg text-stone-900">
        @if($cliente->peso)
            {{ number_format($cliente->peso, 2, ',', '.') }} kg
        @else
            Sin cargar
        @endif
    </p>
</div>

    <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
        Altura
    </p>

    <p class="text-lg text-stone-900">
        @if($cliente->altura)
            {{ $cliente->altura }} cm
        @else
            Sin cargar
        @endif
    </p>
</div>

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
                        Email
                    </p>

                    <p class="text-lg text-stone-900 break-all">
                        {{ $cliente->user?->email ?? $cliente->email }}
                    </p>
                </div>

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">Dirección</p>
                    <p class="text-lg text-stone-900">{{ $cliente->direccion ?: 'Sin cargar' }}</p>
                </div>

                    <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
        Contacto de emergencia
    </p>

    <p class="text-lg text-stone-900">
        {{ $cliente->contacto_emergencia ?: 'Sin cargar' }}
    </p>
</div>

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">
                        Monto de la cuota mensual
                    </p>

                    <p class="text-lg font-bold text-stone-900">
                        @if(! is_null($cliente->monto_cuota))
                            $ {{ number_format($cliente->monto_cuota, 0, ',', '.') }}
                        @else
                            Sin cargar
                        @endif
                    </p>
                </div>

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4 md:col-span-2">
                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">Vencimiento de cuota</p>

                    <p class="text-lg font-bold text-stone-900">
                        @if($cliente->fecha_vencimiento_cuota)
                            📅 {{ \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota)->format('d/m/Y') }}
                        @else
                            Sin vencimiento cargado
                        @endif
                    </p>
                </div>

            </div>

        </div>

        {{-- ACCESO DEL SOCIO --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm text-left">

            <h2 class="text-xl font-bold text-stone-900 mb-2">
                🔐 Acceso del socio
            </h2>

            <p class="text-sm text-stone-600 mb-5">
                Creá o administrá el acceso del socio a su panel.
            </p>

            @if($cliente->user)

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">

                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                        <div class="flex-1">

                            <p class="text-sm font-bold text-green-700 mb-1">
                                ✅ Acceso creado
                            </p>

                            <p class="text-sm text-stone-700">
                                Este socio ya tiene un usuario vinculado.
                            </p>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">

                                <div class="rounded-xl border border-stone-300 bg-stone-200 px-3 py-2">
                                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">Usuario</p>
                                    <p class="text-sm font-semibold text-stone-900">
                                        {{ $cliente->user->name }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-stone-300 bg-stone-200 px-3 py-2">
                                    <p class="text-[10px] font-bold uppercase text-stone-500 mb-1">Email de acceso</p>
                                    <p class="text-sm font-semibold text-stone-900 break-all">
                                        {{ $cliente->user->email }}
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div class="flex flex-col gap-2 w-full md:w-auto">

                            <form method="POST" action="{{ route('clientes.resetPassword', $cliente->id) }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm text-white font-bold hover:bg-orange-600 transition cursor-pointer active:scale-[0.98] w-full"
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
                                    style="background:black;color:white;border-radius:14px;padding:8px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;"
                                >
                                    🗑 Quitar acceso
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @else

                <div class="rounded-xl border border-stone-300 bg-stone-100 p-4">

                    <div class="mb-4">

                        <label class="block text-sm font-semibold text-stone-700 mb-2">
                            Crear acceso nuevo para este socio
                        </label>

                        <p class="text-sm text-stone-500">
                            El acceso se creará utilizando automáticamente el email registrado del socio.
Solo debés definir la contraseña inicial.
                        </p>

                    </div>

                    <form method="POST" action="{{ route('clientes.crearAcceso', $cliente->id) }}" class="space-y-3" autocomplete="off">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                            <input
                                type="password"
                                name="password_acceso"
                                placeholder="Contraseña"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-stone-300 bg-stone-200 px-3 py-2 text-sm text-stone-900 outline-none focus:ring-2 focus:ring-orange-500"
                            >

                            <input
                                type="password"
                                name="password_acceso_confirmation"
                                placeholder="Confirmar contraseña"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-stone-300 bg-stone-200 px-3 py-2 text-sm text-stone-900 outline-none focus:ring-2 focus:ring-orange-500"
                            >

                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm text-white font-bold hover:bg-orange-600 transition cursor-pointer active:scale-[0.98]"
                        >
                            ✔ Crear acceso socio
                        </button>

                    </form>

                </div>

            @endif

        </div>

        {{-- MENSAJES --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm text-left">

            <div class="mb-4">

                <h2 class="text-xl font-bold text-stone-900 flex items-center gap-2">
                    💬 Mensajes con el socio
                </h2>

                <p class="mt-1 text-sm text-stone-600">
                    Comunicación interna entre el gimnasio y el socio.
                </p>

            </div>

            <livewire:clientes.mensajes-cliente :cliente="$cliente" />

        </div>

        {{-- NOTAS INTERNAS --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm text-left">

            <div class="mb-4">

                <h2 class="text-xl font-bold text-stone-900 flex items-center gap-2">
                    📝 Notas internas
                </h2>

                <p class="mt-1 text-sm text-stone-600">
                    Observaciones privadas del gimnasio sobre este socio.
                </p>

            </div>

            @livewire('actions.gestion-notas', ['cliente' => $cliente])

        </div>

        {{-- BOTONES FINALES --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-stone-300">

            <a
                href="{{ route('clientes.index') }}"
                style="background:black;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;"
            >
                ← Volver al listado
            </a>

            <a
                href="{{ route('clientes.edit', $cliente->id) }}"
                class="inline-flex items-center justify-center whitespace-nowrap bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition shadow-sm cursor-pointer active:scale-95"
            >
                ✏️ Editar socio
            </a>

            <a
                href="{{ route('clientes.pagos', $cliente->id) }}"
                style="background:black;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;"
            >
                💰 Historial de pagos
            </a>
            <a
                href="{{ route('clientes.qr', $cliente->id) }}"
                class="inline-flex items-center justify-center whitespace-nowrap bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition shadow-sm cursor-pointer active:scale-95"
            >
                📱 Ver QR
            </a>

        </div>

    </div>

</x-layouts::app>