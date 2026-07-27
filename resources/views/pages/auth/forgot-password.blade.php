<x-layouts::auth :title="'Recuperar contraseña'">
    @php
    $slug = session('slug_estudio', 'sportgym');

    $logoRelativo = "images/logo-{$slug}.png";

    $logo = file_exists(public_path($logoRelativo))
        ? $logoRelativo
        : 'images/logo-sportgym.png';
@endphp
    <div class="flex flex-col gap-6">
        <div class="flex justify-center">
    <img
        src="{{ asset($logo) }}"
        alt="Logo"
        style="height:100px;width:auto;"
    >
</div>
        <x-auth-header
            :title="'Recuperar contraseña'"
            :description="'Ingresá tu email para recibir un enlace de recuperación.'"
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                label="Email"
                type="email"
                required
                autofocus
                placeholder="email@ejemplo.com"
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                Enviar enlace de recuperación
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>O volver a</span>
            <flux:link :href="route('login')" wire:navigate>iniciar sesión</flux:link>
        </div>
    </div>
</x-layouts::auth>
