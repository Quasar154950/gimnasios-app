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

        <livewire:admin-turno-card
            :turno="$turno"
            :clientes="$clientes"
            :key="'admin-turno-'.$turno->id"
        />

    @endforeach

</div>

        @endif

    </div>

</x-layouts::app>