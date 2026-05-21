<x-layouts::app.sidebar title="Mi QR">
    <div class="max-w-xl mx-auto p-6">

        <div class="rounded-3xl border border-stone-300 bg-stone-100 shadow-sm p-6 text-center">

            <h1 class="text-2xl font-bold text-stone-900 mb-2">
                Mi QR de ingreso
            </h1>

            <p class="text-stone-600 mb-6">
                Mostrá este código en recepción para registrar tu ingreso o egreso.
            </p>

            <div class="flex justify-center mb-6 bg-white p-4 rounded-2xl">
                {!! $qrSvg !!}
            </div>

            <div class="space-y-1">
                <p class="text-lg font-semibold text-stone-800">
                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                </p>

                <p class="text-sm text-stone-500">
                    Socio activo
                </p>
            </div>

        </div>

    </div>
</x-layouts::app.sidebar>
