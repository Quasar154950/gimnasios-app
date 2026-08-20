<x-layouts::app :title="'Administración de actividades'">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 pb-10 sm:-m-6 sm:p-6">

        {{-- MENSAJES --}}
        @if(session('success'))
            <div class="rounded-2xl border border-green-800 bg-green-950/40 px-4 py-3 font-bold text-green-300 shadow-lg">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-red-800 bg-red-950/40 px-4 py-3 font-bold text-red-300 shadow-lg">
                ❌ {{ session('error') }}
            </div>
        @endif


        {{-- ENCABEZADO --}}
        <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div>

                    <div class="mb-2 inline-flex items-center rounded-full bg-blue-950 px-3 py-1 text-xs font-black uppercase text-blue-300">
                        ⚙️ Administración
                    </div>

                    <h1 class="text-3xl font-black text-white">
                        🏋️ Administración de actividades
                    </h1>

                    <p class="mt-2 text-sm text-zinc-400">
                        Gestión de reservas, cupos y ocupación del gimnasio.
                    </p>

                </div>

                <div class="inline-flex items-center rounded-full border border-blue-800 bg-blue-950/40 px-4 py-2 text-xs font-black text-blue-300">
                    📅 {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                </div>

            </div>

        </section>


        {{-- FILTRO FECHA --}}
        <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl">

            <form
                method="GET"
                action="{{ route('turnos.index') }}"
                class="flex flex-col gap-4 md:flex-row md:items-end"
            >

                <div>

                    <label class="mb-1 block text-sm font-bold text-zinc-300">
                        📅 Seleccionar fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        value="{{ $fechaSeleccionada }}"
                        class="rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30"
                    >

                </div>

                <button
                    type="submit"
                    style="cursor: pointer !important;"
                    onclick="
                        this.disabled = true;
                        this.innerHTML = '⏳ Cargando actividades...';
                        this.style.opacity = '0.75';
                        this.style.cursor = 'wait';
                        this.form.submit();
                    "
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white
                           shadow-md transition duration-150
                           hover:-translate-y-0.5 hover:bg-blue-500 hover:shadow-xl
                           active:scale-[0.97]"
                >
                    📅 Ver actividades
                </button>

            </form>

        </section>


        {{-- FIN DE SEMANA --}}
        @if($cerradoFinDeSemana)

            <div class="rounded-2xl border border-yellow-800 bg-yellow-950/30 p-6 text-center font-bold text-yellow-300 shadow-lg">
                🏖️ El gimnasio no tiene clases programadas sábados y domingos.
            </div>

        @else


            {{-- MUSCULACIÓN --}}
            <section
                class="group rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl
                       transition duration-200 hover:-translate-y-0.5 hover:shadow-2xl"
            >

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <h2 class="text-xl font-black text-white">
                            🏋️ Musculación
                        </h2>

                        <p class="mt-1 text-sm text-zinc-400">
                            Acceso libre sin reserva previa.
                        </p>

                    </div>

                    <span class="inline-flex items-center rounded-full bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                        🟢 Libre
                    </span>

                </div>


                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4">
                        <p class="text-sm text-zinc-500">Horario</p>

                        <p class="mt-1 font-black text-white">
                            🕒 06:00 a 23:00
                        </p>
                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4">
                        <p class="text-sm text-zinc-500">Modalidad</p>

                        <p class="mt-1 font-black text-white">
                            🔓 Libre
                        </p>
                    </div>

                    <div class="rounded-2xl border border-green-900/50 bg-green-950/30 p-4">
                        <p class="text-sm text-green-400">Disponibilidad</p>

                        <p class="mt-1 font-black text-green-300">
                            🟢 Disponible
                        </p>
                    </div>

                </div>

            </section>


            {{-- ACTIVIDADES --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

                @foreach($turnos as $turno)

                    @php
                        $reservados = $turno->reservas->count();
                        $disponibles = max($turno->cupo_maximo - $reservados, 0);
                        $completo = $disponibles <= 0;

                        $inicioTurno = \Carbon\Carbon::parse(
                            $turno->fecha . ' ' . $turno->hora_inicio
                        );

                        $finTurno = \Carbon\Carbon::parse(
                            $turno->fecha . ' ' . $turno->hora_fin
                        );

                        $ahora = now();

                        $turnoEnCurso = $ahora->between($inicioTurno, $finTurno);
                        $turnoPasado = $finTurno->isPast();

                        $bloqueado = $completo || $turnoEnCurso || $turnoPasado;
                    @endphp


                    <article
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


                        {{-- RESERVA MANUAL --}}
                        <form
                            method="POST"
                            action="{{ route('turnos.reservar.admin', $turno) }}"
                            class="mt-5 space-y-3 border-t border-zinc-800 pt-4"
                            onsubmit="
                                const boton = this.querySelector('button[type=submit]');

                                if (!boton || boton.disabled) {
                                    return;
                                }

                                boton.disabled = true;
                                boton.innerHTML = '⏳ Reservando...';
                                boton.style.cursor = 'wait';
                                boton.style.opacity = '0.75';
                            "
                        >
                            @csrf

                            <label class="block text-[11px] font-black uppercase text-zinc-500">
                                Reservar socio manualmente
                            </label>

                            <select
                                name="cliente_id"
                                required
                                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-3 text-sm text-white outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30"
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
                                type="submit"
                                @if($bloqueado) disabled @endif
                                style="{{ $bloqueado ? 'cursor: not-allowed !important;' : 'cursor: pointer !important;' }}"
                                class="w-full rounded-xl px-4 py-3 text-sm font-black shadow-md transition duration-150
                                       {{ $bloqueado
                                            ? 'bg-zinc-800 text-zinc-500 opacity-70'
                                            : 'bg-orange-600 text-white hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.97]' }}"
                            >
                                @if($turnoEnCurso)

                                    🟠 Turno en curso

                                @elseif($turnoPasado)

                                    ⏰ Turno finalizado

                                @elseif($completo)

                                    ❌ Turno completo

                                @else

                                    ➕ Reservar para socio

                                @endif
                            </button>

                        </form>


                        {{-- SOCIOS RESERVADOS --}}
                        <div class="mt-5 border-t border-zinc-800 pt-4">

                            <div class="mb-3 text-[11px] font-black uppercase text-zinc-500">
                                Socios reservados
                            </div>


                            <div class="space-y-2">

                                @forelse($turno->reservas as $reserva)

                                    <div class="flex items-center justify-between gap-3 rounded-xl border border-zinc-800 bg-zinc-950 px-3 py-2">

                                        <div class="min-w-0 truncate text-sm font-semibold text-zinc-300">
                                            👤 {{ $reserva->cliente->nombre ?? 'Socio eliminado' }}
                                        </div>


                                        <form
                                            method="POST"
                                            action="{{ route('turnos.reservas.cancelar.admin', $reserva) }}"
                                            onsubmit="
                                                if (!confirm('¿Cancelar esta reserva?')) {
                                                    return false;
                                                }

                                                const boton = this.querySelector('button');

                                                if (boton && !boton.disabled) {
                                                    boton.disabled = true;
                                                    boton.innerHTML = '⏳';
                                                    boton.style.cursor = 'wait';
                                                }

                                                return true;
                                            "
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                @if($turnoEnCurso || $turnoPasado) disabled @endif
                                                style="{{ ($turnoEnCurso || $turnoPasado)
                                                    ? 'cursor: not-allowed !important;'
                                                    : 'cursor: pointer !important;' }}"
                                                class="rounded-full px-3 py-1.5 text-[10px] font-black transition duration-150
                                                       {{ ($turnoEnCurso || $turnoPasado)
                                                            ? 'bg-zinc-800 text-zinc-500 opacity-70'
                                                            : 'bg-red-950 text-red-300 hover:-translate-y-0.5 hover:bg-red-800 hover:text-white active:scale-[0.95]' }}"
                                            >
                                                Cancelar
                                            </button>

                                        </form>

                                    </div>

                                @empty

                                    <div class="rounded-xl border border-dashed border-zinc-700 bg-zinc-950 p-4 text-center text-xs italic text-zinc-500">
                                        Sin reservas
                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </div>

</x-layouts::app>