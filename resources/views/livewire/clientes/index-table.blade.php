<div class="rounded-xl border border-stone-300 p-4 md:p-6 bg-stone-200 shadow-sm font-sans">
    
    {{-- BUSCADOR CON LUPA REDONDEADA INTERNA --}}
    <div class="mb-6 md:mb-8 flex items-center gap-2 text-left">
        
        {{-- Contenedor principal del buscador --}}
        <div class="flex-1 min-w-0 flex items-center bg-stone-100 border border-stone-300 rounded-xl p-1 focus-within:ring-2 focus-within:ring-blue-500 transition shadow-sm">
            
            {{-- INPUT --}}
            <input 
                wire:model.live.debounce.300ms="busqueda" 
                type="text" 
                placeholder="Buscar socio por nombre, email o teléfono..."
                class="flex-1 min-w-0 bg-transparent pl-3 py-1.5 border-none focus:ring-0 outline-none text-sm text-neutral-800"
            >

            {{-- LUPITA --}}
            <div class="flex items-center justify-center bg-stone-100 text-neutral-500 rounded-lg px-3 py-1.5 ml-1 border border-stone-300 shrink-0">
                🔍
            </div>
        </div>

        {{-- BOTÓN LIMPIAR --}}
        <button type="button"
                wire:click="$set('busqueda', '')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-stone-100 px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-stone-300 transition cursor-pointer shadow-sm whitespace-nowrap border border-stone-300">
            🧹 Limpiar
        </button>
    </div>

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)" 
             class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-bold flex items-center gap-2 transition-all">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- TABLA DE SOCIOS --}}
    @if($clientes->isEmpty())

        <div class="text-center py-10">
            <p class="text-neutral-500 italic">
                No se encontraron socios activos.
            </p>
        </div>

    @else

        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                <thead>
                    <tr class="border-b border-stone-300 text-neutral-500 text-[11px] uppercase tracking-wider font-bold">
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Teléfono</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-stone-300">

                    @foreach($clientes as $cliente)

                        <tr wire:key="cliente-{{ $cliente->id }}" class="hover:bg-stone-100 transition">

                            {{-- ID --}}
                            <td class="p-3 text-sm text-neutral-500 font-mono">
                                #{{ $cliente->id }}
                            </td>

                            {{-- NOMBRE + BADGES --}}
<td class="p-3 text-sm font-bold text-neutral-800 whitespace-nowrap">
    
    <div class="flex flex-col gap-1">

        <span>
            {{ $cliente->nombre }}
        </span>

        {{-- ESTADO CUOTA --}}
        @if($cliente->fecha_vencimiento_cuota)

            @php
                $hoy = \Carbon\Carbon::today();
                $vencimiento = \Carbon\Carbon::parse($cliente->fecha_vencimiento_cuota);
            @endphp

            @if($vencimiento->lt($hoy))

                <span class="inline-flex w-fit items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">
                    🔴 Cuota vencida
                </span>

            @elseif($vencimiento->lte($hoy->copy()->addDays(5)))

                <span class="inline-flex w-fit items-center rounded-full bg-yellow-100 px-2 py-0.5 text-[10px] font-bold text-yellow-700">
                    🟡 Próxima a vencer
                </span>

            @else

                <span class="inline-flex w-fit items-center rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-700">
                    🟢 Cuota al día
                </span>

            @endif

        @endif

        {{-- CUENTA ACTIVADA --}}
        @if($cliente->user_id)

            <span class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                ✅ Cuenta activada
            </span>

        @else

            <span class="inline-flex w-fit items-center rounded-full bg-stone-300 px-2 py-0.5 text-[10px] font-bold text-stone-700">
                ⏳ Sin activar
            </span>

        @endif

        {{-- MENSAJES --}}
        @if($cliente->mensajes_no_leidos_count > 0)
            <span class="inline-flex w-fit items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                💬 Nuevo mensaje
            </span>
        @endif

    </div>

</td>
                            
                            {{-- CUENTA ACTIVADA --}}
@if($cliente->user_id)

    <span class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
        ✅ Cuenta activada
    </span>

@else

    <span class="inline-flex w-fit items-center rounded-full bg-stone-300 px-2 py-0.5 text-[10px] font-bold text-stone-700">
        ⏳ Sin activar
    </span>

@endif

                            {{-- TELÉFONO --}}
                            <td class="p-3 text-sm text-neutral-600 font-medium">
                                {{ $cliente->telefono }}
                            </td>

                            {{-- EMAIL --}}
                            <td class="p-3 text-sm text-neutral-600">
                                {{ $cliente->email }}
                            </td>

                            {{-- ACCIONES --}}
                            <td class="p-3 text-right">

                                <div class="flex items-center justify-end gap-1">

                                    <a href="{{ route('clientes.show', $cliente->id) }}" 
                                       class="p-2 rounded-lg text-neutral-500 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer" 
                                       title="Ver Detalle">
                                        👁️
                                    </a>
                                    
                                    <a href="{{ route('clientes.edit', $cliente->id) }}" 
                                       class="p-2 rounded-lg text-neutral-500 hover:text-yellow-600 hover:bg-yellow-50 transition cursor-pointer" 
                                       title="Editar">
                                        ✏️
                                    </a>
                                    
                                    <button 
                                         wire:click="abrirRenovacion({{ $cliente->id }})"
                                         class="p-2 text-neutral-500 hover:text-green-600 hover:bg-green-50 transition rounded-lg cursor-pointer"
                                         title="Renovar cuota">
                                        💳
                                    </button>   
                                        
                                     <a href="{{ route('clientes.pagos', $cliente->id) }}"
                                     class="p-2 rounded-lg text-neutral-500 hover:text-emerald-600 hover:bg-emerald-50 transition cursor-pointer"
                                     title="Historial de pagos">
                                        💰
                                    </a>

                                    <button 
                                         wire:click="archivar({{ $cliente->id }})" 
                                         wire:confirm="¿Quieres dar de baja a este socio?"
                                         class="p-2 text-neutral-500 hover:text-amber-600 hover:bg-amber-50 transition rounded-lg cursor-pointer"
                                         title="Dar de baja socio">
                                        📦
                                    </button>

                                    <button 
                                        wire:click="delete({{ $cliente->id }})" 
                                        wire:confirm="¿Estás seguro de que deseas eliminar permanentemente a este socio?"
                                        class="p-2 text-neutral-500 hover:text-red-600 hover:bg-red-50 transition rounded-lg cursor-pointer"
                                        title="Eliminar Permanentemente">
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

    {{-- FORMULARIO RENOVACIÓN --}}
@if($clientePagoId)

    <div class="mt-6 rounded-xl border border-stone-300 bg-stone-200 p-4 md:p-6 shadow-sm">

        <h2 class="text-lg font-bold text-orange-600 mb-4">
            💳 Registrar pago y renovar cuota
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- MONTO --}}
            <div>
                <label class="block text-sm font-bold mb-1 text-neutral-700">
                    Monto
                </label>

                <input
                    type="number"
                    wire:model="montoPago"
                    class="w-full rounded-xl border border-stone-300 bg-stone-100 px-4 py-2 text-neutral-900 placeholder-neutral-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    placeholder="Ej: 25000"
                >
            </div>

            {{-- MÉTODO --}}
            <div>
                <label class="block text-sm font-bold mb-1 text-neutral-700">
                    Método de pago
                </label>

                <select
                    wire:model="metodoPago"
                    class="w-full rounded-xl border border-stone-300 bg-stone-100 px-4 py-2 text-neutral-900 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Débito">Débito</option>
                    <option value="Crédito">Crédito</option>
                    <option value="Mercado Pago">Mercado Pago</option>
                </select>
            </div>

            {{-- OBSERVACIÓN --}}
            <div>
                <label class="block text-sm font-bold mb-1 text-neutral-700">
                    Observación
                </label>

                <input
                    type="text"
                    wire:model="observacionPago"
                    class="w-full rounded-xl border border-stone-300 bg-stone-100 px-4 py-2 text-neutral-900 placeholder-neutral-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    placeholder="Opcional"
                >
            </div>

        </div>

        {{-- BOTONES --}}
        <div class="mt-5 flex gap-2">

            <button
                wire:click="renovarCuota"
                class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-bold text-white hover:bg-orange-600 transition"
            >
                ✅ Confirmar pago
            </button>

            <button
                wire:click="cancelarRenovacion"
                class="inline-flex items-center gap-2 rounded-xl bg-stone-100 px-4 py-2 text-sm font-bold text-neutral-700 hover:bg-stone-300 transition border border-stone-300"
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