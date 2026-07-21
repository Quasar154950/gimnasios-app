<x-layouts::app :title="'Administración de actividades'">

    <div class="space-y-6">

        @if(session('success'))
    <div class="rounded-xl bg-green-100 border border-green-300 text-green-700 px-4 py-3 font-bold">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="rounded-xl bg-red-100 border border-red-300 text-red-700 px-4 py-3 font-bold">
        ❌ {{ session('error') }}
    </div>
@endif

        {{-- ENCABEZADO --}}
        <div class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-black text-stone-800">
                        🏋️ Administración de actividades
                    </h1>

                    <p class="mt-2 text-sm text-stone-600">
                        Gestión de reservas, cupos y ocupación del gimnasio.
                    </p>
                </div>

                <div class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-xs font-black text-blue-700">
                    📅 {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                </div>

            </div>

        </div>

        {{-- FILTRO FECHA --}}
        <div class="rounded-xl border border-stone-300 bg-stone-200 p-5 shadow-sm">

            <form method="GET"
                  action="{{ route('turnos.index') }}"
                  class="flex flex-col md:flex-row md:items-end gap-4">

                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-1">
                        📅 Seleccionar fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        value="{{ $fechaSeleccionada }}"
                        class="rounded-xl border border-stone-300 bg-stone-100 px-4 py-2 text-sm text-stone-800"
                    >
                </div>

                <button
                    type="submit"
                    style="background:black;color:white;padding:12px 20px;border-radius:14px;font-weight:bold;border:2px solid white;"
                >
                    Ver actividades
                </button>

            </form>

        </div>

        {{-- AVISO FIN DE SEMANA --}}
        @if($cerradoFinDeSemana)

            <div class="rounded-xl border border-yellow-300 bg-yellow-50 text-yellow-800 p-5 font-bold text-center shadow-sm">
                🏖️ El gimnasio no tiene clases programadas sábados y domingos.
            </div>

        @else

            {{-- MUSCULACIÓN --}}
            <div class="rounded-xl border border-stone-300 bg-stone-200 shadow-sm p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>
                        <h2 class="text-xl font-black text-green-700">
                            🏋️ Musculación
                        </h2>

                        <p class="text-sm text-stone-600 mt-1">
                            Acceso libre sin reserva previa.
                        </p>
                    </div>

                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                        🟢 Libre
                    </span>

                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="rounded-xl bg-stone-100 p-4 border border-stone-300">
                        <p class="text-sm text-stone-500">Horario</p>

                        <p class="font-black text-stone-800 mt-1">
                            🕒 06:00 a 23:00
                        </p>
                    </div>

                    <div class="rounded-xl bg-stone-100 p-4 border border-stone-300">
                        <p class="text-sm text-stone-500">Modalidad</p>

                        <p class="font-black text-stone-800 mt-1">
                            🔓 Libre
                        </p>
                    </div>

                    <div class="rounded-xl bg-stone-100 p-4 border border-stone-300">
                        <p class="text-sm text-stone-500">Disponibilidad</p>

                        <p class="font-black text-green-700 mt-1">
                            🟢 Disponible
                        </p>
                    </div>

                </div>

            </div>

            {{-- ACTIVIDADES --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

                @foreach($turnos as $turno)

                    @php
                        $reservados = $turno->reservas->count();
                        $disponibles = max($turno->cupo_maximo - $reservados, 0);
                        $completo = $disponibles <= 0;

                        $inicioTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora_inicio);
                        $finTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora_fin);
                        $ahora = now();

                        $turnoEnCurso = $ahora->between($inicioTurno, $finTurno);
                        $turnoPasado = $finTurno->isPast();
                        $bloqueado = $completo || $turnoEnCurso || $turnoPasado;

                        @endphp

                    <div class="rounded-xl border border-stone-300 bg-stone-200 shadow-sm p-4">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <h2 class="text-base font-black text-stone-800">
                                    {{ $turno->actividad }}
                                </h2>

                                <p class="text-[11px] mt-1 text-stone-500">
                                    🕒
                                    {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                                </p>
                            </div>

                            @if($turnoEnCurso)
    <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-1 text-[10px] font-black text-orange-700">
        🟠 En curso
    </span>
@elseif($turnoPasado)
    <span class="inline-flex items-center rounded-full bg-neutral-200 px-2 py-1 text-[10px] font-black text-neutral-700">
        ⏰ Finalizado
    </span>
@elseif($completo)
    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-[10px] font-black text-red-700">
        Completo
    </span>
@else
    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-[10px] font-black text-green-700">
        Disponible
    </span>
@endif

                        </div>

                        <div class="mt-4 flex items-center justify-between text-sm">

                            <div class="text-stone-600">
                                👥 <span class="font-black">{{ $reservados }}</span> / {{ $turno->cupo_maximo }}
                            </div>

                            <div class="font-black text-green-700">
                                {{ $disponibles }} libres
                            </div>

                        </div>
                        
                        <form method="POST"
      action="{{ route('turnos.reservar.admin', $turno) }}"
      class="mt-4 border-t border-stone-300 pt-3 space-y-2">
    @csrf

    <label class="block text-[11px] font-black uppercase text-stone-500">
        Reservar socio manualmente
    </label>

    <select name="cliente_id"
            required
            class="w-full rounded-xl border border-stone-300 bg-stone-100 px-3 py-2 text-sm text-stone-800">
        <option value="">Seleccionar socio...</option>

        @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}">
                {{ $cliente->nombre }}
            </option>
        @endforeach
    </select>

    <button type="submit"
            @if($bloqueado) disabled @endif
            class="w-full rounded-xl px-3 py-2 text-sm font-black
                   {{ $bloqueado ? 'bg-stone-300 text-stone-500 cursor-not-allowed' : 'bg-black text-white' }}">
        ➕ Reservar para socio
    </button>
</form>
                            
                        <div class="mt-4 border-t border-stone-300 pt-3">

                            <div class="text-[11px] font-black uppercase text-stone-500 mb-2">
                                Socios reservados
                            </div>

                            @forelse($turno->reservas as $reserva)

                                <div class="flex items-center justify-between text-sm py-1">

                                    <div class="font-medium text-stone-800">
                                        👤 {{ $reserva->cliente->nombre ?? 'Socio eliminado' }}
                                    </div>

                                    <form method="POST"
      action="{{ route('turnos.reservas.cancelar.admin', $reserva) }}"
      onsubmit="return confirm('¿Cancelar esta reserva?')">
    @csrf
    @method('DELETE')

    <button type="submit"
        @if($turnoEnCurso || $turnoPasado) disabled @endif
        class="rounded-full px-2 py-1 text-[10px] font-black
        {{ ($turnoEnCurso || $turnoPasado)
            ? 'bg-stone-300 text-stone-500 cursor-not-allowed'
            : 'bg-red-100 text-red-700 cursor-pointer hover:bg-red-200 transition-colors' }}">
    Cancelar
</button>
</form>

                                </div>

                            @empty

                                <div class="text-xs text-stone-500 italic">
                                    Sin reservas
                                </div>

                            @endforelse

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</x-layouts::app>