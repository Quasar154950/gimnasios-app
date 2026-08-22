<x-layouts::app :title="'Historial de pagos'">

    <div class="space-y-6">

        {{-- ENCABEZADO --}}
        <div class="rounded-xl border border-stone-300 bg-stone-200 p-6 shadow-sm">

            <h1 class="text-2xl font-bold text-stone-900">
                💰 Historial de pagos
            </h1>

            <p class="mt-2 text-sm text-stone-600">
                Socio:
                <span class="font-bold">
                    {{ $cliente->nombre }}
                </span>
            </p>

        </div>

        {{-- TABLA --}}
        <div class="overflow-x-auto rounded-xl border border-stone-300 bg-stone-200 shadow-sm">

            @if($pagos->isEmpty())

                <div class="p-10 text-center italic text-stone-500">
                    No hay pagos registrados todavía.
                </div>

            @else

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="border-b border-stone-300 text-xs uppercase text-stone-500">

                            <th class="p-4 text-left">
                                Comprobante
                            </th>

                            <th class="p-4 text-left">
                                Fecha de pago
                            </th>

                            <th class="p-4 text-left">
                                Fecha base
                            </th>

                            <th class="p-4 text-left">
                                Nuevo vencimiento
                            </th>

                            <th class="p-4 text-left">
                                Monto
                            </th>

                            <th class="p-4 text-left">
                                Método
                            </th>

                            <th class="p-4 text-left">
                                Origen
                            </th>

                            <th class="p-4 text-left">
                                Estado
                            </th>

                            <th class="p-4 text-left">
                                Observación
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-stone-300">

                        @foreach($pagos as $pago)

                            <tr class="transition hover:bg-stone-100">

                                {{-- COMPROBANTE --}}
                                <td class="whitespace-nowrap p-4 text-sm">

                                    @if($pago->numero_comprobante)

                                        <span
                                            class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1
                                                   text-xs font-black text-orange-700"
                                        >
                                            N.º {{ str_pad($pago->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center rounded-full bg-stone-300 px-2 py-1
                                                   text-[10px] font-bold text-stone-600"
                                            title="Pago registrado antes de incorporar comprobantes numerados"
                                        >
                                            Pago anterior
                                        </span>

                                    @endif

                                </td>

                                {{-- FECHA DE PAGO --}}
                                <td class="whitespace-nowrap p-4 text-sm text-stone-700">

                                    @if($pago->fecha_pago)

                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}

                                    @else

                                        <span class="text-stone-400">
                                            Sin fecha
                                        </span>

                                    @endif

                                </td>

                                {{-- FECHA BASE --}}
                                <td class="whitespace-nowrap p-4 text-sm text-stone-700">

                                    @if($pago->fecha_base)

                                        <span class="font-bold text-blue-700">
                                            {{ \Carbon\Carbon::parse($pago->fecha_base)->format('d/m/Y') }}
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center rounded-full bg-stone-300 px-2 py-1 text-[10px] font-bold text-stone-600"
                                            title="Pago registrado antes de incorporar la fecha base"
                                        >
                                            Pago anterior
                                        </span>

                                    @endif

                                </td>

                                {{-- NUEVO VENCIMIENTO --}}
                                <td class="whitespace-nowrap p-4 text-sm text-stone-700">

                                    @if($pago->vencimiento_cuota)

                                        <span class="font-bold text-purple-700">
                                            {{ \Carbon\Carbon::parse($pago->vencimiento_cuota)->format('d/m/Y') }}
                                        </span>

                                    @else

                                        <span class="text-stone-400">
                                            Sin vencimiento
                                        </span>

                                    @endif

                                </td>

                                {{-- MONTO --}}
                                <td class="whitespace-nowrap p-4 text-sm font-bold text-green-600">
                                    ${{ number_format($pago->monto ?? 0, 0, ',', '.') }}
                                </td>

                                {{-- MÉTODO --}}
                                <td class="p-4 text-sm text-stone-700">

                                    @if($pago->metodo_pago)

                                        <span
                                            class="inline-flex items-center rounded-full bg-stone-100 px-2 py-1 text-xs font-bold text-stone-700"
                                        >
                                            {{ $pago->metodo_pago }}
                                        </span>

                                    @else

                                        <span class="text-stone-400">
                                            Sin especificar
                                        </span>

                                    @endif

                                </td>

                                {{-- ORIGEN --}}
                                <td class="p-4 text-sm text-stone-700">

                                    @if($pago->origen === 'mercadopago')

                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-bold text-blue-700"
                                            title="Pago realizado automáticamente desde la aplicación"
                                        >
                                            Automático
                                        </span>

                                    @elseif($pago->origen === 'manual')

                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2 py-1 text-xs font-bold text-amber-700"
                                            title="Pago registrado manualmente por el gimnasio"
                                        >
                                            Manual
                                        </span>

                                    @else

                                        <span class="text-stone-400">
                                            Sin especificar
                                        </span>

                                    @endif

                                </td>

                                {{-- ESTADO --}}
                                <td class="p-4 text-sm text-stone-700">

                                    @if($pago->estado === 'aprobado')

                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-bold text-green-700"
                                        >
                                            Aprobado
                                        </span>

                                    @elseif($pago->estado === 'pendiente')

                                        <span
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-bold text-yellow-700"
                                        >
                                            Pendiente
                                        </span>

                                    @elseif($pago->estado === 'rechazado')

                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-bold text-red-700"
                                        >
                                            Rechazado
                                        </span>

                                    @else

                                        <span class="text-stone-400">
                                            Sin especificar
                                        </span>

                                    @endif

                                </td>

                                {{-- OBSERVACIÓN --}}
                                <td class="min-w-[220px] p-4 text-sm text-stone-700">

                                    @if($pago->observacion)

                                        {{ $pago->observacion }}

                                    @else

                                        <span class="italic text-stone-400">
                                            Sin observación
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