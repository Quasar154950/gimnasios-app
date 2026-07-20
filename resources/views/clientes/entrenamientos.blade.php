<x-layouts::app :title="'Historial de entrenamientos'">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="rounded-xl border border-stone-300 p-6 bg-stone-200 shadow-sm">

            <h1 class="text-2xl font-bold text-stone-900">
                🏋️ Historial de entrenamientos
            </h1>

            <p class="mt-2 text-sm text-stone-600">
                Socio:
                <span class="font-bold">
                    {{ $cliente->nombre }}
                </span>
            </p>

        </div>

        {{-- TABLA --}}
        <div class="rounded-xl border border-stone-300 bg-stone-200 shadow-sm overflow-x-auto">

            @if($asistencias->isEmpty())

                <div class="p-10 text-center text-stone-500 italic">
                    Este socio todavía no registra entrenamientos.
                </div>

            @else

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="border-b border-stone-300 text-stone-500 text-xs uppercase">

                            <th class="p-4 text-left">Fecha</th>

                            <th class="p-4 text-left">Ingreso</th>

                            <th class="p-4 text-left">Salida</th>

                            <th class="p-4 text-left">Estado</th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-stone-300">

                        @foreach($asistencias as $asistencia)

                            <tr class="hover:bg-stone-100 transition">

                                <td class="p-4 text-sm text-stone-700">
                                    {{ $asistencia->created_at->format('d/m/Y') }}
                                </td>

                                <td class="p-4 text-sm text-stone-700">
                                    {{ \Carbon\Carbon::parse($asistencia->hora_ingreso)->format('H:i') }}
                                </td>

                                <td class="p-4 text-sm text-stone-700">

                                    @if($asistencia->hora_salida)
                                        {{ \Carbon\Carbon::parse($asistencia->hora_salida)->format('H:i') }}
                                    @else
                                        —
                                    @endif

                                </td>

                                <td class="p-4">

                                    @if($asistencia->hora_salida)

                                        <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold">
                                            Finalizado
                                        </span>

                                    @else

                                        <span class="px-2 py-1 rounded bg-orange-100 text-orange-700 text-xs font-semibold">
                                            Entrenando
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @endif

        </div>

    </div>

</x-layouts::app>
