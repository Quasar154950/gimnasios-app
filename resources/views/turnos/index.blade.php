<x-layouts::app :title="'Turnos'">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 pb-32 sm:-m-6 sm:p-6 sm:pb-32">

        {{-- ALERTAS --}}
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
        <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl md:p-6">

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div>

                    <div class="mb-2 inline-flex items-center rounded-full bg-orange-950 px-3 py-1 text-xs font-black uppercase text-orange-300">
                        📅 Reservas
                    </div>

                    <h1 class="text-3xl font-black text-white">
                        🏋️ Actividades y reservas
                    </h1>

                    <p class="mt-2 text-sm text-zinc-400">
                        Gestión de clases, reservas, cupos y disponibilidad del gimnasio.
                    </p>

                </div>

                <div class="inline-flex items-center rounded-full border border-orange-800 bg-orange-950/40 px-4 py-2 text-xs font-black text-orange-300">
                    📅 {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                </div>

            </div>

        </section>


        {{-- FILTRO FECHA --}}
        <section class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl">

            <form
                method="GET"
                action="{{ auth()->user()->role === 'cliente' ? route('cliente.turnos') : route('turnos.index') }}"
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
                        min="{{ now()->toDateString() }}"
                        class="rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30"
                    >

                </div>

                <button
                    type="submit"
                    onclick="
                        this.disabled = true;
                        this.innerHTML = '⏳ Cargando actividades...';
                        this.classList.add('cursor-wait', 'opacity-75');
                        this.form.submit();
                    "
                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white
                           shadow-md transition duration-150
                           hover:-translate-y-0.5 hover:bg-blue-500 hover:shadow-xl
                           active:scale-[0.97]
                           disabled:pointer-events-none"
                >
                    📅 Ver actividades
                </button>

            </form>

        </section>


        {{-- FIN DE SEMANA --}}
        @if(\Carbon\Carbon::parse($fechaSeleccionada)->isSunday())

            <div class="rounded-2xl border border-orange-800 bg-orange-950/30 p-6 text-center font-bold text-orange-300 shadow-lg">
                🏖️ Gimnasio cerrado. Los domingos no hay actividades disponibles.
            </div>

        @endif


        @if(!\Carbon\Carbon::parse($fechaSeleccionada)->isSunday())

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


                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">

                    {{-- HORARIO --}}
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4">

                        <p class="text-sm text-zinc-500">
                            Horario
                        </p>

                        <p class="mt-1 font-black text-white">
                            🕒 06:00 a 23:00
                        </p>

                    </div>


                    {{-- MODALIDAD --}}
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4">

                        <p class="text-sm text-zinc-500">
                            Modalidad
                        </p>

                        <p class="mt-1 font-black text-white">
                            🔓 Libre
                        </p>

                    </div>


                    {{-- DISPONIBILIDAD --}}
                    <div class="rounded-2xl border border-green-900/50 bg-green-950/30 p-4">

                        <p class="text-sm text-green-400">
                            Disponibilidad
                        </p>

                        <p class="mt-1 font-black text-green-300">
                            🟢 Disponible
                        </p>

                    </div>


                    {{-- PRESENTES --}}
                    <div class="rounded-2xl border border-orange-900/50 bg-orange-950/30 p-4">

                        <p class="text-sm font-bold text-orange-400">
                            Presentes ahora
                        </p>

                        <p class="mt-1 text-lg font-black text-orange-300">
                            👥 {{ $presentesAhora ?? 0 }} socios
                        </p>

                    </div>

                </div>

            </section>


            @if(!\Carbon\Carbon::parse($fechaSeleccionada)->isSaturday())

                {{-- LISTADO DE ACTIVIDADES --}}
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                    @foreach($turnos as $turno)

                        <livewire:turno-card
                            :turno="$turno"
                            :key="'turno-card-'.$turno->id"
                        />

                    @endforeach

                </div>

            @endif

        @endif

    </div>


    @if(auth()->user()->role === 'cliente')

        {{-- BARRA INFERIOR --}}
        <div class="fixed bottom-0 left-0 right-0 z-50">

            <div class="mx-auto max-w-md px-5 pb-4">

                <div class="relative flex items-center justify-between rounded-[2rem] bg-white px-5 py-3 text-zinc-900 shadow-2xl">

                    <a
                        href="{{ route('cliente.dashboard') }}"
                        class="cursor-pointer text-center text-xs font-bold transition hover:scale-105 active:scale-[0.96]"
                    >
                        <div class="text-xl">🏠</div>
                        Inicio
                    </a>

                    <a
                        href="{{ route('cliente.turnos') }}"
                        class="cursor-pointer text-center text-xs font-bold transition hover:scale-105 active:scale-[0.96]"
                    >
                        <div class="text-xl">📅</div>
                        Reservas
                    </a>

                    <a
                        href="{{ route('cliente.mi-qr') }}"
                        class="absolute left-1/2 -top-7 flex h-16 w-16 -translate-x-1/2 cursor-pointer items-center justify-center rounded-full border-4 border-[#071015] bg-orange-500 text-3xl text-white shadow-xl transition hover:scale-105 active:scale-[0.95]"
                    >
                        📱
                    </a>

                    <a
                        href="{{ route('cliente.mensajes') }}"
                        class="ml-14 cursor-pointer text-center text-xs font-bold transition hover:scale-105 active:scale-[0.96]"
                    >
                        <div class="text-xl">🔔</div>
                        Avisos
                    </a>

                    <a
                        href="#"
                        class="cursor-pointer text-center text-xs font-bold transition hover:scale-105 active:scale-[0.96]"
                    >
                        <div class="text-xl">👤</div>
                        Perfil
                    </a>

                </div>

            </div>

        </div>

    @endif

</x-layouts::app>