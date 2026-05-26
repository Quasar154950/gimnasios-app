@props([
    'sidebar' => false,
])

@php
    $user = auth()->user();

    $nombreEstudio = $user->nombre_estudio ?? 'SportGym Tandil';

    $logo = asset('images/logo-sportgym.png');
@endphp

@if($sidebar)

    <flux:sidebar.brand name="{{ $nombreEstudio }}" {{ $attributes }}>

        <x-slot name="logo" class="flex items-center justify-center">

            <img
                src="{{ $logo }}"
                class="h-15 w-15 object-contain"
            />

        </x-slot>

    </flux:sidebar.brand>

@else

    <flux:brand name="{{ $nombreEstudio }}" {{ $attributes }}>

        <x-slot name="logo" class="flex items-center justify-center">

            <img
                src="{{ $logo }}"
                class="h-10 w-10 object-contain"
            />

        </x-slot>

    </flux:brand>

@endif
