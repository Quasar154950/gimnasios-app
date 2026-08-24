<div class="flex items-start gap-6 max-md:flex-col">

    {{-- MENÚ LATERAL --}}
    <div class="w-full md:w-[230px]">

        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-3 shadow-xl">

            <flux:navlist aria-label="Configuración">

                <flux:navlist.item
                    :href="route('profile.edit')"
                    wire:navigate
                    class="rounded-xl px-3 py-2.5 font-bold text-zinc-300 transition duration-150
                           hover:-translate-y-0.5 hover:bg-zinc-800 hover:text-white
                           active:scale-[0.97]"
                >
                    👤 Perfil
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('user-password.edit')"
                    wire:navigate
                    class="rounded-xl px-3 py-2.5 font-bold text-zinc-300 transition duration-150
                           hover:-translate-y-0.5 hover:bg-zinc-800 hover:text-white
                           active:scale-[0.97]"
                >
                    🔐 Contraseña
                </flux:navlist.item>

                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())

                    <flux:navlist.item
                        :href="route('two-factor.show')"
                        wire:navigate
                        class="rounded-xl px-3 py-2.5 font-bold text-zinc-300 transition duration-150
                               hover:-translate-y-0.5 hover:bg-zinc-800 hover:text-white
                               active:scale-[0.97]"
                    >
                        🛡️ Autenticación en dos pasos
                    </flux:navlist.item>

                @endif

                <flux:navlist.item
                    :href="route('appearance.edit')"
                    wire:navigate
                    class="rounded-xl px-3 py-2.5 font-bold text-zinc-300 transition duration-150
                           hover:-translate-y-0.5 hover:bg-zinc-800 hover:text-white
                           active:scale-[0.97]"
                >
                    🎨 Apariencia
                </flux:navlist.item>

            </flux:navlist>

        </div>

    </div>


    {{-- CONTENIDO --}}
    <div class="min-w-0 flex-1 self-stretch">

        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl md:p-6">

            {{-- ENCABEZADO DE SECCIÓN --}}
            <div class="mb-5 border-b border-zinc-800 pb-4">

                <flux:heading class="text-white">
                    {{ $heading ?? '' }}
                </flux:heading>

                <flux:subheading class="mt-1 text-zinc-400">
                    {{ $subheading ?? '' }}
                </flux:subheading>

            </div>

            {{-- CONTENIDO DE LA PANTALLA --}}
            <div class="settings-content w-full max-w-xl">
                {{ $slot }}
            </div>

        </div>

    </div>

</div>
