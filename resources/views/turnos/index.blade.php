<x-layouts::app :title="'Turnos'">

    <div class="-m-4 min-h-screen space-y-6 bg-slate-950 p-4 pb-32 sm:-m-6 sm:p-6 sm:pb-32">

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

                    onclick="
                     this.disabled = true;
                     this.innerHTML = '⏳ Cargando actividades...';
                     this.style.opacity = '0.75';
                     this.form.submit();
                    "

                    style="
                        background:black;
                        color:white;
                        border-radius:14px;
                        padding:10px 18px;
                        font-size:14px;
                        font-weight:bold;
                        display:inline-flex;
                        align-items:center;
                        justify-content:center;
                        border:none;
                        transition:0.2s;
                        cursor:pointer;
                    "
                >
                    Ver actividades
                </button>

            </form>

        </div>

        {{-- FIN DE SEMANA --}}
        @if(\Carbon\Carbon::parse($fechaSeleccionada)->isSunday())

            <div style="border-radius:8px !important;"
                 class="border border-orange-500/30 bg-orange-500/20 text-orange-200 p-6 font-bold text-center shadow-sm">

                🏖️ Gimnasio cerrado. Los domingos no hay actividades disponibles.

            </div>

        @endif

        @if(!\Carbon\Carbon::parse($fechaSeleccionada)->isSunday())

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

                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- HORARIO --}}
                    <div style="border-radius:8px !important;"
                         class="bg-stone-100 p-4 border border-stone-300">

                        <p class="text-sm text-zinc-500">
                            Horario
                        </p>

                        <p class="font-black text-zinc-800 mt-1">
                            🕒 06:00 a 23:00
                        </p>

                    </div>

                    {{-- MODALIDAD --}}
                    <div style="border-radius:8px !important;"
                         class="bg-stone-100 p-4 border border-stone-300">

                        <p class="text-sm text-zinc-500">
                            Modalidad
                        </p>

                        <p class="font-black text-zinc-800 mt-1">
                            🔓 Libre
                        </p>

                    </div>

                    {{-- DISPONIBILIDAD --}}
                    <div style="border-radius:8px !important;"
                         class="bg-stone-100 p-4 border border-stone-300">

                        <p class="text-sm text-zinc-500">
                            Disponibilidad
                        </p>

                        <p class="font-black text-green-700 mt-1">
                            🟢 Disponible
                        </p>

                    </div>

                    {{-- PRESENTES --}}
                    <div style="border-radius:8px !important;"
                         class="bg-orange-100 p-4 border border-orange-300">

                        <p class="text-sm text-orange-700 font-bold">
                            Presentes ahora
                        </p>

                        <p class="font-black text-orange-700 mt-1 text-lg">
                            👥 {{ $presentesAhora ?? 0 }} socios
                        </p>

                    </div>

                </div>

            </div>
            
                @if(!\Carbon\Carbon::parse($fechaSeleccionada)->isSaturday())

    {{-- LISTADO DE ACTIVIDADES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

        @foreach($turnos as $turno)

            <livewire:turno-card :turno="$turno" :key="'turno-card-'.$turno->id" />

        @endforeach

    </div>

@endif

{{-- Cierra el bloque de NO DOMINGO --}}
@endif

</div>

  @if(auth()->user()->role === 'cliente')
    {{-- BARRA INFERIOR --}}
    <div class="fixed bottom-0 left-0 right-0 z-50">
        <div class="max-w-md mx-auto px-5 pb-4">
            <div class="relative rounded-[2rem] bg-white text-zinc-900 shadow-2xl px-5 py-3 flex items-center justify-between">

                <a href="{{ route('cliente.dashboard') }}" class="text-center text-xs font-bold">
                    <div class="text-xl">🏠</div>
                    Inicio
                </a>

                <a href="{{ route('cliente.turnos') }}" class="text-center text-xs font-bold">
                    <div class="text-xl">📅</div>
                    Reservas
                </a>

                <a href="{{ route('cliente.mi-qr') }}"
                   class="absolute left-1/2 -translate-x-1/2 -top-7 h-16 w-16 rounded-full bg-orange-500 text-white flex items-center justify-center text-3xl shadow-xl border-4 border-[#071015]">
                    📱
                </a>

                <a href="{{ route('cliente.mensajes') }}" class="text-center text-xs font-bold ml-14">
                    <div class="text-xl">🔔</div>
                    Avisos
                </a>

                <a href="#" class="text-center text-xs font-bold">
                    <div class="text-xl">👤</div>
                    Perfil
                </a>

            </div>
        </div>
    </div>
@endif  

</x-layouts::app>