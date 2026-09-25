<div
    class="group rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg
           transition duration-200 hover:-translate-y-1 hover:shadow-xl"
>

    {{-- CABECERA TARJETA --}}
    <div class="flex items-start justify-between gap-3">

        <div>

            <h2 class="text-lg font-black text-white">
                {{ $turno->actividad }}
            </h2>

            <p class="mt-1 text-xs text-zinc-500">
                🕒
                {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                -
                {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
            </p>

        </div>


        @if($turnoEnCurso)

            <span class="inline-flex items-center rounded-full bg-orange-950 px-3 py-1 text-[10px] font-black text-orange-300">
                🟠 En curso
            </span>

        @elseif($turnoPasado)

            <span class="inline-flex items-center rounded-full bg-zinc-800 px-3 py-1 text-[10px] font-black text-zinc-400">
                ⏰ Finalizado
            </span>

        @elseif($completo)

            <span class="inline-flex items-center rounded-full bg-red-950 px-3 py-1 text-[10px] font-black text-red-300">
                ❌ Completo
            </span>

        @else

            <span class="inline-flex items-center rounded-full bg-green-950 px-3 py-1 text-[10px] font-black text-green-300">
                ✅ Disponible
            </span>

        @endif

    </div>


    {{-- CUPOS --}}
    <div class="mt-4 grid grid-cols-3 gap-2">

        <div class="rounded-xl border border-zinc-800 bg-zinc-950 p-3 text-center">

            <div class="text-[10px] font-black uppercase text-zinc-500">
                Cupos
            </div>

            <div class="mt-1 text-lg font-black text-white">
                {{ $turno->cupo_maximo }}
            </div>

        </div>


        <div class="rounded-xl border border-orange-900/50 bg-orange-950/30 p-3 text-center">

            <div class="text-[9px] font-black uppercase text-orange-400">
                Reservados
            </div>

            <div class="mt-1 text-lg font-black text-orange-300">
                {{ $reservados }}
            </div>

        </div>


        <div class="rounded-xl border border-green-900/50 bg-green-950/30 p-3 text-center">

            <div class="text-[10px] font-black uppercase text-green-400">
                Libres
            </div>

            <div class="mt-1 text-lg font-black text-green-300">
                {{ $disponibles }}
            </div>

        </div>

    </div>


    {{-- MENSAJE ÉXITO --}}
@if($mensajeExito)

    <div
        x-data="{ visible: true }"
        x-init="setTimeout(() => visible = false, 4000)"
        x-show="visible"
        x-transition.opacity.duration.500ms
        class="mt-4 rounded-xl border border-green-800 bg-green-950/40 px-3 py-2 text-xs font-bold text-green-300"
    >
        ✅ {{ $mensajeExito }}
    </div>

@endif


{{-- MENSAJE ERROR --}}
@if($mensajeError)

    <div
        x-data="{ visible: true }"
        x-init="setTimeout(() => visible = false, 4000)"
        x-show="visible"
        x-transition.opacity.duration.500ms
        class="mt-4 rounded-xl border border-red-800 bg-red-950/40 px-3 py-2 text-xs font-bold text-red-300"
    >
        ❌ {{ $mensajeError }}
    </div>

@endif


    {{-- RESERVA MANUAL --}}
    <div class="mt-5 space-y-3 border-t border-zinc-800 pt-4">

        <label class="block text-[11px] font-black uppercase text-zinc-500">
            Reservar socio manualmente
        </label>

        <select
            wire:model="clienteId"
            @if($bloqueado) disabled @endif
            class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-3 text-sm text-white outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30
                   disabled:cursor-not-allowed disabled:opacity-60"
        >

            <option value="">
                Seleccionar socio...
            </option>

            @foreach($clientes as $cliente)

                <option value="{{ $cliente->id }}">
                    {{ $cliente->nombre }}
                </option>

            @endforeach

        </select>


        <button
            type="button"
            wire:click="reservar"
            wire:loading.attr="disabled"
            wire:target="reservar"
            @if($bloqueado) disabled @endif
            style="{{ $bloqueado ? 'cursor: not-allowed !important;' : 'cursor: pointer !important;' }}"
            class="w-full rounded-xl px-4 py-3 text-sm font-black shadow-md transition duration-150
                   {{ $bloqueado
                        ? 'bg-zinc-800 text-zinc-500 opacity-70'
                        : 'bg-orange-600 text-white hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.97]' }}
                   disabled:pointer-events-none disabled:opacity-70"
        >

            <span wire:loading.remove wire:target="reservar">

                @if($turnoEnCurso)

                    🟠 Turno en curso

                @elseif($turnoPasado)

                    ⏰ Turno finalizado

                @elseif($completo)

                    ❌ Turno completo

                @else

                    ➕ Reservar para socio

                @endif

            </span>


            <span
                wire:loading
                wire:target="reservar"
                class="inline-flex items-center justify-center gap-2"
            >

                <svg
                    class="h-4 w-4 animate-spin"
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

                Reservando...

            </span>

        </button>

    </div>


    {{-- SOCIOS RESERVADOS --}}
    <div class="mt-5 border-t border-zinc-800 pt-4">

        <div class="mb-3 text-[11px] font-black uppercase text-zinc-500">
            Socios reservados
        </div>


        <div class="space-y-2">

            @forelse($turno->reservas as $reserva)

                <div
                    wire:key="reserva-admin-{{ $reserva->id }}"
                    class="flex items-center justify-between gap-3 rounded-xl border border-zinc-800 bg-zinc-950 px-3 py-2"
                >

                    <div class="min-w-0 truncate text-sm font-semibold text-zinc-300">
                        👤 {{ $reserva->cliente->nombre ?? 'Socio eliminado' }}
                    </div>


                    <button
                        type="button"
                        wire:click="cancelar({{ $reserva->id }})"
                        wire:confirm="¿Cancelar esta reserva?"
                        wire:loading.attr="disabled"
                        wire:target="cancelar({{ $reserva->id }})"
                        @if($turnoEnCurso || $turnoPasado) disabled @endif
                        style="{{ ($turnoEnCurso || $turnoPasado)
                            ? 'cursor: not-allowed !important;'
                            : 'cursor: pointer !important;' }}"
                        class="rounded-full px-3 py-1.5 text-[10px] font-black transition duration-150
                               {{ ($turnoEnCurso || $turnoPasado)
                                    ? 'bg-zinc-800 text-zinc-500 opacity-70'
                                    : 'bg-red-950 text-red-300 hover:-translate-y-0.5 hover:bg-red-800 hover:text-white active:scale-[0.95]' }}
                               disabled:pointer-events-none disabled:opacity-70"
                    >

                        <span
                            wire:loading.remove
                            wire:target="cancelar({{ $reserva->id }})"
                        >
                            Cancelar
                        </span>

                        <span
                            wire:loading
                            wire:target="cancelar({{ $reserva->id }})"
                        >
                            ⏳
                        </span>

                    </button>

                </div>

            @empty

                <div class="rounded-xl border border-dashed border-zinc-700 bg-zinc-950 p-4 text-center text-xs italic text-zinc-500">
                    Sin reservas
                </div>

            @endforelse

        </div>

    </div>

</div>