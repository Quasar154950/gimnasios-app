<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Configuración de contraseña')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Actualiza la contraseña del usuario autenticado.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset(
                'current_password',
                'password',
                'password_confirmation'
            );

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset(
            'current_password',
            'password',
            'password_confirmation'
        );

        $this->dispatch('password-updated');
    }
};
?>

<section class="w-full">

    @include('partials.settings-heading')

    <flux:heading class="sr-only">
        Configuración de contraseña
    </flux:heading>

    <x-pages::settings.layout
        heading="Contraseña"
        subheading="Actualizá tu contraseña para mantener tu cuenta segura"
    >

        <form
            method="POST"
            wire:submit="updatePassword"
            class="mt-6 space-y-6"
        >

            <flux:input
                wire:model="current_password"
                label="Contraseña actual"
                label:class="text-zinc-400"
                type="password"
                required
                autocomplete="current-password"
            />

            <flux:input
                wire:model="password"
                label="Nueva contraseña"
                label:class="text-zinc-400"
                type="password"
                required
                autocomplete="new-password"
            />

            <flux:input
                wire:model="password_confirmation"
                label="Confirmar nueva contraseña"
                label:class="text-zinc-400"
                type="password"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">

                <div class="flex items-center justify-end">

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full"
                        data-test="update-password-button"
                    >
                        Guardar cambios
                    </flux:button>

                </div>

                <x-action-message
                    class="me-3"
                    on="password-updated"
                >
                    Contraseña actualizada correctamente.
                </x-action-message>

            </div>

        </form>

    </x-pages::settings.layout>

</section>