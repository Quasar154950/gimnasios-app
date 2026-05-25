<x-layouts::app :title="'Panel de Soporte'">

    <div class="space-y-5 text-left">

        <div class="rounded-xl border border-neutral-200 p-5 bg-white shadow-sm">
            <h1 class="text-2xl font-bold italic">Panel de Soporte</h1>
            <p class="text-sm text-neutral-600 mt-2">
                Administración SaaS de Fernando García
            </p>
        </div>

        {{-- 🔑 MENSAJE PASSWORD GENERADA --}}
@if(session('password_generada'))
    <div class="p-3 rounded bg-blue-100 text-blue-800 text-sm font-bold">
        Nueva contraseña: {{ session('password_generada') }}
    </div>
@endif

{{-- SUCCESS POR SESSION --}}
@if(session('success'))
    <div class="p-3 rounded bg-green-100 text-green-800 text-sm font-bold">
        {{ session('success') }}
    </div>
@endif

{{-- ERROR POR SESSION --}}
@if(session('error'))
    <div class="p-3 rounded bg-red-100 text-red-800 text-sm font-bold">
        {{ session('error') }}
    </div>
@endif

{{-- SUCCESS POR QUERY STRING --}}
@if(request('success'))
    <div class="p-3 rounded bg-green-100 text-green-800 text-sm font-bold">
        {{ request('success') }}
    </div>
@endif

{{-- ERROR POR QUERY STRING --}}
@if(request('error'))
    <div class="p-3 rounded bg-red-100 text-red-800 text-sm font-bold">
        {{ request('error') }}
    </div>
@endif

        {{-- MÉTRICAS --}}
        @php
            $users = \App\Models\User::where('role', 'abogado')
                ->where('email', '!=', 'soporte@tuempresa.com')
                ->orderBy('email')
                ->get();

            $totalEstudios = $users->count();
            $activos = $users->where('activo', true)->count();
            $inactivos = $users->where('activo', false)->count();

            $vencidos = $users->filter(fn ($u) => $u->fecha_vencimiento && now()->greaterThan($u->fecha_vencimiento))->count();

            $porVencer = $users->filter(function ($u) {
                if (!$u->fecha_vencimiento) return false;
                $dias = max(0, now()->startOfDay()->diffInDays($u->fecha_vencimiento->startOfDay(), false));
                return $dias > 0 && $dias <= 7;
            })->count();

            $abogados = $users->where('tipo_app', 'abogados')->count();
            $gimnasios = $users->where('tipo_app', 'gimnasios')->count();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-7 gap-3">

            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-center">
                <div class="text-xs text-blue-600">Total</div>
                <div class="text-xl font-bold text-blue-800">{{ $totalEstudios }}</div>
            </div>

            <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-center">
                <div class="text-xs text-green-600">Activos</div>
                <div class="text-xl font-bold text-green-800">{{ $activos }}</div>
            </div>

            <div class="p-3 rounded-xl bg-gray-100 border border-gray-300 text-center">
                <div class="text-xs text-gray-600">Inactivos</div>
                <div class="text-xl font-bold text-gray-800">{{ $inactivos }}</div>
            </div>

            <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-center">
                <div class="text-xs text-red-600">Vencidos</div>
                <div class="text-xl font-bold text-red-800">{{ $vencidos }}</div>
            </div>

            <div class="p-3 rounded-xl bg-yellow-50 border border-yellow-200 text-center">
                <div class="text-xs text-yellow-600">Por vencer</div>
                <div class="text-xl font-bold text-yellow-800">{{ $porVencer }}</div>
            </div>

            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200 text-center">
                <div class="text-xs text-indigo-600">Abogados</div>
                <div class="text-xl font-bold text-indigo-800">{{ $abogados }}</div>
            </div>

            <div class="p-3 rounded-xl bg-orange-50 border border-orange-200 text-center">
                <div class="text-xs text-orange-600">Gimnasios</div>
                <div class="text-xl font-bold text-orange-800">{{ $gimnasios }}</div>
            </div>

        </div>

        {{-- LISTADO --}}
        <div class="rounded-xl border border-neutral-200 p-5 bg-white shadow-sm">
            <h2 class="text-lg font-bold mb-4">Clientes SaaS</h2>

            <div class="space-y-3">
                @foreach($users as $user)
                    @php
                        $vencido = $user->fecha_vencimiento && now()->greaterThan($user->fecha_vencimiento);

                        $diasRestantes = $user->fecha_vencimiento
                            ? max(0, now()->startOfDay()->diffInDays($user->fecha_vencimiento->startOfDay(), false))
                            : null;

                        if (!$user->activo) {
                            $estado = 'Inactivo';
                        } elseif ($vencido) {
                            $estado = 'Vencido';
                        } else {
                            $estado = 'Vigente';
                        }

                        if (is_null($diasRestantes)) {
                            $color = '#6b7280';
                        } elseif ($diasRestantes > 10) {
                            $color = '#16a34a';
                        } elseif ($diasRestantes > 3) {
                            $color = '#ca8a04';
                        } else {
                            $color = '#dc2626';
                        }
                    @endphp

                    <div class="p-4 border rounded-xl space-y-3 bg-white">

                        {{-- INFO --}}
                        <div>

                            <div class="flex items-center gap-2 flex-wrap">

                                <div class="font-semibold">
                                    {{ $user->email }}
                                </div>

                                @if($user->tipo_app === 'abogados')
                                    <span class="text-[10px] px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-bold">
                                        ⚖️ ABOGADOS
                                    </span>
                                @endif

                                @if($user->tipo_app === 'gimnasios')
                                    <span class="text-[10px] px-2 py-1 rounded-full bg-orange-100 text-orange-700 font-bold">
                                        🏋️ GIMNASIOS
                                    </span>
                                @endif

                            </div>

                            <div class="text-xs text-gray-500">
    Vence:
    {{ $user->fecha_vencimiento ? $user->fecha_vencimiento->format('d/m/Y') : 'Sin fecha' }}
</div>

<div class="flex items-center gap-2 flex-wrap mt-2">

    <span class="text-[10px] px-2 py-1 rounded-full bg-zinc-100 text-zinc-700 font-bold">
        📦 {{ strtoupper($user->plan) }}
    </span>

    <span class="text-[10px] px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold">
        💰 ${{ number_format($user->precio_suscripcion, 0, ',', '.') }}
    </span>

</div>
                            <div class="text-xs font-bold" style="color: {{ $color }}">
                                Días restantes:
                                {{ is_null($diasRestantes) ? 'Sin fecha' : $diasRestantes }}
                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="text-sm font-bold">
                            {{ $estado }}
                        </div>

                        {{-- BOTONES --}}
                        <div class="flex gap-2 overflow-x-auto whitespace-nowrap pb-1 -mx-1 px-1">

                            {{-- RENOVAR --}}
                            <form method="POST" action="{{ route('renovar.suscripcion', $user) }}" class="shrink-0">
                                @csrf
                                <button onclick="return confirm('¿Seguro querés renovar 30 días?')"
                                    class="text-sm px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white cursor-pointer transition">
                                    🔄 Renovar
                                </button>
                            </form>

                            {{-- ACTIVAR / SUSPENDER --}}
                            <form method="POST" action="{{ route('toggle.activo', $user) }}" class="shrink-0">
                                @csrf
                                <button onclick="return confirm('¿Seguro querés cambiar el estado?')"
                                    class="text-sm px-4 py-2 rounded {{ $user->activo ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white cursor-pointer transition">
                                    {{ $user->activo ? '⛔ Suspender' : '✅ Activar' }}
                                </button>
                            </form>

                            {{-- RESET --}}
                            <form method="POST" action="{{ route('soporte.reset.password', $user) }}" class="shrink-0">
                                @csrf
                                <button onclick="return confirm('¿Resetear contraseña de este usuario?')"
                                    class="text-sm px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white cursor-pointer transition">
                                    🔑 Reset
                                </button>
                            </form>

                            {{-- EDITAR VENCIMIENTO --}}
                            <a href="{{ route('soporte.editar.vencimiento', $user) }}"
                               class="shrink-0 text-sm px-4 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white transition">
                               ✏️ Editar vencimiento
                            </a>

                            {{-- COPIAR ACCESO --}}
                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ url('/estudio/' . $user->slug_estudio) }}')"
                                class="shrink-0 text-sm px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer transition">
                                📩 Copiar acceso
                            </button>

                            {{-- VER COMO USUARIO --}}
                            <form method="POST" action="{{ route('soporte.ver-como', $user) }}" class="shrink-0">
                                @csrf

                                <button
                                    type="submit"
                                    onclick="return confirm('¿Entrar como este usuario?')"
                                    class="text-sm px-4 py-2 rounded bg-violet-600 hover:bg-violet-700 text-white cursor-pointer transition">
                                    👁 Ver usuario
                                </button>
                            </form>
                            
                            {{-- COBRAR SAAS --}}
                            <form method="POST" action="{{ route('soporte.saas.pagar', $user) }}" class="shrink-0">
                            @csrf

                           <button
                          type="submit"
                         onclick="return confirm('¿Generar pago SaaS para este cliente?')"
                          class="text-sm px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer transition">
                              💳 Cobrar SaaS
                             </button>
                           </form>
                           
                           @php
    $ultimoPago = $user->saasPagos()->latest()->first();
@endphp

@if($ultimoPago && $ultimoPago->checkout_url)

    <a href="{{ $ultimoPago->checkout_url }}"
       target="_blank"
       class="shrink-0 text-sm px-4 py-2 rounded bg-lime-600 hover:bg-lime-700 text-white transition">
        🔗 Abrir link pago
    </a>

    <button
        type="button"
        onclick="navigator.clipboard.writeText('{{ $ultimoPago->checkout_url }}')"
        class="shrink-0 text-sm px-4 py-2 rounded bg-zinc-700 hover:bg-zinc-800 text-white transition">
        📋 Copiar link
    </button>

@endif

                            {{-- BACKUP --}}
                            <form method="POST" action="{{ route('soporte.backup') }}" class="shrink-0">
                                @csrf

                                <button
                                    type="submit"
                                    onclick="return confirm('¿Generar backup del sistema?')"
                                    class="text-sm px-4 py-2 rounded bg-cyan-600 hover:bg-cyan-700 text-white cursor-pointer transition">
                                    💾 Backup
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>
        </div>

    </div>

</x-layouts::app>


