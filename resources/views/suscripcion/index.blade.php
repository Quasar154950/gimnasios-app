<x-layouts::app>

    <div class="max-w-3xl mx-auto p-4 space-y-6">

        <h1 class="text-2xl font-bold">
            Mi Suscripción
        </h1>

        <div class="bg-white dark:bg-neutral-900 rounded-xl shadow p-6 space-y-4">

            {{-- ESTADO --}}
            <div>
                <p class="text-sm text-gray-500">Estado</p>

                @if($user->activo)
                    <span class="text-green-600 font-semibold">Activa</span>
                @else
                    <span class="text-red-600 font-semibold">Suspendida</span>
                @endif
            </div>

            {{-- FECHA --}}
            <div>
                <p class="text-sm text-gray-500">Fecha de vencimiento</p>

                <p class="font-semibold">
                    {{ $user->fecha_vencimiento 
                        ? \Carbon\Carbon::parse($user->fecha_vencimiento)->format('d/m/Y') 
                        : 'Sin fecha' }}
                </p>
            </div>

            {{-- DÍAS RESTANTES --}}
            
{{-- DÍAS RESTANTES --}}
<div>
    <p class="text-sm text-gray-500">Días restantes</p>

    @php
        if ($user->fecha_vencimiento) {
            $dias = \Carbon\Carbon::now()->startOfDay()
                ->diffInDays(\Carbon\Carbon::parse($user->fecha_vencimiento)->startOfDay(), false);
        } else {
            $dias = null;
        }
    @endphp

    @if($dias === null)
        <p class="font-semibold">—</p>

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

            </div>

    </div>

</x-layouts::app>