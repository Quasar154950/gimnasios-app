<x-layouts::app :title="__('Panel')">

    <style>
        @keyframes fadeInDashboard {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-card {
            animation: fadeInDashboard .35s ease-out both;
        }
    </style>

    @php
        $sociosActivos = $sociosActivos ?? $totalClientes ?? 0;
        $reservasHoy = $reservasHoy ?? 0;
        $cuposOcupadosHoy = $cuposOcupadosHoy ?? 0;
        $proximasReservas = $proximasReservas ?? 0;
        $pagosPendientes = $pagosPendientes ?? 0;
        $presentesAhora = $presentesAhora ?? 0;

        $esDemoGym = (auth()->user()->slug_estudio ?? null) === 'demo';

        $logoDashboard = $esDemoGym
            ? asset('images/logo-demogym.png')
            : asset('images/logo-sportgym.png');

        $nombreGym = $esDemoGym
            ? 'DemoGym'
            : 'SportGym';
    @endphp


    <div class="-m-4 min-h-screen bg-slate-950 p-4 text-left sm:-m-6 sm:p-6">

        <div class="mx-auto max-w-7xl space-y-6">


            {{-- MENSAJE DE ÉXITO --}}
            @if(session('success'))

                <div
                    class="dashboard-card flex items-center gap-3 rounded-2xl border border-green-800 bg-green-950/70 p-4 font-semibold text-green-300 shadow-lg"
                >
                    <span class="text-xl">✅</span>

                    <span>
                        {{ session('success') }}
                    </span>
                </div>

            @endif


            {{-- MENSAJES NUEVOS --}}
            @if(isset($mensajesNoLeidos) && $mensajesNoLeidos > 0)

                <div
                    class="dashboard-card flex flex-col gap-4 rounded-2xl border border-orange-700/60 bg-orange-950/40 p-5 shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl sm:flex-row sm:items-center sm:justify-between"
                >

                    <div class="flex items-start gap-3">

                        <div class="text-3xl">
                            🔔
                        </div>

                        <div>

                            <div class="font-black text-orange-200">
                                Tenés mensajes nuevos
                            </div>

                            <div class="mt-1 text-sm text-zinc-400">
                                Hay {{ $mensajesNoLeidos }}
                                mensaje{{ $mensajesNoLeidos === 1 ? '' : 's' }}
                                sin leer de socios.
                            </div>

                        </div>

                    </div>

                    <a
                        href="{{ route('clientes.index') }}"
                        class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:bg-orange-500 active:scale-[0.96]"
                    >
                        👥 Ver socios
                    </a>

                </div>

            @endif


            {{-- CABECERA --}}
            <section
                class="dashboard-card overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900 shadow-xl"
            >

                <div class="flex items-center justify-between gap-6 p-6 sm:p-7">

                    <div>

                        <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-orange-950 px-3 py-1 text-xs font-black uppercase tracking-wide text-orange-300">
                            ⚡ Panel principal
                        </div>

                        <h1 class="text-3xl font-black text-white sm:text-4xl">
                            Panel del Gimnasio
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-400">
                            Resumen general de socios, reservas, actividades,
                            asistencias y cuotas.
                        </p>

                    </div>

                    <div class="hidden shrink-0 sm:block">

                        <img
                            src="{{ $logoDashboard }}"
                            alt="{{ $nombreGym }}"
                            class="h-28 w-28 object-contain drop-shadow-2xl transition duration-300 hover:scale-105 lg:h-32 lg:w-32"
                        >

                    </div>

                </div>

            </section>


            {{-- TARJETAS PRINCIPALES --}}
            <section>

                <div class="mb-4 flex items-center justify-between">

                    <div>

                        <h2 class="text-xl font-black text-white">
                            📊 Resumen de hoy
                        </h2>

                        <p class="mt-1 text-sm text-zinc-500">
                            Los datos principales del gimnasio de un vistazo.
                        </p>

                    </div>

                </div>


                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">


                    {{-- SOCIOS --}}
                    <a
                        href="{{ route('clientes.index') }}"
                        class="dashboard-card group cursor-pointer rounded-2xl border border-blue-900/60 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl active:scale-[0.97]"
                    >

                        <div class="flex items-start justify-between">

                            <div class="rounded-xl bg-blue-950 p-3 text-2xl transition group-hover:scale-110">
                                👥
                            </div>

                            <div class="rounded-full bg-blue-950 px-2.5 py-1 text-[10px] font-black uppercase text-blue-300">
                                Socios
                            </div>

                        </div>

                        <div class="mt-5 text-4xl font-black text-white">
                            {{ $sociosActivos }}
                        </div>

                        <div class="mt-2 text-sm font-bold text-blue-300">
                            Socios activos
                        </div>

                        <div class="mt-1 text-xs leading-5 text-zinc-500">
                            Habilitados para entrenar.
                        </div>

                    </a>


                    {{-- RESERVAS HOY --}}
                    <div
                        class="dashboard-card group cursor-default rounded-2xl border border-violet-900/60 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:border-violet-500 hover:shadow-xl"
                    >

                        <div class="flex items-start justify-between">

                            <div class="rounded-xl bg-violet-950 p-3 text-2xl transition group-hover:scale-110">
                                📅
                            </div>

                            <div class="rounded-full bg-violet-950 px-2.5 py-1 text-[10px] font-black uppercase text-violet-300">
                                Hoy
                            </div>

                        </div>

                        <div class="mt-5 text-4xl font-black text-white">
                            {{ $reservasHoy }}
                        </div>

                        <div class="mt-2 text-sm font-bold text-violet-300">
                            Reservas de hoy
                        </div>

                        <div class="mt-1 text-xs leading-5 text-zinc-500">
                            Reservas realizadas para hoy.
                        </div>

                    </div>


                    {{-- CUPOS --}}
                    <div
                        class="dashboard-card group cursor-default rounded-2xl border border-green-900/60 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:border-green-500 hover:shadow-xl"
                    >

                        <div class="flex items-start justify-between">

                            <div class="rounded-xl bg-green-950 p-3 text-2xl transition group-hover:scale-110">
                                🎟️
                            </div>

                            <div class="rounded-full bg-green-950 px-2.5 py-1 text-[10px] font-black uppercase text-green-300">
                                Cupos
                            </div>

                        </div>

                        <div class="mt-5 text-4xl font-black text-white">
                            {{ $cuposOcupadosHoy }}
                        </div>

                        <div class="mt-2 text-sm font-bold text-green-300">
                            Cupos ocupados
                        </div>

                        <div class="mt-1 text-xs leading-5 text-zinc-500">
                            Ocupación de clases de hoy.
                        </div>

                    </div>


                    {{-- PRÓXIMAS RESERVAS --}}
                    <div
                        class="dashboard-card group cursor-default rounded-2xl border border-orange-900/60 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:border-orange-500 hover:shadow-xl"
                    >

                        <div class="flex items-start justify-between">

                            <div class="rounded-xl bg-orange-950 p-3 text-2xl transition group-hover:scale-110">
                                ⏭️
                            </div>

                            <div class="rounded-full bg-orange-950 px-2.5 py-1 text-[10px] font-black uppercase text-orange-300">
                                6 días
                            </div>

                        </div>

                        <div class="mt-5 text-4xl font-black text-white">
                            {{ $proximasReservas }}
                        </div>

                        <div class="mt-2 text-sm font-bold text-orange-300">
                            Próximas reservas
                        </div>

                        <div class="mt-1 text-xs leading-5 text-zinc-500">
                            Reservas de los próximos 6 días.
                        </div>

                    </div>


                    {{-- PAGOS --}}
                    <div
                        class="dashboard-card group cursor-default rounded-2xl border border-red-900/60 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:border-red-500 hover:shadow-xl"
                    >

                        <div class="flex items-start justify-between">

                            <div class="rounded-xl bg-red-950 p-3 text-2xl transition group-hover:scale-110">
                                💳
                            </div>

                            <div class="rounded-full bg-red-950 px-2.5 py-1 text-[10px] font-black uppercase text-red-300">
                                Atención
                            </div>

                        </div>

                        <div class="mt-5 text-4xl font-black text-white">
                            {{ $pagosPendientes }}
                        </div>

                        <div class="mt-2 text-sm font-bold text-red-300">
                            Pagos pendientes
                        </div>

                        <div class="mt-1 text-xs leading-5 text-zinc-500">
                            Cuotas vencidas o pendientes.
                        </div>

                    </div>


                    {{-- PRESENTES --}}
                    <div
                        class="dashboard-card group cursor-default rounded-2xl border border-cyan-900/60 bg-zinc-900 p-5 shadow-lg transition duration-200 hover:-translate-y-1 hover:border-cyan-500 hover:shadow-xl"
                    >

                        <div class="flex items-start justify-between">

                            <div class="rounded-xl bg-cyan-950 p-3 text-2xl transition group-hover:scale-110">
                                🏋️
                            </div>

                            <div class="rounded-full bg-cyan-950 px-2.5 py-1 text-[10px] font-black uppercase text-cyan-300">
                                Ahora
                            </div>

                        </div>

                        <div class="mt-5 text-4xl font-black text-white">
                            {{ $presentesAhora }}
                        </div>

                        <div class="mt-2 text-sm font-bold text-cyan-300">
                            Presentes ahora
                        </div>

                        <div class="mt-1 text-xs leading-5 text-zinc-500">
                            Actualmente en el gimnasio.
                        </div>

                    </div>

                </div>

            </section>


            {{-- ACTIVIDADES --}}
            <section
                class="dashboard-card rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl"
            >

                <div>

                    <h2 class="text-xl font-black text-white">
                        🏃 Actividades del gimnasio
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Modalidades disponibles para tus socios.
                    </p>

                </div>


                <div class="mt-5 grid gap-4 md:grid-cols-3">


                    <div
                        class="group cursor-default rounded-2xl border border-zinc-800 bg-zinc-950 p-5 transition duration-200 hover:-translate-y-1 hover:border-orange-600 hover:shadow-xl"
                    >

                        <div class="text-4xl transition duration-200 group-hover:scale-110">
                            🚴
                        </div>

                        <div class="mt-4 text-lg font-black text-white">
                            Spinning
                        </div>

                        <div class="mt-1 text-sm text-zinc-500">
                            Actividad con reserva previa.
                        </div>

                    </div>


                    <div
                        class="group cursor-default rounded-2xl border border-zinc-800 bg-zinc-950 p-5 transition duration-200 hover:-translate-y-1 hover:border-purple-600 hover:shadow-xl"
                    >

                        <div class="text-4xl transition duration-200 group-hover:scale-110">
                            🧘
                        </div>

                        <div class="mt-4 text-lg font-black text-white">
                            Pilates
                        </div>

                        <div class="mt-1 text-sm text-zinc-500">
                            Actividad con reserva previa.
                        </div>

                    </div>


                    <div
                        class="group cursor-default rounded-2xl border border-zinc-800 bg-zinc-950 p-5 transition duration-200 hover:-translate-y-1 hover:border-green-600 hover:shadow-xl"
                    >

                        <div class="flex items-start justify-between">

                            <div class="text-4xl transition duration-200 group-hover:scale-110">
                                🏋️
                            </div>

                            <div class="rounded-full bg-green-950 px-3 py-1 text-xs font-black text-green-300">
                                {{ $presentesAhora }} presentes
                            </div>

                        </div>

                        <div class="mt-4 text-lg font-black text-white">
                            Musculación
                        </div>

                        <div class="mt-1 text-sm text-zinc-500">
                            Acceso libre de 06:00 a 23:00.
                        </div>

                    </div>


                </div>

            </section>


            {{-- GESTIÓN RÁPIDA --}}
            <section
                class="dashboard-card rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl"
            >

                <h2 class="text-xl font-black text-white">
                    ⚡ Gestión rápida
                </h2>

                <p class="mt-1 text-sm text-zinc-500">
                    Accesos frecuentes del administrador.
                </p>


                <div class="mt-5 flex flex-wrap gap-3">


                    <a
                        href="{{ route('clientes.index') }}"
                        class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.96]"
                    >
                        👥 Ver socios
                    </a>


                    <a
                        href="{{ route('clientes.create') }}"
                        class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-blue-500 hover:shadow-xl active:scale-[0.96]"
                    >
                        ➕ Nuevo socio
                    </a>


                    @if(Route::has('turnos.index'))

                        <a
                            href="{{ route('turnos.index') }}"
                            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-violet-500 hover:shadow-xl active:scale-[0.96]"
                        >
                            📅 Turnos / Reservas
                        </a>

                    @endif


                    @if(Route::has('asistencias.index'))

                        <a
                            href="{{ route('asistencias.index') }}"
                            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-green-500 hover:shadow-xl active:scale-[0.96]"
                        >
                            👥 Asistencias
                        </a>

                    @endif


                    @if(Route::has('asistencias.escanear'))

                        <a
                            href="{{ route('asistencias.escanear') }}"
                            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-zinc-700 px-5 py-3 text-sm font-bold text-white shadow-md transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-600 hover:shadow-xl active:scale-[0.96]"
                        >
                            📷 Escanear QR
                        </a>

                    @endif


                </div>

            </section>


            {{-- PARTE INFERIOR --}}
            <div class="grid gap-6 lg:grid-cols-2">


                {{-- PRÓXIMAS CLASES --}}
                <section
                    class="dashboard-card rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl"
                >

                    <div class="mb-5">

                        <h2 class="text-xl font-black text-white">
                            📌 Próximas clases reservadas
                        </h2>

                        <p class="mt-1 text-sm text-zinc-500">
                            Clases próximas que ya tienen socios inscriptos.
                        </p>

                    </div>


                    <div class="space-y-3">

                        @forelse($proximasActividades ?? [] as $actividad)

                            <div
                                class="group cursor-default rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition duration-200 hover:-translate-y-0.5 hover:border-orange-700 hover:shadow-lg"
                            >

                                <div class="flex items-start justify-between gap-3">

                                    <div>

                                        <div class="text-base font-black text-white">
                                            {{ $actividad->actividad }}
                                        </div>

                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-400">

                                            <span>
                                                📅
                                                {{ \Carbon\Carbon::parse($actividad->fecha)->format('d/m/Y') }}
                                            </span>

                                            <span>
                                                🕒
                                                {{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($actividad->hora_fin)->format('H:i') }}
                                            </span>

                                        </div>

                                        <div class="mt-2 text-xs text-zinc-500">
                                            👨‍🏫 {{ $actividad->profesor ?? 'Profesor a confirmar' }}
                                        </div>

                                    </div>


                                    <div class="shrink-0 rounded-xl bg-orange-600 px-3 py-2 text-center text-xs font-black text-white shadow">
                                        {{ \Carbon\Carbon::parse($actividad->fecha)->format('d/m') }}
                                    </div>

                                </div>


                                <div class="mt-4 border-t border-zinc-800 pt-3">

                                    @if(isset($actividad->reservas_count))

                                        <div class="inline-flex items-center rounded-full bg-orange-950 px-3 py-1.5 text-xs font-bold text-orange-300">
                                            👥 {{ $actividad->reservas_count }}
                                            reserva{{ $actividad->reservas_count == 1 ? '' : 's' }}
                                        </div>

                                    @else

                                        <div class="inline-flex items-center rounded-full bg-orange-950 px-3 py-1.5 text-xs font-bold text-orange-300">
                                            👥 Tiene reservas
                                        </div>

                                    @endif

                                </div>

                            </div>

                        @empty

                            <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-950 p-8 text-center">

                                <div class="text-4xl">
                                    📅
                                </div>

                                <p class="mt-3 text-sm font-semibold text-zinc-400">
                                    No hay clases próximas con reservas.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </section>


                {{-- ÚLTIMOS SOCIOS --}}
                <section
                    class="dashboard-card rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl"
                >

                    <div class="mb-5">

                        <h2 class="text-xl font-black text-white">
                            🆕 Últimos socios incorporados
                        </h2>

                        <p class="mt-1 text-sm text-zinc-500">
                            Socios registrados recientemente.
                        </p>

                    </div>


                    <div class="space-y-3">

                        @forelse($ultimosClientes ?? [] as $cliente)

                            <a
                                href="{{ route('clientes.show', $cliente->id) }}"
                                class="group flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition duration-200 hover:-translate-y-0.5 hover:border-blue-700 hover:shadow-lg active:scale-[0.99]"
                            >

                                <div class="flex min-w-0 items-center gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-950 text-lg">
                                        👤
                                    </div>

                                    <div class="min-w-0">

                                        <div class="truncate text-sm font-black text-white transition group-hover:text-blue-300">
                                            {{ $cliente->nombre }}
                                        </div>

                                        <div class="mt-1 text-xs text-zinc-500">
                                            Nuevo socio
                                        </div>

                                    </div>

                                </div>


                                <span class="shrink-0 rounded-full bg-zinc-800 px-3 py-1.5 text-xs font-semibold text-zinc-300">
                                    {{ $cliente->created_at?->locale('es')->diffForHumans() }}
                                </span>

                            </a>

                        @empty

                            <div class="rounded-2xl border border-dashed border-zinc-700 bg-zinc-950 p-8 text-center">

                                <div class="text-4xl">
                                    👥
                                </div>

                                <p class="mt-3 text-sm font-semibold text-zinc-400">
                                    Todavía no hay socios cargados.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </section>


            </div>


        </div>

    </div>

</x-layouts::app>