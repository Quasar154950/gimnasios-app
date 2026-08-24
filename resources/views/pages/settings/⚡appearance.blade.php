<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Configuración de apariencia')] class extends Component {
    //
};
?>

<section class="w-full">

    @include('partials.settings-heading')

    <flux:heading class="sr-only">
        Configuración de apariencia
    </flux:heading>

    <x-pages::settings.layout
        heading="Apariencia"
        subheading="Elegí cómo querés visualizar el panel"
    >

        <flux:radio.group
            x-data
            variant="segmented"
            x-model="$flux.appearance"
        >

            <flux:radio value="light" icon="sun">
                Claro
            </flux:radio>

            <flux:radio value="dark" icon="moon">
                Oscuro
            </flux:radio>

            <flux:radio value="system" icon="computer-desktop">
                Sistema
            </flux:radio>

        </flux:radio.group>

    </x-pages::settings.layout>

</section>
