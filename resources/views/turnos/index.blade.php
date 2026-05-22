<x-layouts::app :title="'Turnos'">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 sm:-m-6 sm:p-6">

        {{-- ALERTAS --}}
        @if(session('success'))
            <div style="border-radius:8px !important;"
                 class="bg-green-500/20 border border-green-500/30 text-green-300 px-4 py-3 font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="border-radius:8px !important;"
                 class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 font-bold">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- ENCABEZADO --}}
        <div style="border-radius:8px !important;"
             class="border border-stone-300 bg-stone-200 p-5 md:p-6 shadow-sm">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <h1 class="text-2xl font-black text-neutral-800">
                        🏋️ Actividades y reservas
                    </h1>

                    <p class="mt-2 text-sm text-neutral-600">
                        Gestión de clases, reservas, cupos y disponibilidad del gimnasio.
                    </p>

                </div>

                <div class="inline-flex items-center rounded-full bg-orange-500/20 px-4 py-2 text-xs font-black text-orange-600 border border-orange-500/30">
                    📅 {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                </div>

            </div>

        </div>

        {{-- FILTRO FECHA --}}
        <div style="border-radius:8px !important;"
             class="border border-stone-300 bg-stone-200 shadow-sm p-5">

            <form method="GET"
                  action="{{ auth()->user()->role === 'cliente' ? route('cliente.turnos') : route('turnos.index') }}"
                  class="flex flex-col md:flex-row md:items-end gap-4">

                <div>

                    <label class="block text-sm font-bold text-neutral-700 mb-1">
                        📅 Seleccionar fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        value="{{ $fechaSeleccionada }}"
                        min="{{ now()->toDateString() }}"
                        style="border-radius:12px !important;"
                        class="border border-stone-300 bg-stone-100 px-4 py-2 text-sm text-neutral-800"
                    >

                </div>

                <button
                    type="submit"
                    style="background:black;color:white;border-radius:14px;padding:10px 18px;font-size:14px;font-weight:bold;display:inline-flex;align-items:center;justify-content:center;border:none;"
                >
                    Ver actividades
                </button>

            </form>

        </div>

        {{-- FIN DE SEMANA --}}
        @if(\Carbon\Carbon::parse($fechaSeleccionada)->isWeekend())

            <div style="border-radius:8px !important;"
                 class="border border-orange-500/30 bg-orange-500/20 text-orange-200 p-6 font-bold text-center shadow-sm">

                🏖️ Gimnasio cerrado. No hay actividades disponibles sábados y domingos.

            </div>

            @else

        {{-- MUSCULACIÓN --}}
        <div style="border-radius:8px !important;"
             class="border border-stone-300 bg-stone-200 shadow-sm p-5">

            <div class="flex items-center justify-between gap-3">

                <div>

                    <h2 class="text-xl font-black text-neutral-800">
                        🏋️ Musculación
                    </h2>

                    <p class="text-sm text-neutral-600 mt-1">
                        Acceso libre sin reserva previa.
                    </p>

                </div>

                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                    🟢 Libre
                </span>

            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">

                <div style="border-radius:8px !important;"
                     class="bg-stone-100 p-4 border border-stone-300">

                    <p class="text-sm text-zinc-500">
                        Horario
                    </p>

                    <p class="font-black text-zinc-800 mt-1">
                        🕒 06:00 a 23:00
                    </p>

                </div>

                <div style="border-radius:8px !important;"
                     class="bg-stone-100 p-4 border border-stone-300">

                    <p class="text-sm text-zinc-500">
                        Modalidad
                    </p>

                    <p class="font-black text-zinc-800 mt-1">
                        🔓 Libre
                    </p>

                </div>

                <div style="border-radius:8px !important;"
                     class="bg-stone-100 p-4 border border-stone-300">

                    <p class="text-sm text-zinc-500">
                        Disponibilidad
                    </p>

                    <p class="font-black text-green-700 mt-1">
                        🟢 Disponible
                    </p>

                </div>

            </div>

        </div>

        {{-- LISTADO DE ACTIVIDADES --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

            @foreach($turnos as $turno)

                @php
                    $reservados = $turno->reservas->count();
                    $disponibles = max($turno->cupo_maximo - $reservados, 0);
                    $completo = $disponibles <= 0;
                @endphp

                <div style="border-radius:8px !important;"
                     class="border border-stone-300 bg-stone-200 shadow-sm p-5">

                    {{-- CABECERA --}}
                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <h2 class="text-lg font-black text-neutral-800">
                                {{ $turno->actividad }}
                            </h2>

                            <p class="text-xs mt-1 text-neutral-500">
                                {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
                            </p>

                        </div>

                        @if($completo)

                            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-[10px] font-black text-red-700">
                                ❌ Completo
                            </span>

                        @else

                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-[10px] font-black text-green-700">
                                ✅ Disponible
                            </span>

                        @endif

                    </div>

                    {{-- DATOS --}}
                    <div class="mt-5 space-y-3">

                        <div style="border-radius:8px !important;"
                             class="bg-stone-100 p-3 border border-stone-300">

                            <div class="text-xs text-neutral-500">
                                Horario
                            </div>

                            <div class="font-black text-neutral-800 mt-1">
                                🕒
                                {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}
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

                                <div class="mt-1 font-black text-orange-600">
                                    {{ $reservados }}
                                </div>

                            </div>

                            <div style="border-radius:8px !important;"
                                 class="bg-stone-100 p-3 text-center border border-stone-300">

                                <div class="text-[10px] uppercase font-black text-neutral-500">
                                    Libres
                                </div>

                                <div class="mt-1 font-black text-green-700">
                                    {{ $disponibles }}
                                </div>

                            </div>

                        </div>

                        {{-- 👥 RESERVAS ADMIN --}}
                        @if(auth()->user()->role !== 'cliente')

                            <div style="border-radius:8px !important;"
                                 class="bg-stone-100 p-4 border border-stone-300">

                                <div class="flex items-center justify-between mb-3">

                                    <div class="text-xs font-black uppercase text-neutral-500">
                                        Socios reservados
                                    </div>

                                    <div class="text-xs font-black text-neutral-700">
                                        {{ $reservados }}
                                    </div>

                                </div>

                                @forelse($turno->reservas as $reserva)

                                    <div class="flex items-center justify-between py-2 border-b border-neutral-300 last:border-none">

                                        <div class="text-sm font-bold text-neutral-800">
                                            👤 {{ $reserva->cliente->nombre ?? 'Socio' }}
                                        </div>

                                        <div class="text-[10px] font-black text-green-600">
                                            CONFIRMADO
                                        </div>

                                    </div>

                                @empty

                                    <div class="text-xs text-neutral-500 italic">
                                        No hay reservas todavía.
                                    </div>

                                @endforelse

                            </div>

                        @endif

                    </div>

                    {{-- BOTONES --}}
                    <div class="mt-5">

                        @if(auth()->user()->role === 'cliente')

                            @if($completo)

                                <button
                                    disabled
                                    style="border-radius:8px !important;"
                                    class="w-full bg-neutral-300 px-4 py-2 text-sm font-bold text-neutral-600 cursor-not-allowed"
                                >
                                    Turno completo
                                </button>

                            @else

                                <form method="POST" action="{{ route('cliente.turnos.reservar', $turno) }}">
                                    @csrf

                                    <button
                                        type="submit"
                                        style="background:#f97316;color:white;border-radius:18px;padding:10px 16px;font-size:14px;font-weight:bold;width:100%;"
                                    >
                                        Reservar actividad
                                    </button>

                                </form>

                            @endif

                        @else

                            <div style="border-radius:8px !important;"
                                 class="bg-stone-100 px-4 py-3 text-sm text-zinc-600 text-center font-bold border border-stone-300">

                                👨‍💼 Vista administrativa de reservas

                            </div>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</x-layouts::app>