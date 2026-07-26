<div class="min-h-screen bg-zinc-950 px-4 py-6 text-white sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- ENCABEZADO --}}
        <div class="mb-8">

            <a
                href="{{ route('rutinas.index') }}"
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

                Volver a rutinas
            </a>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

                <div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-green-400">
                        Constructor de rutina
                    </p>

                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ $rutina->nombre }}
                    </h1>

                    @if ($rutina->objetivo)
                        <p class="mt-2 text-base font-medium text-green-400">
                            {{ $rutina->objetivo }}
                        </p>
                    @endif

                    @if ($rutina->descripcion)
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-400">
                            {{ $rutina->descripcion }}
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">

                    <div class="rounded-xl border border-zinc-800 bg-zinc-900 px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-zinc-500">
                            Duración
                        </p>

                        <p class="mt-1 font-bold text-white">
                            {{ $rutina->duracion_semanas
                                ? $rutina->duracion_semanas . ' semanas'
                                : 'Sin definir'
                            }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-zinc-800 bg-zinc-900 px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-zinc-500">
                            Estado
                        </p>

                        <p class="mt-1 font-bold {{ $rutina->activa ? 'text-green-400' : 'text-zinc-400' }}">
                            {{ $rutina->activa ? 'Activa' : 'Inactiva' }}
                        </p>
                    </div>
                
                    <a
                        href="{{ route('rutinas.asignar', $rutina) }}"
                        wire:navigate
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-500 active:scale-95"
                    >
                       <svg
                           class="h-4 w-4"
                           viewBox="0 0 24 24"
                           fill="none"
                           stroke="currentColor"
                           stroke-width="2"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                    >
                           <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                           <circle cx="9" cy="7" r="4"/>
                           <path d="M19 8v6"/>
                           <path d="M22 11h-6"/>
                       </svg>

                       Asignar a Socios
                    </a>

                    <a
                        href="{{ route('rutinas.edit', $rutina) }}"
                        wire:navigate
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-700 active:scale-95"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                        </svg>

                        Editar datos
                    </a>

                </div>

            </div>

        </div>

        {{-- INDICACIÓN --}}
        <div class="mb-6 rounded-2xl border border-green-900/60 bg-green-950/30 px-5 py-4">

            <div class="flex items-start gap-4">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-950 text-green-400">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 11v5"/>
                        <path d="M12 8h.01"/>
                    </svg>
                </div>

                <div>
                    <h2 class="font-bold text-green-300">
                        Organizá la semana de entrenamiento
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-green-100/70">
                        Cada día podrá contener sus propios ejercicios, series,
                        repeticiones, descansos y observaciones.
                    </p>
                </div>

            </div>

        </div>

        {{-- DÍAS --}}
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

    @forelse ($rutina->dias as $dia)

        <article
            wire:key="rutina-dia-{{ $dia->id }}"
            class="group flex min-h-[230px] flex-col overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-xl transition hover:-translate-y-1 hover:border-green-800 hover:shadow-2xl"
        >

            <div class="flex flex-1 flex-col p-6">

                <div class="mb-5 flex items-start justify-between gap-4">

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-950 text-xl font-black text-green-400">
                        {{ $dia->orden }}
                    </div>

                    @if ($dia->ejercicios_count > 0)
                        <span class="rounded-full border border-green-800 bg-green-950 px-3 py-1 text-xs font-bold text-green-300">
                            {{ $dia->ejercicios_count }}

                            {{ $dia->ejercicios_count === 1
                                ? 'ejercicio'
                                : 'ejercicios'
                            }}
                        </span>
                    @else
                        <span class="rounded-full border border-zinc-700 bg-zinc-800 px-3 py-1 text-xs font-bold text-zinc-400">
                            Sin ejercicios
                        </span>
                    @endif

                </div>

                <h2 class="text-2xl font-bold text-white">
                    {{ $dia->nombre }}
                </h2>

                <p class="mt-2 text-sm leading-6 text-zinc-400">
                    {{ $dia->descripcion ?: 'Día de entrenamiento' }}
                </p>

                <div class="mt-auto pt-6">

                    <a
                        href="{{ route('rutinas.dias.show', [
                            'rutina' => $rutina,
                            'dia' => $dia,
                        ]) }}"
                        wire:navigate
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-green-500 active:scale-95"
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
                            <path d="M12 5v14M5 12h14"/>
                        </svg>

                        Administrar ejercicios
                    </a>

                </div>

            </div>

        </article>

    @empty

        <div class="col-span-full rounded-2xl border border-dashed border-zinc-700 bg-zinc-900/60 px-6 py-14 text-center">
            <h2 class="text-xl font-bold text-white">
                Esta rutina todavía no tiene días
            </h2>

            <p class="mt-2 text-sm text-zinc-400">
                Los días se crean automáticamente al registrar una rutina nueva.
            </p>
        </div>

    @endforelse

</div>
    </div>

</div>