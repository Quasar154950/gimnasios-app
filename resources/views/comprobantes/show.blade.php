<x-layouts::app :title="'Comprobante de pago'">

    <div class="-m-4 min-h-screen bg-slate-950 p-4 sm:-m-6 sm:p-6">

        <div class="mx-auto max-w-3xl space-y-5">

            {{-- ENCABEZADO --}}
            <div
                class="rounded-xl border border-stone-600 bg-stone-800 p-6 shadow-lg"
            >
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-orange-500">
                            Comprobante de pago
                        </p>

                        <h1 class="mt-1 text-2xl font-black text-stone-100">
                            N.º {{ str_pad($pago->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
                        </h1>
                    </div>

                    <div
                        class="inline-flex w-fit items-center rounded-full border border-green-500/30
                               bg-green-500/15 px-4 py-2 text-xs font-black text-green-400"
                    >
                        ✓ Pago aprobado
                    </div>

                </div>
            </div>


            {{-- COMPROBANTE --}}
            <div
                class="overflow-hidden rounded-xl border border-stone-300 bg-stone-100 shadow-xl"
            >

                {{-- CABECERA DEL RECIBO --}}
                <div class="border-b border-stone-300 bg-stone-200 p-6 text-center">

                    <h2 class="text-2xl font-black text-stone-900">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="mt-1 text-sm font-bold text-stone-500">
                        Comprobante interno de pago
                    </p>

                </div>


                {{-- DATOS --}}
                <div class="space-y-5 p-6 sm:p-8">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        {{-- SOCIO --}}
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-wide text-stone-500">
                                Socio
                            </p>

                            <p class="mt-1 text-base font-black text-stone-900">
                                {{ $pago->cliente->nombre }}
                            </p>
                        </div>


                        {{-- FECHA --}}
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-wide text-stone-500">
                                Fecha de pago
                            </p>

                            <p class="mt-1 text-base font-bold text-stone-800">
                                {{ $pago->fecha_pago?->format('d/m/Y') ?? 'Sin fecha' }}
                            </p>
                        </div>


                        {{-- MÉTODO --}}
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-wide text-stone-500">
                                Método de pago
                            </p>

                            <p class="mt-1 text-base font-bold text-stone-800">
                                {{ $pago->metodo_pago ?: 'Sin especificar' }}
                            </p>
                        </div>


                        {{-- ORIGEN --}}
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-wide text-stone-500">
                                Origen
                            </p>

                            <p class="mt-1 text-base font-bold text-stone-800">
                                {{ $pago->origen === 'mercadopago' ? 'Mercado Pago' : 'Registrado por el gimnasio' }}
                            </p>
                        </div>

                    </div>


                    {{-- IMPORTE --}}
                    <div
                        class="rounded-xl border border-green-200 bg-green-50 p-5 text-center"
                    >
                        <p class="text-[11px] font-black uppercase tracking-widest text-green-700">
                            Importe abonado
                        </p>

                        <p class="mt-1 text-3xl font-black text-green-700">
                            $ {{ number_format((float) $pago->monto, 2, ',', '.') }}
                        </p>
                    </div>


                    {{-- CONCEPTO --}}
                    <div class="border-t border-stone-300 pt-5">

                        <p class="text-[11px] font-black uppercase tracking-wide text-stone-500">
                            Concepto
                        </p>

                        <p class="mt-1 text-sm font-bold text-stone-800">
                            {{ $pago->observacion ?: 'Renovación de cuota mensual' }}
                        </p>

                    </div>


                    {{-- VENCIMIENTO --}}
                    <div class="border-t border-stone-300 pt-5">

                        <p class="text-[11px] font-black uppercase tracking-wide text-stone-500">
                            Cuota vigente hasta
                        </p>

                        <p class="mt-1 text-lg font-black text-purple-700">
                            {{ $pago->vencimiento_cuota?->format('d/m/Y') ?? 'Sin vencimiento' }}
                        </p>

                    </div>

                </div>


                {{-- PIE --}}
                <div class="border-t border-stone-300 bg-stone-200 px-6 py-4 text-center">

                    <p class="text-xs font-bold text-stone-500">
                        Comprobante N.º
                        {{ str_pad($pago->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
                    </p>

                    <p class="mt-1 text-[10px] text-stone-400">
                        Comprobante interno emitido por el gimnasio.
                    </p>

                </div>

            </div>


            {{-- ACCIONES --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

    <a
        href="{{ route('clientes.pagos', $pago->cliente_id) }}"
        class="inline-flex items-center justify-center rounded-xl bg-stone-700 px-5 py-3
               text-sm font-black text-white shadow-md transition
               hover:-translate-y-0.5 hover:bg-stone-600 hover:shadow-xl"
    >
        ← Volver al historial
    </a>

    <a
        href="{{ route('comprobantes.descargar', $pago) }}"
        class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3
               text-sm font-black text-white shadow-md transition
               hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-xl"
    >
        ⬇ Descargar PDF
    </a>

</div>

        </div>

    </div>

</x-layouts::app>