<div style="border-radius:8px !important;"
     class="border border-stone-300 bg-stone-200 shadow-sm p-5">

    <div class="flex items-start justify-between gap-3">

        <div>
            <h2 class="text-lg font-black text-neutral-800">
                {{ $turno->actividad }}
            </h2>

            <p class="text-xs mt-1 text-neutral-500">
                {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
            </p>
        </div>

        @if($miReserva)
            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-[10px] font-black text-green-700">
                ✅ Reservado
            </span>
        @elseif($turnoEnCurso)
            <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-1 text-[10px] font-black text-orange-700">
                🟠 En curso
            </span>
        @elseif($turnoPasado)
            <span class="inline-flex items-center rounded-full bg-neutral-200 px-2 py-1 text-[10px] font-black text-neutral-700">
                ⏰ Finalizado
            </span>
        @elseif($completo)
            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-[10px] font-black text-red-700">
                ❌ Completo
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-[10px] font-black text-green-700">
                ✅ Disponible
            </span>
        @endif

    </div>

    <div class="mt-5 space-y-3">

        <div style="border-radius:8px !important;"
             class="bg-stone-100 p-3 border border-stone-300">

            <div class="text-xs text-neutral-500">
                Horario
            </div>

            <div class="font-black text-neutral-800 mt-1">
                🕒 {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                -
                {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
            </div>

        </div>

        <div class="grid grid-cols-3 gap-2">

            <div style="border-radius:8px !important;"
                 class="bg-stone-100 p-3 text-center border border-stone-300">
                <div class="text-[10px] uppercase font-black text-neutral-500">
                    Cupos
                </div>

                <div class="mt-1 font-black text-neutral-800">
                    {{ $turno->cupo_maximo }}
                </div>
            </div>

            <div style="border-radius:8px !important;"
                 class="bg-stone-100 p-3 text-center border border-stone-300">
                <div class="text-[9px] uppercase font-black text-neutral-500">
                    Reservados
                </div>

                <div class="mt-1 font-black text-orange-600 transition-all duration-300">
                    {{ $reservados }}
                </div>
            </div>

            <div style="border-radius:8px !important;"
                 class="bg-stone-100 p-3 text-center border border-stone-300">
                <div class="text-[10px] uppercase font-black text-neutral-500">
                    Libres
                </div>

                <div class="mt-1 font-black text-green-700 transition-all duration-300">
                    {{ $disponibles }}
                </div>
            </div>

        </div>

    </div>

    @if($mensajeError)
        <div class="mt-4 rounded-xl bg-red-100 border border-red-300 text-red-700 px-3 py-2 text-xs font-bold">
            ❌ {{ $mensajeError }}
        </div>
    @endif

    <div class="mt-5">

        @if(auth()->user()->role === 'cliente')

            @if($miReserva)

                <button
                    wire:click="cancelar({{ $miReserva->id }})"
                    wire:loading.attr="disabled"
                    wire:target="cancelar"
                    style="background:black;color:white;border-radius:18px;padding:10px 16px;font-size:14px;font-weight:bold;width:100%;transition:0.2s;"
                    class="hover:scale-[1.01] active:scale-[0.99] cursor-pointer"
                >
                    <span wire:loading.remove wire:target="cancelar">
                        Cancelar reserva
                    </span>

                    <span wire:loading wire:target="cancelar">
                        ⏳ Cancelando...
                    </span>
                </button>

            @elseif($turnoEnCurso)

                <button
                    disabled
                    style="border-radius:8px !important;"
                    class="w-full bg-orange-400 px-4 py-2 text-sm font-bold text-white cursor-not-allowed"
                >
                    🟠 Turno en curso
                </button>

            @elseif($turnoPasado)

                <button
                    disabled
                    style="border-radius:8px !important;"
                    class="w-full bg-neutral-400 px-4 py-2 text-sm font-bold text-white cursor-not-allowed"
                >
                    ⏰ Turno finalizado
                </button>

            @elseif($completo)

                <button
                    disabled
                    style="border-radius:8px !important;"
                    class="w-full bg-neutral-300 px-4 py-2 text-sm font-bold text-neutral-600 cursor-not-allowed"
                >
                    Turno completo
                </button>

            @else

                <button
                    wire:click="reservar"
                    wire:loading.attr="disabled"
                    wire:target="reservar"
                    style="background:#f97316;color:white;border-radius:18px;padding:10px 16px;font-size:14px;font-weight:bold;width:100%;transition:0.2s;"
                    class="hover:scale-[1.01] active:scale-[0.99] cursor-pointer"
                >
                    <span wire:loading.remove wire:target="reservar">
                        Reservar actividad
                    </span>

                    <span wire:loading wire:target="reservar">
                        ⏳ Reservando...
                    </span>
                </button>

            @endif

        @else

            <div style="border-radius:8px !important;"
                 class="bg-stone-100 px-4 py-3 text-sm text-zinc-600 text-center font-bold border border-stone-300">
                👨‍💼 Vista administrativa de reservas
            </div>

        @endif

    </div>

</div>