<div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-4 shadow-xl md:p-6">

    {{-- BUSCADOR --}}
    <div class="mb-6 flex flex-col gap-3 md:mb-8 md:flex-row md:items-center">

        <div
            class="flex min-w-0 flex-1 items-center rounded-2xl border border-zinc-700 bg-zinc-950 p-1 shadow-sm
                   transition duration-200 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/30"
        >
            <input
                wire:model.live.debounce.300ms="busqueda"
                type="text"
                placeholder="Buscar socio por nombre, email o teléfono..."
                class="min-w-0 flex-1 border-none bg-transparent py-2 pl-3 text-sm text-white outline-none placeholder:text-zinc-500 focus:ring-0"
            >

            <div
                class="ml-1 flex shrink-0 items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 px-3 py-2 text-zinc-400"
            >
                🔍
            </div>
        </div>


        <button
            type="button"
            wire:click="$set('busqueda', '')"
            style="cursor: pointer !important;"
            class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-zinc-700
                   bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200 shadow-sm
                   transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-700 hover:shadow-lg
                   active:scale-[0.97]"
        >
            🧹 Limpiar
        </button>

    </div>


    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))

        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 flex items-center gap-2 rounded-2xl border border-green-800 bg-green-950/40 p-4 text-sm font-bold text-green-300 shadow-lg transition-all"
        >
            ✅ {{ session('success') }}
        </div>

    @endif


    {{-- MENSAJE DE ERROR --}}
    @if(session('error'))

        <div
            class="mb-4 flex items-center gap-2 rounded-2xl border border-red-800 bg-red-950/40 p-4 text-sm font-bold text-red-300 shadow-lg"
        >
            ❌ {{ session('error') }}
        </div>

    @endif


    {{-- TABLA DE SOCIOS --}}
    @if($clientes->isEmpty())

        <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-950 py-12 text-center">

            <div class="text-4xl">
                👥
            </div>

            <p class="mt-3 italic text-zinc-500">
                No se encontraron socios activos.
            </p>

        </div>

    @else

        <div class="overflow-x-auto rounded-2xl border border-zinc-800">

            <table class="w-full border-collapse">

                <thead class="bg-zinc-950">
                    <tr class="border-b border-zinc-800 text-[11px] font-black uppercase tracking-wider text-zinc-500">
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Teléfono</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-800">

                    @foreach($clientes as $cliente)

                        <tr
                            wire:key="cliente-{{ $cliente->id }}"
                            class="transition duration-150 hover:bg-zinc-800/70"
                        >

                            {{-- ID --}}
                            <td class="p-3 font-mono text-sm text-zinc-500">
                                #{{ $cliente->id }}
                            </td>


                            {{-- NOMBRE Y BADGES --}}
                            <td class="whitespace-nowrap p-3 text-sm font-bold text-white">

                                <div class="flex flex-col gap-1.5">

                                    <span class="text-sm font-black text-white">
                                        {{ $cliente->nombre }}
                                    </span>


                                    {{-- ESTADO CUOTA --}}
                                    @if($cliente->fecha_vencimiento_cuota)

                                        @php
                                            $hoy = \Carbon\Carbon::today();

                                            $vencimiento = \Carbon\Carbon::parse(
                                                $cliente->fecha_vencimiento_cuota
                                            );
                                        @endphp

                                        @if($vencimiento->lt($hoy))

                                            <span class="inline-flex w-fit items-center rounded-full bg-red-950 px-2.5 py-1 text-[10px] font-bold text-red-300">
                                                🔴 Cuota vencida
                                            </span>

                                        @elseif($vencimiento->lte($hoy->copy()->addDays(5)))

                                            <span class="inline-flex w-fit items-center rounded-full bg-yellow-950 px-2.5 py-1 text-[10px] font-bold text-yellow-300">
                                                🟡 Próxima a vencer
                                            </span>

                                        @else

                                            <span class="inline-flex w-fit items-center rounded-full bg-green-950 px-2.5 py-1 text-[10px] font-bold text-green-300">
                                                🟢 Cuota al día
                                            </span>

                                        @endif

                                        <span class="text-[10px] font-medium text-zinc-500">
                                            Vence: {{ $vencimiento->format('d/m/Y') }}
                                        </span>

                                    @else

                                        <span class="inline-flex w-fit items-center rounded-full bg-zinc-800 px-2.5 py-1 text-[10px] font-bold text-zinc-400">
                                            ⚪ Sin vencimiento
                                        </span>

                                    @endif


                                    {{-- MENSAJES --}}
                                    @if($cliente->mensajes_no_leidos_count > 0)

                                        <span class="inline-flex w-fit items-center rounded-full bg-blue-950 px-2.5 py-1 text-[10px] font-bold text-blue-300">
                                            💬 Nuevo mensaje
                                        </span>

                                    @endif


                                    {{-- CUENTA ACTIVADA --}}
                                    @if($cliente->user_id)

                                        <span class="inline-flex w-fit items-center rounded-full bg-emerald-950 px-2.5 py-1 text-[10px] font-bold text-emerald-300">
                                            ✅ Cuenta activada
                                        </span>

                                    @else

                                        <span class="inline-flex w-fit items-center rounded-full bg-zinc-800 px-2.5 py-1 text-[10px] font-bold text-zinc-400">
                                            ⏳ Sin activar
                                        </span>

                                    @endif

                                </div>

                            </td>


                            {{-- TELÉFONO --}}
                            <td class="p-3 text-sm font-medium text-zinc-400">
                                {{ $cliente->telefono }}
                            </td>


                            {{-- EMAIL --}}
                            <td class="p-3 text-sm text-zinc-400">
                                {{ $cliente->email }}
                            </td>


                            {{-- ACCIONES --}}
                            <td class="p-3 text-right">

                                <div class="flex flex-wrap items-center justify-end gap-1.5">

                                    <a
                                        href="{{ route('clientes.show', $cliente->id) }}"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-blue-950 hover:text-blue-300
                                               active:scale-[0.92]"
                                        title="Ver detalle"
                                    >
                                        👁️
                                    </a>

                                    <a
                                        href="{{ route('clientes.edit', $cliente->id) }}"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-yellow-950 hover:text-yellow-300
                                               active:scale-[0.92]"
                                        title="Editar socio"
                                    >
                                        ✏️
                                    </a>


                                    {{-- RENOVAR Y REGISTRAR PAGO --}}
                                    <button
                                        type="button"
                                        wire:click="abrirRenovacion({{ $cliente->id }})"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-green-950 hover:text-green-300
                                               active:scale-[0.92]"
                                        title="Registrar pago y renovar cuota"
                                    >
                                        💳
                                    </button>


                                    {{-- EDITAR VENCIMIENTO MANUALMENTE --}}
                                    <button
                                        type="button"
                                        wire:click="abrirEdicionVencimiento({{ $cliente->id }})"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-purple-950 hover:text-purple-300
                                               active:scale-[0.92]"
                                        title="Editar fecha de vencimiento"
                                    >
                                        📅
                                    </button>


                                    <a
                                        href="{{ route('clientes.pagos', $cliente->id) }}"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-emerald-950 hover:text-emerald-300
                                               active:scale-[0.92]"
                                        title="Historial de pagos"
                                    >
                                        💰
                                    </a>


                                    <a
                                        href="{{ route('clientes.rutina', $cliente->id) }}"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-green-950 hover:text-green-300
                                               active:scale-[0.92]"
                                        title="Rutina del socio"
                                    >
                                        💪
                                    </a>


                                    <a
                                        href="{{ route('clientes.entrenamientos', $cliente->id) }}"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-indigo-950 hover:text-indigo-300
                                               active:scale-[0.92]"
                                        title="Historial de entrenamientos"
                                    >
                                        🏋️
                                    </a>


                                    <button
                                        type="button"
                                        wire:click="archivar({{ $cliente->id }})"
                                        wire:confirm="¿Quieres dar de baja a este socio?"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-amber-950 hover:text-amber-300
                                               active:scale-[0.92]"
                                        title="Dar de baja socio"
                                    >
                                        📦
                                    </button>


                                    <button
                                        type="button"
                                        wire:click="delete({{ $cliente->id }})"
                                        wire:confirm="¿Estás seguro de que deseas eliminar permanentemente a este socio?"
                                        style="cursor: pointer !important;"
                                        class="rounded-xl bg-zinc-800 p-2 text-zinc-300 shadow-sm transition duration-150
                                               hover:-translate-y-0.5 hover:bg-red-950 hover:text-red-300
                                               active:scale-[0.92]"
                                        title="Eliminar permanentemente"
                                    >
                                        🗑️
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- PAGINACIÓN --}}
        <div class="mt-6">
            {{ $clientes->links() }}
        </div>

    @endif


    {{-- FORMULARIO DE RENOVACIÓN --}}
    @if($clientePagoId)

        @php
            $nuevoVencimientoCalculado = $fechaBasePago
                ? \Carbon\Carbon::parse($fechaBasePago)->addDays(30)
                : null;
        @endphp

        <div class="mt-6 rounded-3xl border border-orange-900/60 bg-zinc-950 p-4 shadow-xl md:p-6">

            <div class="mb-5">

                <div class="mb-2 inline-flex items-center rounded-full bg-orange-950 px-3 py-1 text-xs font-black text-orange-300">
                    💳 Renovación
                </div>

                <h2 class="text-xl font-black text-white">
                    Registrar pago y renovar cuota
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Elegí la fecha desde la cual se calcularán los próximos 30 días.
                </p>

            </div>


            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                {{-- MONTO --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-zinc-300">
                        Monto
                    </label>

                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        wire:model="montoPago"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-white outline-none
                               placeholder:text-zinc-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                        placeholder="Ej: 25000"
                    >

                    @error('montoPago')
                        <p class="mt-1 text-xs font-bold text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- MÉTODO --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-zinc-300">
                        Método de pago
                    </label>

                    <select
                        wire:model="metodoPago"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-white outline-none
                               focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                    >
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Débito">Débito</option>
                        <option value="Crédito">Crédito</option>
                        <option value="Mercado Pago">Mercado Pago</option>
                    </select>

                    @error('metodoPago')
                        <p class="mt-1 text-xs font-bold text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- FECHA BASE --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-zinc-300">
                        Fecha base
                    </label>

                    <input
                        type="date"
                        wire:model.live="fechaBasePago"
                        style="cursor: pointer !important;"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-white outline-none
                               focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                    >

                    @error('fechaBasePago')
                        <p class="mt-1 text-xs font-bold text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- NUEVO VENCIMIENTO --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-zinc-300">
                        Nuevo vencimiento
                    </label>

                    <div class="flex min-h-[44px] items-center rounded-xl border border-green-900 bg-green-950/40 px-4 py-2 font-black text-green-300">

                        @if($nuevoVencimientoCalculado)

                            📅 {{ $nuevoVencimientoCalculado->format('d/m/Y') }}

                        @else

                            Fecha pendiente

                        @endif

                    </div>

                </div>

            </div>


            {{-- OBSERVACIÓN --}}
            <div class="mt-4">

                <label class="mb-1 block text-sm font-bold text-zinc-300">
                    Observación
                </label>

                <input
                    type="text"
                    wire:model="observacionPago"
                    class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-white outline-none
                           placeholder:text-zinc-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
                    placeholder="Ej: Pago realizado con 5 días de atraso"
                >

                @error('observacionPago')
                    <p class="mt-1 text-xs font-bold text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- RESUMEN --}}
            @if($fechaBasePago && $nuevoVencimientoCalculado)

                <div class="mt-4 rounded-2xl border border-blue-900 bg-blue-950/30 p-4 text-sm text-blue-300">

                    <span class="font-black">
                        Resumen:
                    </span>

                    la cuota se renovará desde el

                    <span class="font-black">
                        {{ \Carbon\Carbon::parse($fechaBasePago)->format('d/m/Y') }}
                    </span>

                    hasta el

                    <span class="font-black">
                        {{ $nuevoVencimientoCalculado->format('d/m/Y') }}.
                    </span>

                </div>

            @endif


            {{-- BOTONES --}}
            <div class="mt-5 flex flex-wrap gap-2">

                <button
                    type="button"
                    wire:click="renovarCuota"
                    wire:loading.attr="disabled"
                    wire:target="renovarCuota"
                    style="cursor: pointer !important;"
                    class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white
                           shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl
                           active:scale-[0.97] disabled:cursor-wait disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="renovarCuota">
                        ✅ Confirmar pago y renovación
                    </span>

                    <span wire:loading wire:target="renovarCuota">
                        ⏳ Procesando...
                    </span>
                </button>


                <button
                    type="button"
                    wire:click="guardarMontoCuota"
                    wire:loading.attr="disabled"
                    wire:target="guardarMontoCuota"
                    style="cursor: pointer !important;"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white
                           shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-blue-500 hover:shadow-xl
                           active:scale-[0.97] disabled:cursor-wait disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="guardarMontoCuota">
                        💾 Guardar monto
                    </span>

                    <span wire:loading wire:target="guardarMontoCuota">
                        ⏳ Guardando...
                    </span>
                </button>


                <button
                    type="button"
                    wire:click="cancelarRenovacion"
                    style="cursor: pointer !important;"
                    class="inline-flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200
                           transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-700 active:scale-[0.97]"
                >
                    ❌ Cancelar
                </button>

            </div>

        </div>

    @endif


    {{-- FORMULARIO DE EDICIÓN MANUAL DEL VENCIMIENTO --}}
    @if($clienteVencimientoId)

        <div class="mt-6 rounded-3xl border border-purple-900/60 bg-zinc-950 p-4 shadow-xl md:p-6">

            <div class="mb-5">

                <div class="mb-2 inline-flex items-center rounded-full bg-purple-950 px-3 py-1 text-xs font-black text-purple-300">
                    📅 Vencimiento
                </div>

                <h2 class="text-xl font-black text-white">
                    Editar fecha de vencimiento
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Esta acción modifica solamente el vencimiento de la cuota y no registra un pago.
                </p>

            </div>


            <div class="max-w-md">

                <label class="mb-1 block text-sm font-bold text-zinc-300">
                    Nueva fecha de vencimiento
                </label>

                <input
                    type="date"
                    wire:model="fechaVencimientoManual"
                    style="cursor: pointer !important;"
                    class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-white outline-none
                           focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30"
                >

                @error('fechaVencimientoManual')
                    <p class="mt-1 text-xs font-bold text-red-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            <div class="mt-4 rounded-2xl border border-yellow-900 bg-yellow-950/30 p-4 text-sm text-yellow-300">

                ⚠️ Esta modificación no aparecerá como un pago nuevo en el historial.
                Utilizala para correcciones, días de cortesía o acuerdos especiales.

            </div>


            <div class="mt-5 flex flex-wrap gap-2">

                <button
                    type="button"
                    wire:click="guardarVencimientoManual"
                    wire:confirm="¿Confirmás la nueva fecha de vencimiento?"
                    wire:loading.attr="disabled"
                    wire:target="guardarVencimientoManual"
                    style="cursor: pointer !important;"
                    class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-bold text-white
                           shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-purple-500 hover:shadow-xl
                           active:scale-[0.97] disabled:cursor-wait disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="guardarVencimientoManual">
                        ✅ Guardar vencimiento
                    </span>

                    <span wire:loading wire:target="guardarVencimientoManual">
                        ⏳ Guardando...
                    </span>
                </button>


                <button
                    type="button"
                    wire:click="cancelarEdicionVencimiento"
                    style="cursor: pointer !important;"
                    class="inline-flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-bold text-zinc-200
                           transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-700 active:scale-[0.97]"
                >
                    ❌ Cancelar
                </button>

            </div>

        </div>

    @endif


    {{-- CURSOR PAGINACIÓN --}}
    <style>
        nav[role="navigation"] a,
        nav[role="navigation"] button {
            cursor: pointer !important;
        }
    </style>

</div>