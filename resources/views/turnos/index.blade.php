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
    "
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

@endif

@if(!\Carbon\Carbon::parse($fechaSeleccionada)->isWeekend())

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

                <livewire:turno-card :turno="$turno" :key="'turno-card-'.$turno->id" />
            @endforeach

        </div>

        @endif
        
    </div>

</x-layouts::app>