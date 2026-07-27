<x-layouts::auth :title="__('Acceso al sistema')">
    <div class="flex flex-col gap-6">

        {{-- LOGO GRANDE --}}
        <div class="flex justify-center">
            <img
                src="{{ asset('images/logo-sportgym.png') }}"
                alt="SportGym"
                style="height: 100px; width: auto;"
            >
        </div>

        {{-- TITULO --}}
        <x-auth-header
            :title="__('Acceso al sistema')"
            :description="__('Ingresá con tu email y contraseña para continuar')"
        />

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('login.store') }}"
            class="flex flex-col gap-6"
        >
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
                    <flux:link
                        class="absolute top-0 text-sm end-0"
                        :href="route('password.request')"
                        wire:navigate
                    >
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox
                name="remember"
                :label="__('Recordarme')"
                :checked="old('remember')"
            />

            <div class="flex items-center justify-end">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full"
                >
                    {{ __('Ingresar') }}
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts::auth>
