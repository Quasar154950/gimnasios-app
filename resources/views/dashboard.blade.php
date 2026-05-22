<x-layouts::app :title="__('Panel')">

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @php
        $sociosActivos = $sociosActivos ?? $totalClientes ?? 0;
        $reservasHoy = $reservasHoy ?? 0;
        $cuposOcupadosHoy = $cuposOcupadosHoy ?? 0;
        $proximasReservas = $proximasReservas ?? 0;
        $pagosPendientes = $pagosPendientes ?? 0;
        $presentesAhora = $presentesAhora ?? 0;
    @endphp

    <div class="-m-4 min-h-screen space-y-5 bg-slate-950 p-4 sm:-m-6 sm:p-6 text-left">

        @if(session('success'))
            <div style="border-radius:8px !important;" class="p-3 bg-green-50 border border-green-200 text-green-700 text-sm font-bold shadow-sm flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- 🔔 ALERTA MENSAJES NUEVOS --}}
        @if(isset($mensajesNoLeidos) && $mensajesNoLeidos > 0)

            <div style="border-radius:8px !important;" class="p-4 bg-stone-200 border border-stone-300 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                <div>
                    <div class="font-black text-sm text-neutral-800">
                        🔔 Tenés mensajes nuevos de socios
                    </div>

                    <div class="text-xs mt-1 text-neutral-600">
                        Hay {{ $mensajesNoLeidos }} mensaje{{ $mensajesNoLeidos === 1 ? '' : 's' }} sin leer.
                    </div>
                </div>

                <a href="{{ route('clientes.index') }}"
                   style="background:#f97316;color:white;border-radius:14px;padding:10px 16px;font-size:12px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                    Ver socios
                </a>

            </div>

        @endif

        {{-- CABECERA --}}
        <div style="border-radius:8px !important;" class="border border-stone-300 p-5 bg-stone-200 shadow-sm font-sans">

            <h1 class="text-2xl font-black text-neutral-800">
                Panel del Gimnasio
            </h1>

            <p class="mt-1.5 text-sm text-neutral-600 leading-relaxed">
                Resumen general de socios, reservas, actividades, asistencias y cuotas.
            </p>

        </div>

        {{-- TARJETAS PRINCIPALES --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">

            <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-200 p-5 shadow-sm">
                <div class="text-xs font-black uppercase text-blue-700">Socios activos</div>
                <div class="mt-3 text-3xl font-black text-neutral-800">{{ $sociosActivos }}</div>
                <div class="mt-1 text-xs text-neutral-600">Socios habilitados para entrenar.</div>
            </div>

            <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-200 p-5 shadow-sm">
                <div class="text-xs font-black uppercase text-violet-700">Reservas de hoy</div>
                <div class="mt-3 text-3xl font-black text-neutral-800">{{ $reservasHoy }}</div>
                <div class="mt-1 text-xs text-neutral-600">Turnos reservados para hoy.</div>
            </div>

            <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-200 p-5 shadow-sm">
                <div class="text-xs font-black uppercase text-green-700">Cupos ocupados hoy</div>
                <div class="mt-3 text-3xl font-black text-neutral-800">{{ $cuposOcupadosHoy }}</div>
                <div class="mt-1 text-xs text-neutral-600">Ocupación total de clases de hoy.</div>
            </div>

            <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-200 p-5 shadow-sm">
                <div class="text-xs font-black uppercase text-orange-700">Próximas reservas</div>
                <div class="mt-3 text-3xl font-black text-neutral-800">{{ $proximasReservas }}</div>
                <div class="mt-1 text-xs text-neutral-600">Reservas de los próximos 6 días.</div>
            </div>

            <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-200 p-5 shadow-sm">
                <div class="text-xs font-black uppercase text-red-700">Pagos pendientes</div>
                <div class="mt-3 text-3xl font-black text-neutral-800">{{ $pagosPendientes }}</div>
                <div class="mt-1 text-xs text-neutral-600">Cuotas vencidas o por regularizar.</div>
            </div>

            <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-200 p-5 shadow-sm">
                <div class="text-xs font-black uppercase text-orange-700">Presentes ahora</div>
                <div class="mt-3 text-3xl font-black text-neutral-800">{{ $presentesAhora }}</div>
                <div class="mt-1 text-xs text-neutral-600">Socios actualmente dentro del gimnasio.</div>
            </div>

        </div>

        {{-- ACTIVIDADES --}}
        <div style="border-radius:8px !important;" class="border border-stone-300 p-5 bg-stone-200 shadow-sm font-sans">

            <h2 class="text-lg font-black text-neutral-800">
                Actividades del gimnasio
            </h2>

            <div class="mt-4 grid gap-3 md:grid-cols-3">

                <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-100 p-4">
                    <div class="font-black text-neutral-800">🚴 Spinning</div>
                    <div class="text-xs mt-1 text-neutral-600">Actividad con reserva previa.</div>
                </div>

                <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-100 p-4">
                    <div class="font-black text-neutral-800">🧘 Pilates</div>
                    <div class="text-xs mt-1 text-neutral-600">Actividad con reserva previa.</div>
                </div>

                <div style="border-radius:8px !important;" class="border border-stone-300 bg-stone-100 p-4">
                    <div class="font-black text-neutral-800">🏋️ Musculación</div>
                    <div class="text-xs mt-1 text-neutral-600">Acceso libre de 06:00 a 23:00.</div>

                    <div class="mt-4 inline-flex items-center gap-2 rounded-xl bg-orange-500 px-3 py-2 text-white text-sm font-bold shadow-sm">
                        👥 Presentes ahora: {{ $presentesAhora }}
                    </div>
                </div>

            </div>

        </div>

        {{-- ACCESOS RÁPIDOS --}}
        <div style="border-radius:8px !important;" class="border border-stone-300 p-5 bg-stone-200 shadow-sm font-sans">

            <h2 class="text-lg font-black text-neutral-800">
                Gestión rápida
            </h2>

            <div class="mt-4 flex gap-2.5 overflow-x-auto md:overflow-visible whitespace-nowrap md:whitespace-normal md:flex-wrap">

                <a href="{{ route('clientes.index') }}"
                   style="background:#f97316;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                    👥 Ver socios
                </a>

                <a href="{{ route('clientes.create') }}"
                   style="background:black;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                    ➕ Nuevo socio
                </a>

                @if(Route::has('turnos.index'))

                    <a href="{{ route('turnos.index') }}"
                       style="background:#f97316;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                        📅 Turnos / Reservas
                    </a>

                @endif

                @if(Route::has('asistencias.index'))

                    <a href="{{ route('asistencias.index') }}"
                       style="background:#f97316;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                        👥 Asistencias
                    </a>

                @endif

                @if(Route::has('asistencias.escanear'))

                    <a href="{{ route('asistencias.escanear') }}"
                       style="background:black;color:white;border-radius:14px;padding:10px 16px;font-size:14px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
                        📷 Escanear QR
                    </a>

                @endif

            </div>

        </div>

        {{-- SECCIÓN INFERIOR --}}
        <div class="grid gap-4 md:grid-cols-2">

            {{-- Próximas actividades --}}
            <div style="border-radius:10px !important;" class="border border-stone-300 p-5 bg-stone-200 shadow-sm font-sans">

                <h2 class="text-lg font-black text-neutral-800 mb-4">
                    📌 Próximas clases con reservas
                </h2>

                <div class="space-y-3">

                    @forelse($proximasActividades ?? [] as $actividad)

                        <div style="border-radius:10px !important;" class="p-3 border border-stone-300 bg-stone-100">

                            <div class="flex justify-between items-center">

                                <span class="text-sm font-bold text-neutral-800">
                                    {{ $actividad->actividad }}
                                </span>

                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-orange-500 text-white">
                                    {{ \Carbon\Carbon::parse($actividad->fecha)->format('d/m') }}
                                </span>

                            </div>

                            <p class="text-xs text-neutral-600 mt-2">
                                📅 {{ \Carbon\Carbon::parse($actividad->fecha)->format('d/m/Y') }}
    ·
                                🕒 {{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}
    -
                        {{ \Carbon\Carbon::parse($actividad->hora_fin)->format('H:i') }}
                           </p>

                         <p class="text-xs text-neutral-500 mt-1">
                               👨‍🏫 {{ $actividad->profesor ?? 'Profesor a confirmar' }}
                    </p>

                      <p class="text-xs text-orange-600 font-bold mt-2">
                        👥 Con reservas
                     </p>

                        </div>

                    @empty

                        <p class="text-xs text-neutral-500 italic text-center py-5">
                            No hay actividades próximas cargadas.
                        </p>

                    @endforelse

                </div>

            </div>

            {{-- Últimos socios --}}
            <div style="border-radius:8px !important;" class="border border-stone-300 p-5 bg-stone-200 shadow-sm font-sans">

                <h2 class="text-lg font-black mb-4 text-neutral-800">
                    🆕 Últimos socios incorporados
                </h2>

                <div class="space-y-3">

                    @forelse($ultimosClientes ?? [] as $cliente)

                        <div class="flex justify-between text-sm border-b border-stone-300 pb-2">

                            <a href="{{ route('clientes.show', $cliente->id) }}"
                               class="hover:underline font-bold text-neutral-800">
                                {{ $cliente->nombre }}
                            </a>

                            <span class="text-xs text-neutral-500">
                                {{ $cliente->created_at?->diffForHumans() }}
                            </span>

                        </div>

                    @empty

                        <p class="text-xs text-neutral-500 italic text-center py-5">
                            Todavía no hay socios cargados.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</x-layouts::app>