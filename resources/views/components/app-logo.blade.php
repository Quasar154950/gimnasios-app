@props([
    'sidebar' => false,
])

@php
    $user = auth()->user();

    $slug = $user->slug_estudio ?? null;
    $nombreEstudio = $user->nombre_estudio ?? null;

    if ($user && $user->role === 'cliente') {

        $cliente = \App\Models\Cliente::with('abogado')
            ->where('user_id', $user->id)
            ->first();

        $slug = $cliente?->abogado?->slug_estudio ?? $slug;
        $nombreEstudio = $cliente?->abogado?->nombre_estudio ?? $nombreEstudio;
    }

    $esDemoGym = $slug === 'demo';

    $nombreEstudio = $nombreEstudio
        ?? ($esDemoGym ? 'DemoGym' : 'SportGym Tandil');

    $logo = $esDemoGym
        ? asset('images/logo-demogym.png')
        : asset('images/logo-sportgym.png');
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