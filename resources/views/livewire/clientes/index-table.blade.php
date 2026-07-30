<div class="rounded-xl border border-stone-300 bg-stone-200 p-4 font-sans shadow-sm md:p-6">

    {{-- BUSCADOR --}}
    <div class="mb-6 flex items-center gap-2 text-left md:mb-8">

        <div
            class="flex min-w-0 flex-1 items-center rounded-xl border border-stone-300 bg-stone-100 p-1 shadow-sm transition focus-within:ring-2 focus-within:ring-blue-500"
        >
            <input
                wire:model.live.debounce.300ms="busqueda"
                type="text"
                placeholder="Buscar socio por nombre, email o teléfono..."
                class="min-w-0 flex-1 border-none bg-transparent py-1.5 pl-3 text-sm text-neutral-800 outline-none focus:ring-0"
            >

            <div
                class="ml-1 flex shrink-0 items-center justify-center rounded-lg border border-stone-300 bg-stone-100 px-3 py-1.5 text-neutral-500"
            >
                🔍
            </div>
        </div>

        <button
            type="button"
            wire:click="$set('busqueda', '')"
            class="inline-flex shrink-0 cursor-pointer items-center gap-2 whitespace-nowrap rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-sm font-medium text-neutral-600 shadow-sm transition hover:bg-stone-300"
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
            class="mb-4 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-bold text-green-700 transition-all"
        >
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- MENSAJE DE ERROR --}}
    @if(session('error'))
        <div
            class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700"
        >
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- TABLA DE SOCIOS --}}
    @if($clientes->isEmpty())

        <div class="py-10 text-center">
            <p class="italic text-neutral-500">
                No se encontraron socios activos.
            </p>
        </div>

    @else

        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                <thead>
                    <tr
                        class="border-b border-stone-300 text-[11px] font-bold uppercase tracking-wider text-neutral-500"
                    >
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Teléfono</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-300">

                    @foreach($clientes as $cliente)

                        <tr
                            wire:key="cliente-{{ $cliente->id }}"
                            class="transition hover:bg-stone-100"
                        >

                            {{-- ID --}}
                            <td class="p-3 font-mono text-sm text-neutral-500">
                                #{{ $cliente->id }}
                            </td>

                            {{-- NOMBRE Y BADGES --}}
                            <td class="whitespace-nowrap p-3 text-sm font-bold text-neutral-800">

                                <div class="flex flex-col gap-1">

                                    <span>
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

                                            <span
                                                class="inline-flex w-fit items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700"
                                            >
                                                🔴 Cuota vencida
                                            </span>

                                        @elseif($vencimiento->lte($hoy->copy()->addDays(5)))

                                            <span
                                                class="inline-flex w-fit items-center rounded-full bg-yellow-100 px-2 py-0.5 text-[10px] font-bold text-yellow-700"
                                            >
                                                🟡 Próxima a vencer
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex w-fit items-center rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-700"
                                            >
                                                🟢 Cuota al día
                                            </span>

                                        @endif

                                        <span class="text-[10px] font-medium text-neutral-500">
                                            Vence:
                                            {{ $vencimiento->format('d/m/Y') }}
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex w-fit items-center rounded-full bg-stone-300 px-2 py-0.5 text-[10px] font-bold text-stone-700"
                                        >
                                            ⚪ Sin vencimiento
                                        </span>

                                    @endif

                                    {{-- MENSAJES --}}
                                    @if($cliente->mensajes_no_leidos_count > 0)

                                        <span
                                            class="inline-flex w-fit items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700"
                                        >
                                            💬 Nuevo mensaje
                                        </span>

                                    @endif

                                    {{-- CUENTA ACTIVADA --}}
                                    @if($cliente->user_id)

                                        <span
                                            class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700"
                                        >
                                            ✅ Cuenta activada
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex w-fit items-center rounded-full bg-stone-300 px-2 py-0.5 text-[10px] font-bold text-stone-700"
                                        >
                                            ⏳ Sin activar
                                        </span>

                                    @endif

                                </div>

                            </td>

                            {{-- TELÉFONO --}}
                            <td class="p-3 text-sm font-medium text-neutral-600">
                                {{ $cliente->telefono }}
                            </td>

                            {{-- EMAIL --}}
                            <td class="p-3 text-sm text-neutral-600">
                                {{ $cliente->email }}
                            </td>

                            {{-- ACCIONES --}}
                            <td class="p-3 text-right">

                                <div class="flex items-center justify-end gap-1">

                                    <a
                                        href="{{ route('clientes.show', $cliente->id) }}"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-blue-50 hover:text-blue-600"
                                        title="Ver detalle"
                                    >
                                        👁️
                                    </a>

                                    <a
                                        href="{{ route('clientes.edit', $cliente->id) }}"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-yellow-50 hover:text-yellow-600"
                                        title="Editar socio"
                                    >
                                        ✏️
                                    </a>

                                    {{-- RENOVAR Y REGISTRAR PAGO --}}
                                    <button
                                        type="button"
                                        wire:click="abrirRenovacion({{ $cliente->id }})"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-green-50 hover:text-green-600"
                                        title="Registrar pago y renovar cuota"
                                    >
                                        💳
                                    </button>

                                    {{-- EDITAR VENCIMIENTO MANUALMENTE --}}
                                    <button
                                        type="button"
                                        wire:click="abrirEdicionVencimiento({{ $cliente->id }})"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-purple-50 hover:text-purple-600"
                                        title="Editar fecha de vencimiento"
                                    >
                                        📅
                                    </button>

                                    <a
                                        href="{{ route('clientes.pagos', $cliente->id) }}"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-emerald-50 hover:text-emerald-600"
                                        title="Historial de pagos"
                                    >
                                        💰
                                    </a>

                                    <a
                                        href="{{ route('clientes.rutina', $cliente->id) }}"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-green-50 hover:text-green-600"
                                        title="Rutina del socio"
                                    >
                                        💪
                                    </a>
                                        
                                    <a
                                        href="{{ route('clientes.entrenamientos', $cliente->id) }}"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                        title="Historial de entrenamientos"
                                    >
                                        🏋️
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="archivar({{ $cliente->id }})"
                                        wire:confirm="¿Quieres dar de baja a este socio?"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-amber-50 hover:text-amber-600"
                                        title="Dar de baja socio"
                                    >
                                        📦
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $cliente->id }})"
                                        wire:confirm="¿Estás seguro de que deseas eliminar permanentemente a este socio?"
                                        class="cursor-pointer rounded-lg p-2 text-neutral-500 transition hover:bg-red-50 hover:text-red-600"
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

        <div
            class="mt-6 rounded-xl border border-orange-300 bg-orange-50 p-4 shadow-sm md:p-6"
        >

            <div class="mb-5">

                <h2 class="text-lg font-bold text-orange-700">
                    💳 Registrar pago y renovar cuota
                </h2>

                <p class="mt-1 text-sm text-neutral-600">
                    Elegí la fecha desde la cual se calcularán los próximos 30 días.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                {{-- MONTO --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-neutral-700">
                        Monto
                    </label>

                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        wire:model="montoPago"
                        class="w-full rounded-xl border border-stone-300 bg-white px-4 py-2 text-neutral-900 placeholder-neutral-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                        placeholder="Ej: 25000"
                    >

                    @error('montoPago')
                        <p class="mt-1 text-xs font-bold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- MÉTODO --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-neutral-700">
                        Método de pago
                    </label>

                    <select
                        wire:model="metodoPago"
                        class="w-full rounded-xl border border-stone-300 bg-white px-4 py-2 text-neutral-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                    >
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Débito">Débito</option>
                        <option value="Crédito">Crédito</option>
                        <option value="Mercado Pago">Mercado Pago</option>
                    </select>

                    @error('metodoPago')
                        <p class="mt-1 text-xs font-bold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- FECHA BASE --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-neutral-700">
                        Fecha base
                    </label>

                    <input
                        type="date"
                        wire:model.live="fechaBasePago"
                        class="w-full cursor-pointer rounded-xl border border-stone-300 bg-white px-4 py-2 text-neutral-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                    >

                    @error('fechaBasePago')
                        <p class="mt-1 text-xs font-bold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- NUEVO VENCIMIENTO --}}
                <div>

                    <label class="mb-1 block text-sm font-bold text-neutral-700">
                        Nuevo vencimiento
                    </label>

                    <div
                        class="flex min-h-[42px] items-center rounded-xl border border-green-300 bg-green-100 px-4 py-2 font-black text-green-800"
                    >
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

                <label class="mb-1 block text-sm font-bold text-neutral-700">
                    Observación
                </label>

                <input
                    type="text"
                    wire:model="observacionPago"
                    class="w-full rounded-xl border border-stone-300 bg-white px-4 py-2 text-neutral-900 placeholder-neutral-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-500"
                    placeholder="Ej: Pago realizado con 5 días de atraso"
                >

                @error('observacionPago')
                    <p class="mt-1 text-xs font-bold text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- RESUMEN --}}
            @if($fechaBasePago && $nuevoVencimientoCalculado)

                <div
                    class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800"
                >
                    <span class="font-black">Resumen:</span>

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
        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-orange-600 disabled:cursor-wait disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="renovarCuota">
            ✅ Confirmar pago y renovación
        </span>

        <span wire:loading wire:target="renovarCuota">
            Procesando...
        </span>
    </button>

    <button
        type="button"
        wire:click="guardarMontoCuota"
        wire:loading.attr="disabled"
        wire:target="guardarMontoCuota"
        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="guardarMontoCuota">
            💾 Guardar monto
        </span>

        <span wire:loading wire:target="guardarMontoCuota">
            Guardando...
        </span>
    </button>

    <button
        type="button"
        wire:click="cancelarRenovacion"
        class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-stone-300 bg-stone-100 px-4 py-2 text-sm font-bold text-neutral-700 transition hover:bg-stone-300"
    >
        ❌ Cancelar
    </button>

</div>

    @endif

    {{-- FORMULARIO DE EDICIÓN MANUAL DEL VENCIMIENTO --}}
    @if($clienteVencimientoId)

        <div
            class="mt-6 rounded-xl border border-purple-300 bg-purple-50 p-4 shadow-sm md:p-6"
        >

            <div class="mb-5">

                <h2 class="text-lg font-bold text-purple-700">
                    📅 Editar fecha de vencimiento
                </h2>

                <p class="mt-1 text-sm text-neutral-600">
                    Esta acción modifica solamente el vencimiento de la cuota y no registra un pago.
                </p>

            </div>

            <div class="max-w-md">

                <label class="mb-1 block text-sm font-bold text-neutral-700">
                    Nueva fecha de vencimiento
                </label>

                <input
                    type="date"
                    wire:model="fechaVencimientoManual"
                    class="w-full cursor-pointer rounded-xl border border-stone-300 bg-white px-4 py-2 text-neutral-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-500"
                >

                @error('fechaVencimientoManual')
                    <p class="mt-1 text-xs font-bold text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div
                class="mt-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800"
            >
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
                    class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-purple-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-purple-700 disabled:cursor-wait disabled:opacity-60"
                >
                    <span
                        wire:loading.remove
                        wire:target="guardarVencimientoManual"
                    >
                        ✅ Guardar vencimiento
                    </span>

                    <span
                        wire:loading
                        wire:target="guardarVencimientoManual"
                    >
                        Guardando...
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="cancelarEdicionVencimiento"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-stone-300 bg-stone-100 px-4 py-2 text-sm font-bold text-neutral-700 transition hover:bg-stone-300"
                >
                    ❌ Cancelar
                </button>

            </div>

        </div>

    @endif

</div>

<style>
    nav[role="navigation"] a,
    nav[role="navigation"] button {
        cursor: pointer !important;
    }
</style>