<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Configuración del perfil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Carga los datos actuales del usuario.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Actualiza la información del perfil.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate(
            $this->profileRules($user->id)
        );

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch(
            'profile-updated',
            name: $user->name
        );
    }

    /**
     * Reenvía el correo de verificación.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(
                default: route(
                    'dashboard',
                    absolute: false
                )
            );

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash(
            'status',
            'verification-link-sent'
        );
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail
            && ! Auth::user()->hasVerifiedEmail();
    }
};
?>

<section class="w-full">

    @include('partials.settings-heading')

    <flux:heading class="sr-only">
        Configuración del perfil
    </flux:heading>

    <x-pages::settings.layout
        heading="Perfil"
        subheading="Actualizá tu nombre y dirección de correo electrónico"
    >

        <form
            wire:submit="updateProfileInformation"
            class="my-6 w-full space-y-6"
        >

            <flux:input
                wire:model="name"
                label="Nombre"
                type="text"
                required
                autofocus
                autocomplete="name"
            />

            <div>

                <flux:input
                    wire:model="email"
                    label="Correo electrónico"
                    type="email"
                    required
                    autocomplete="email"
                />

                @if ($this->hasUnverifiedEmail)

                    <div>

                        <flux:text class="mt-4">

                            Tu dirección de correo electrónico
                            todavía no está verificada.

                            <flux:link
                                class="cursor-pointer text-sm"
                                wire:click.prevent="resendVerificationNotification"
                            >
                                Hacé clic acá para reenviar
                                el correo de verificación.
                            </flux:link>

                        </flux:text>

                        @if (
                            session('status')
                            === 'verification-link-sent'
                        )

                            <flux:text
                                class="mt-2 font-medium
                                       !text-green-600
                                       !dark:text-green-400"
                            >
                                Se envió un nuevo enlace de
                                verificación a tu correo electrónico.
                            </flux:text>

                        @endif

                    </div>

                @endif

            </div>

            <div class="flex items-center gap-4">

                <div class="flex items-center justify-end">

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full"
                        data-test="update-profile-button"
                    >
                        Guardar cambios
                    </flux:button>

                </div>

                <x-action-message
                    class="me-3"
                    on="profile-updated"
                >
                    Guardado correctamente.
                </x-action-message>

            </div>

        </form>

    </x-pages::settings.layout>

</section>