<x-layouts::app>

    <div class="max-w-3xl mx-auto p-4 space-y-6">

        <h1 class="text-2xl font-bold" style="color: #111827 !important;">
            Mi Suscripción
        </h1>

        <div class="bg-white dark:bg-neutral-900 rounded-xl shadow p-6 space-y-4">

            {{-- ESTADO --}}
            <div>
                <p class="text-sm" style="color: #6B7280 !important;">
                    Estado
                </p>

                @if($user->activo)
                    <span class="text-green-600 font-semibold">
                        Activa
                    </span>
                @else
                    <span class="text-red-600 font-semibold">
                        Suspendida
                    </span>
                @endif
            </div>

            {{-- FECHA --}}
            <div>
                <p class="text-sm" style="color: #6B7280 !important;">
                    Fecha de vencimiento
                </p>

                <p class="font-semibold"
                   style="color: #111827 !important;">
                    {{ $user->fecha_vencimiento
                        ? \Carbon\Carbon::parse($user->fecha_vencimiento)->format('d/m/Y')
                        : 'Sin fecha' }}
                </p>
            </div>

            {{-- PLAN --}}
            <div>
                <p class="text-sm" style="color: #6B7280 !important;">
                    Plan
                </p>

                <p class="font-semibold"
                   style="color: #111827 !important;">
                    {{ strtoupper($user->plan ?? 'Sin plan') }}
                </p>
            </div>

            {{-- PRECIO --}}
            <div>
                <p class="text-sm" style="color: #6B7280 !important;">
                    Precio mensual
                </p>

                <p class="font-semibold"
                   style="color: #111827 !important;">
                    ${{ number_format($user->precio_suscripcion ?? 0, 0, ',', '.') }}
                </p>
            </div>

            {{-- DÍAS RESTANTES --}}
            <div>
                <p class="text-sm" style="color: #6B7280 !important;">
                    Días restantes
                </p>

                @php
                    if ($user->fecha_vencimiento) {
                        $dias = \Carbon\Carbon::now()->startOfDay()
                            ->diffInDays(
                                \Carbon\Carbon::parse($user->fecha_vencimiento)->startOfDay(),
                                false
                            );
                    } else {
                        $dias = null;
                    }
                @endphp

                @if($dias === null)
                    <p class="font-semibold"
                       style="color: #111827 !important;">
                        —
                    </p>

                @elseif($dias < 0)
                    <p class="font-semibold text-red-600">
                        Suscripción vencida
                    </p>

                @elseif($dias <= 5)
                    <p class="font-semibold text-yellow-600">
                        Te quedan {{ $dias }} días
                    </p>

                @else
                    <p class="font-semibold text-green-600">
                        Te quedan {{ $dias }} días
                    </p>
                @endif
            </div>

            {{-- PAGAR SUSCRIPCIÓN --}}
            @if($user->precio_suscripcion > 0)

                <div class="pt-4">

                    <form method="POST" action="{{ route('suscripcion.pagar') }}">
                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition"
                        >
                            💳 Pagar con Mercado Pago
                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>

</x-layouts::app>