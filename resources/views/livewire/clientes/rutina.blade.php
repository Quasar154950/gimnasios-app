<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8">

            <a
                href="{{ route('clientes.index') }}"
                wire:navigate
                class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-zinc-400 transition hover:text-white"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m15 18-6-6 6-6"/>
                </svg>

                Volver a socios
            </a>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

                <div>

                    <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-green-400">
                        Entrenamiento del socio
                    </p>

                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ $cliente->nombre }}
                    </h1>

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-zinc-400">

                        @if($cliente->email)
                            <span>
                                ✉️ {{ $cliente->email }}
                            </span>
                        @endif

                        @if($cliente->telefono)
                            <span>
                                📞 {{ $cliente->telefono }}
                            </span>
                        @endif

                    </div>

                </div>

                <a
                    href="{{ route('rutinas.index') }}"
                    wire:navigate
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:border-green-700 hover:bg-zinc-800 active:scale-95"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                    </svg>

                    Ver rutinas
                </a>

            </div>

        </div>

        {{-- MENSAJE DE ÉXITO --}}
        @if(session('success'))

            <div
                x-data="{ visible: true }"
                x-show="visible"
                x-init="setTimeout(() => visible = false, 4000)"
                class="mb-6 rounded-2xl border border-green-800 bg-green-950/50 px-5 py-4 font-semibold text-green-300"
            >
                ✅ {{ session('success') }}
            </div>

        @endif

        {{-- RUTINA ACTIVA --}}
        @if($asignacionActiva)

            <section class="mb-10">

                <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="mb-2 flex items-center gap-3">

                            <span class="inline-flex items-center rounded-full border border-green-700 bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                                🟢 Rutina activa
                            </span>

                        </div>

                        <h2 class="text-2xl font-bold text-white sm:text-3xl">
                            {{ $asignacionActiva->rutina->nombre }}
                        </h2>

                        @if($asignacionActiva->rutina->objetivo)
                            <p class="mt-2 font-semibold text-green-400">
                                {{ $asignacionActiva->rutina->objetivo }}
                            </p>
                        @endif

                    </div>

                    <div class="flex flex-wrap gap-3">

                        <a
                            href="{{ route('rutinas.index') }}"
                            wire:navigate
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-green-500 active:scale-95"
                        >
                            🔄 Cambiar rutina
                        </a>

                        <button
                            type="button"
                            wire:click="finalizarRutina({{ $asignacionActiva->id }})"
                            wire:confirm="¿Confirmás que querés finalizar la rutina activa de este socio?"
                            wire:loading.attr="disabled"
                            wire:target="finalizarRutina"
                            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-red-800 bg-red-950/50 px-5 py-3 text-sm font-bold text-red-300 transition hover:bg-red-900/60 disabled:cursor-wait disabled:opacity-60"
                        >
                            <span
                                wire:loading.remove
                                wire:target="finalizarRutina"
                            >
                                ⛔ Finalizar rutina
                            </span>

                            <span
                                wire:loading
                                wire:target="finalizarRutina"
                            >
                                Finalizando...
                            </span>
                        </button>

                    </div>

                </div>

                {{-- INFORMACIÓN GENERAL --}}
                <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">

                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Fecha de inicio
                        </p>

                        <p class="mt-2 text-lg font-bold text-white">
                            {{ $asignacionActiva->fecha_inicio
                                ? \Carbon\Carbon::parse($asignacionActiva->fecha_inicio)->format('d/m/Y')
                                : 'Sin definir'
                            }}
                        </p>

                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">

                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Fecha de finalización
                        </p>

                        <p class="mt-2 text-lg font-bold text-white">
                            {{ $asignacionActiva->fecha_fin
                                ? \Carbon\Carbon::parse($asignacionActiva->fecha_fin)->format('d/m/Y')
                                : 'Sin definir'
                            }}
                        </p>

                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">

                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Próxima revisión
                        </p>

                        <p class="mt-2 text-lg font-bold text-white">
                            {{ $asignacionActiva->fecha_revision
                                ? \Carbon\Carbon::parse($asignacionActiva->fecha_revision)->format('d/m/Y')
                                : 'Sin definir'
                            }}
                        </p>

                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">

                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Duración estimada
                        </p>

                        <p class="mt-2 text-lg font-bold text-white">
                            {{ $asignacionActiva->rutina->duracion_semanas
                                ? $asignacionActiva->rutina->duracion_semanas . ' semanas'
                                : 'Sin definir'
                            }}
                        </p>

                    </div>

                </div>

                {{-- DESCRIPCIÓN Y OBSERVACIONES --}}
                @if(
                    $asignacionActiva->rutina->descripcion ||
                    $asignacionActiva->observaciones
                )

                    <div class="mb-7 grid gap-5 lg:grid-cols-2">

                        @if($asignacionActiva->rutina->descripcion)

                            <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">

                                <h3 class="font-bold text-white">
                                    Descripción de la rutina
                                </h3>

                                <p class="mt-3 text-sm leading-6 text-zinc-400">
                                    {{ $asignacionActiva->rutina->descripcion }}
                                </p>

                            </div>

                        @endif

                        @if($asignacionActiva->observaciones)

                            <div class="rounded-2xl border border-yellow-900/70 bg-yellow-950/20 p-5">

                                <h3 class="font-bold text-yellow-300">
                                    Observaciones del profesor
                                </h3>

                                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-yellow-100/70">
                                    {{ $asignacionActiva->observaciones }}
                                </p>

                            </div>

                        @endif

                    </div>

                @endif

                {{-- DÍAS DE ENTRENAMIENTO --}}
                <div class="mb-5">

                    <p class="text-sm font-semibold uppercase tracking-widest text-green-400">
                        Plan semanal
                    </p>

                    <h3 class="mt-1 text-2xl font-bold text-white">
                        Días y ejercicios
                    </h3>

                </div>

                <div class="grid gap-6 lg:grid-cols-2">

                    @forelse($asignacionActiva->rutina->dias as $dia)

                        <article
                            wire:key="rutina-dia-socio-{{ $dia->id }}"
                            class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-xl"
                        >

                            <div class="border-b border-zinc-800 bg-zinc-900/80 px-5 py-4">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-4">

                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-950 text-lg font-black text-green-400">
                                            {{ $dia->orden }}
                                        </div>

                                        <div>

                                            <h4 class="text-xl font-bold text-white">
                                                {{ $dia->nombre }}
                                            </h4>

                                            @if($dia->descripcion)
                                                <p class="mt-1 text-sm text-zinc-400">
                                                    {{ $dia->descripcion }}
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                    <span class="rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1 text-xs font-bold text-zinc-300">
                                        {{ $dia->ejercicios->count() }}
                                        {{ $dia->ejercicios->count() === 1
                                            ? 'ejercicio'
                                            : 'ejercicios'
                                        }}
                                    </span>

                                </div>

                            </div>

                            <div class="divide-y divide-zinc-800">

                                @forelse($dia->ejercicios as $ejercicio)

                                    <div
                                        wire:key="rutina-dia-{{ $dia->id }}-ejercicio-{{ $ejercicio->id }}"
                                        class="p-5"
                                    >

                                        <div class="flex items-start gap-4">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-zinc-800 font-black text-green-400">
                                                {{ $loop->iteration }}
                                            </div>

                                            <div class="min-w-0 flex-1">

                                                <h5 class="text-base font-bold text-white">
    {{ $ejercicio->ejercicioBiblioteca?->nombre ?? $ejercicio->ejercicio ?? 'Ejercicio sin nombre' }}
</h5>

@php
    $grupoMuscular = $ejercicio->ejercicio?->grupo_muscular
        ?? $ejercicio->grupo_muscular;
@endphp

@if($grupoMuscular)
    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-zinc-500">
        {{ $grupoMuscular }}
    </p>
@endif

                                                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">

                                                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/70 px-3 py-3">

                                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                                                            Series
                                                        </p>

                                                        <p class="mt-1 font-black text-white">
                                                            {{ $ejercicio->series ?? '—' }}
                                                        </p>

                                                    </div>

                                                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/70 px-3 py-3">

                                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                                                            Repeticiones
                                                        </p>

                                                        <p class="mt-1 font-black text-white">
                                                            {{ $ejercicio->repeticiones ?? '—' }}
                                                        </p>

                                                    </div>

                                                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/70 px-3 py-3">

                                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                                                            Descanso
                                                        </p>

                                                        <p class="mt-1 font-black text-white">
                                                            @if($ejercicio->descanso_segundos ?? null)
                                                                {{ $ejercicio->descanso_segundos }} s
                                                            @else
                                                                —
                                                            @endif
                                                        </p>

                                                    </div>

                                                    <div class="rounded-xl border border-zinc-800 bg-zinc-950/70 px-3 py-3">

                                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                                                            Peso
                                                        </p>

                                                        <p class="mt-1 font-black text-white">
                                                            @if($ejercicio->peso ?? null)
                                                                {{ $ejercicio->peso }} kg
                                                            @else
                                                                —
                                                            @endif
                                                        </p>

                                                    </div>

                                                </div>

                                                @if($ejercicio->observaciones ?? null)

                                                    <div class="mt-4 rounded-xl border border-blue-900/60 bg-blue-950/20 px-4 py-3">

                                                        <p class="text-xs font-bold uppercase tracking-wide text-blue-400">
                                                            Indicaciones
                                                        </p>

                                                        <p class="mt-1 text-sm leading-6 text-blue-100/70">
                                                            {{ $ejercicio->observaciones }}
                                                        </p>

                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="px-5 py-10 text-center">

                                        <p class="font-semibold text-zinc-400">
                                            Este día todavía no tiene ejercicios.
                                        </p>

                                    </div>

                                @endforelse

                            </div>

                        </article>

                    @empty

                        <div class="col-span-full rounded-2xl border border-dashed border-zinc-700 bg-zinc-900/50 px-6 py-14 text-center">

                            <h3 class="text-xl font-bold text-white">
                                La rutina no tiene días configurados
                            </h3>

                            <p class="mt-2 text-sm text-zinc-400">
                                Editá la rutina para organizar sus días y ejercicios.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>

        @else

            {{-- SIN RUTINA ACTIVA --}}
            <section class="mb-10 rounded-3xl border border-dashed border-zinc-700 bg-zinc-900/60 px-6 py-14 text-center">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-zinc-800 text-3xl">
                    💪
                </div>

                <h2 class="mt-5 text-2xl font-bold text-white">
                    Este socio no tiene una rutina activa
                </h2>

                <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-zinc-400">
                    Podés ingresar al listado de rutinas, elegir una y asignarla
                    a este socio.
                </p>

                <button
                    type="button"
                    wire:click="abrirFormularioAsignacion"
                    class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-green-500 active:scale-95"
                >
                    ➕ Asignar una rutina
                </button>

            @if($mostrarFormularioAsignacion)

<div class="mt-8 rounded-2xl border border-zinc-700 bg-zinc-900 p-6">

    <h3 class="mb-6 text-xl font-bold text-white">
        Asignar rutina
    </h3>

    <div class="grid gap-5 md:grid-cols-2">

        <div>
            <label class="mb-2 block text-sm font-bold text-zinc-300">
                Rutina
            </label>

            <select
                wire:model="rutinaId"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-800 p-3 text-white"
            >
                <option value="">Seleccionar...</option>

                @foreach($rutinasDisponibles as $rutina)

                    <option value="{{ $rutina->id }}">
                        {{ $rutina->nombre }}
                    </option>

                @endforeach
            </select>

            @error('rutinaId')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-zinc-300">
                Fecha inicio
            </label>

            <input
                type="date"
                wire:model="fechaInicio"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-800 p-3 text-white"
            >
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-zinc-300">
                Fecha fin
            </label>

            <input
                type="date"
                wire:model="fechaFin"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-800 p-3 text-white"
            >
        </div>

        <div class="md:col-span-2">

            <label class="mb-2 block text-sm font-bold text-zinc-300">
                Observaciones
            </label>

            <textarea
                wire:model="observaciones"
                rows="4"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-800 p-3 text-white"
            ></textarea>

        </div>

    </div>

    <div class="mt-6 flex gap-3">

        <button
            wire:click="asignarRutina"
            class="rounded-xl bg-green-600 px-5 py-3 font-bold text-white hover:bg-green-500"
        >
            Guardar rutina
        </button>

        <button
            wire:click="cancelarAsignacion"
            class="rounded-xl bg-zinc-700 px-5 py-3 font-bold text-white"
        >
            Cancelar
        </button>

    </div>

</div>

@endif

            </section>

        @endif

        {{-- HISTORIAL --}}
        <section class="rounded-2xl border border-zinc-800 bg-zinc-900">

            <div class="border-b border-zinc-800 px-5 py-5 sm:px-6">

                <h2 class="text-xl font-bold text-white">
                    Historial de rutinas
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Rutinas anteriores asignadas al socio.
                </p>

            </div>

            @if($historialAsignaciones->isNotEmpty())

                <div class="divide-y divide-zinc-800">

                    @foreach($historialAsignaciones as $asignacion)

                        <div
                            wire:key="historial-rutina-{{ $asignacion->id }}"
                            class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6"
                        >

                            <div>

                                <h3 class="font-bold text-white">
                                    {{ $asignacion->rutina->nombre }}
                                </h3>

                                @if($asignacion->rutina->objetivo)
                                    <p class="mt-1 text-sm text-zinc-400">
                                        {{ $asignacion->rutina->objetivo }}
                                    </p>
                                @endif

                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm">

                                <span class="rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1 font-semibold text-zinc-300">
                                    Inicio:
                                    {{ $asignacion->fecha_inicio
                                        ? \Carbon\Carbon::parse($asignacion->fecha_inicio)->format('d/m/Y')
                                        : 'Sin fecha'
                                    }}
                                </span>

                                <span class="rounded-full border border-red-900 bg-red-950/30 px-3 py-1 font-semibold text-red-300">
                                    Finalizada:
                                    {{ $asignacion->fecha_fin
                                        ? \Carbon\Carbon::parse($asignacion->fecha_fin)->format('d/m/Y')
                                        : 'Sin fecha'
                                    }}
                                </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="px-6 py-10 text-center">

                    <p class="text-sm font-medium text-zinc-500">
                        Todavía no hay rutinas anteriores registradas.
                    </p>

                </div>

            @endif

        </section>

    </div>

</div>