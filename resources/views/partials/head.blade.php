<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ $title ?? '' }}
</title>

@php
    $logo = auth()->check()
        ? asset(auth()->user()->logo_estudio ?? 'images/logo-sportgym.png')
        : asset('images/logo-sportgym.png');
@endphp

{{-- FAVICON PERSONALIZADO --}}
<link rel="icon" href="{{ $logo }}">
<link rel="apple-touch-icon" href="{{ $logo }}">

<meta name="theme-color" content="#111827">

{{-- FUENTES --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

{{-- ESTILOS Y JS --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- MODO OSCURO / APARIENCIA --}}
@fluxAppearance
