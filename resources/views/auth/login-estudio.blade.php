<x-layouts::auth :title="__('Acceso al sistema')">

    @php
    /*
    |--------------------------------------------------------------------------
    | MARCA DINÁMICA DEL GIMNASIO
    |--------------------------------------------------------------------------
    */

    $slug = strtolower($userEstudio->slug_estudio ?? 'sportgym');

    // Solo permite letras, números, guiones y guiones bajos.
    $slugSeguro = preg_replace('/[^a-z0-9_-]/', '', $slug);

    $nombreGimnasio = $userEstudio->nombre_estudio ?? 'SportGym';

    /*
    |--------------------------------------------------------------------------
    | ARCHIVOS DE MARCA
    |--------------------------------------------------------------------------
    |
    | Convención:
    | logo-{slug}.png
    | splash-{slug}.png
    |
    */

    $logoRelativo = "images/logo-{$slugSeguro}.png";
    $splashRelativo = "images/splash-{$slugSeguro}.png";

    /*
    |--------------------------------------------------------------------------
    | RESPALDO
    |--------------------------------------------------------------------------
    */

    $logoLogin = file_exists(public_path($logoRelativo))
        ? $logoRelativo
        : 'images/logo-sportgym.png';

    $splashImage = file_exists(public_path($splashRelativo))
        ? $splashRelativo
        : 'images/splash-sportgym.png';

    /*
    |--------------------------------------------------------------------------
    | NOMBRE VISUAL
    |--------------------------------------------------------------------------
    */

    if (str_ends_with(strtolower($nombreGimnasio), 'gym')) {
        $marcaUno = substr($nombreGimnasio, 0, -3);
        $marcaDos = 'Gym';
    } else {
        $marcaUno = $nombreGimnasio;
        $marcaDos = '';
    }
@endphp

    {{-- SPLASH PWA / APP --}}
    <div 
        id="app-splash" 
        class="fixed inset-0 z-[9999] flex items-center justify-center overflow-hidden transition-opacity duration-700"
        style="background: radial-gradient(circle at center, #1e293b 0%, #020617 72%);"
    >
        {{-- Fondo decorativo --}}
        <div class="absolute inset-0 opacity-40"
             style="background: radial-gradient(circle at center, rgba(245,158,11,0.18) 0%, transparent 45%);">
        </div>

        <div class="relative flex flex-col items-center text-center px-6">

            {{-- Logo splash --}}
            <div class="splash-logo-wrap">
                <img
                    src="{{ asset($splashImage) }}"
                    alt="{{ $nombreGimnasio }}"
                    class="w-36 h-36 sm:w-44 sm:h-44 rounded-full object-cover"
                >
            </div>

            {{-- Nombre --}}
            <h1 class="mt-8 text-3xl sm:text-4xl font-black tracking-wide">
              
               <span class="text-stone-100">
                  {{ $marcaUno }}
               </span>

               <span class="text-orange-500">
                  {{ $marcaDos }}
               </span>

        </h1>

<p class="mt-1 text-orange-400 text-sm font-black tracking-[0.35em]">
    TANDIL
</p>

            <p class="mt-2 text-amber-300 tracking-[0.45em] text-sm font-serif">
                FITNESS CLUB
            </p>

            {{-- Texto carga --}}
<p class="mt-8 text-gray-300 text-sm tracking-[0.25em] uppercase">
    Cargando tu mejor versión
</p>

{{-- Barra POWER --}}
<div class="mt-5 w-64 h-4 rounded-full bg-white/10 overflow-hidden border border-orange-500/30 shadow-lg">

    <div class="splash-bar-power h-full"></div>

</div>

{{-- PESA FITNESS --}}
<div class="mt-8 dumbbell-wrap">

    <div class="dumbbell">

        <span></span>
        <span></span>
        <span></span>

    </div>

</div>

</div>
</div>

    {{-- LOGIN --}}
    <div id="login-content" class="flex flex-col gap-6 opacity-0 transition-opacity duration-500">

        {{-- LOGO DEL ESTUDIO --}}
        <div class="flex justify-center">
            <img 
                src="{{ asset($logoLogin) }}" 
                alt="Logo" 
                style="height: 100px; width: auto;"
            >
        </div>

        {{-- TITULO --}}
        <x-auth-header 
            :title="$nombreGimnasio" 
            :description="__('Ingresá con tu email y contraseña para continuar')" 
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="cliente@email.com"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Contraseña')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Ingresá tu contraseña')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Ingresar') }}
                </flux:button>
            </div>
        </form>

        
    </div>

    <style>
        .splash-logo-wrap {
            padding: 10px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow:
                0 0 0 1px rgba(245, 158, 11, 0.35),
                0 25px 80px rgba(0, 0, 0, 0.55),
                0 0 45px rgba(245, 158, 11, 0.25);
            animation: splashLogo 1.4s ease-out both, splashBreath 2.4s ease-in-out infinite;
        }

        .splash-bar-power {
    width: 45%;
    height: 100%;
    border-radius: 9999px;

    background:
        repeating-linear-gradient(
            -45deg,
            #f97316 0px,
            #f97316 10px,
            #fb923c 10px,
            #fb923c 20px
        );

    box-shadow:
        0 0 12px rgba(249, 115, 22, 0.8),
        0 0 24px rgba(249, 115, 22, 0.35);

    animation: splashBar 1.6s linear infinite;
}

.dumbbell-wrap {

    display: flex;
    justify-content: center;
    align-items: center;

    animation: dumbbellFloat 2.4s ease-in-out infinite;
}

.dumbbell {

    display: flex;
    align-items: center;
    gap: 6px;

    filter:
        drop-shadow(0 0 8px rgba(249,115,22,0.8))
        drop-shadow(0 0 20px rgba(249,115,22,0.35));
}

.dumbbell span:nth-child(1),
.dumbbell span:nth-child(3) {

    width: 18px;
    height: 38px;

    border-radius: 6px;

    background:
        linear-gradient(180deg, #fb923c, #ea580c);
}

.dumbbell span:nth-child(2) {

    width: 78px;
    height: 8px;

    border-radius: 9999px;

    background:
        linear-gradient(90deg, #f97316, #fdba74, #f97316);
}

@keyframes dumbbellFloat {

    0%, 100% {
        transform: translateY(0) scale(1);
        opacity: 0.9;
    }

    50% {
        transform: translateY(-6px) scale(1.06);
        opacity: 1;
    }
}

@keyframes splashLogo {
    0% {
        opacity: 0;
        transform: scale(0.72) rotate(-6deg);
    }

    60% {
        opacity: 1;
        transform: scale(1.05) rotate(0deg);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes splashBreath {
    0%, 100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.035);
    }
}

@keyframes splashBar {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(240%);
    }
}
</style>

<script>
    window.addEventListener('load', function () {

        const splash = document.getElementById('app-splash');
        const loginContent = document.getElementById('login-content');

        setTimeout(function () {

            if (splash) {

                splash.classList.add('opacity-0');

                setTimeout(function () {

                    splash.remove();

                    if (loginContent) {
                        loginContent.classList.remove('opacity-0');
                        loginContent.classList.add('opacity-100');
                    }

                }, 700);
            }

        }, 1800);

    });
</script>

</x-layouts::auth>