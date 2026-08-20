<div
    class="group overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg
           transition duration-200 hover:-translate-y-0.5 hover:shadow-xl"
>

    <div class="flex items-start justify-between gap-3">

        <div>

            <h2 class="text-lg font-black text-white">
                {{ $turno->actividad }}
            </h2>

            <p class="mt-1 text-xs text-zinc-500">
                {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
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

        @elseif($miReserva)

            <span class="inline-flex items-center rounded-full bg-green-950 px-3 py-1 text-[10px] font-black text-green-300">
                ✅ Reservado
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


    <div class="mt-5 space-y-3">

        {{-- HORARIO --}}
        <div
            class="rounded-xl border border-zinc-800 bg-zinc-950 p-4 transition duration-200 group-hover:border-zinc-700"
        >

            <div class="text-xs text-zinc-500">
                Horario
            </div>

            <div class="mt-1 font-black text-white">
                🕒 {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                -
                {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
            </div>

        </div>


        {{-- CUPOS --}}
        <div class="grid grid-cols-3 gap-2">

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

                <div class="mt-1 text-lg font-black text-orange-300 transition-all duration-300">
                    {{ $reservados }}
                </div>

            </div>


            <div class="rounded-xl border border-green-900/50 bg-green-950/30 p-3 text-center">

                <div class="text-[10px] font-black uppercase text-green-400">
                    Libres
                </div>

                <div class="mt-1 text-lg font-black text-green-300 transition-all duration-300">
                    {{ $disponibles }}
                </div>

            </div>

        </div>

    </div>


    @if($mensajeError)

        <div class="mt-4 rounded-xl border border-red-800 bg-red-950/40 px-3 py-2 text-xs font-bold text-red-300">
            ❌ {{ $mensajeError }}
        </div>

    @endif


    <div class="mt-5">

        @if(auth()->user()->role === 'cliente')

            @if($turnoEnCurso)

                <button
                    disabled
                    class="w-full cursor-not-allowed rounded-xl bg-orange-700/70 px-4 py-3 text-sm font-bold text-white opacity-80"
                >
                    🟠 Turno en curso
                </button>

            @elseif($turnoPasado)

                <button
                    disabled
                    class="w-full cursor-not-allowed rounded-xl bg-zinc-700 px-4 py-3 text-sm font-bold text-zinc-300 opacity-80"
                >
                    ⏰ Turno finalizado
                </button>

            @elseif($miReserva)

                <button
                    wire:click="cancelar({{ $miReserva->id }})"
                    wire:loading.attr="disabled"
                    wire:target="cancelar"
                    class="w-full cursor-pointer rounded-xl bg-red-700 px-4 py-3 text-sm font-bold text-white
                           shadow-md transition duration-150
                           hover:-translate-y-0.5 hover:bg-red-600 hover:shadow-xl
                           active:scale-[0.97]
                           disabled:pointer-events-none disabled:opacity-70"
                >

                    <span wire:loading.remove wire:target="cancelar">
                        🗑 Cancelar reserva
                    </span>

                    <span
                        wire:loading
                        wire:target="cancelar"
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

                        Cancelando...
                    </span>

                </button>

            @elseif($completo)

                <button
                    disabled
                    class="w-full cursor-not-allowed rounded-xl bg-zinc-800 px-4 py-3 text-sm font-bold text-zinc-500 opacity-80"
                >
                    Turno completo
                </button>

            @else

                <button
                    wire:click="reservar"
                    wire:loading.attr="disabled"
                    wire:target="reservar"
                    class="w-full cursor-pointer rounded-xl bg-orange-600 px-4 py-3 text-sm font-bold text-white
                           shadow-md transition duration-150
                           hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl
                           active:scale-[0.97]
                           disabled:pointer-events-none disabled:opacity-70"
                >

                    <span wire:loading.remove wire:target="reservar">
                        ➕ Reservar actividad
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

            @endif

        @else

            <div class="rounded-xl border border-zinc-800 bg-zinc-950 px-4 py-3 text-center text-sm font-bold text-zinc-500">
                👨‍💼 Vista administrativa de reservas
            </div>

        @endif

    </div>

</div>