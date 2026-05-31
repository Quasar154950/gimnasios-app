<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ $title ?? '' }}
</title>

@php
    $slug = auth()->check() ? auth()->user()->slug_estudio : null;

    $manifest = match($slug) {
        'demo' => '/manifest-demo.json',
        'sportgym' => '/manifest-sportgym.json',
        default => '/manifest-sportgym.json',
    };

    $logo = auth()->check()
        ? asset(auth()->user()->logo_estudio ?? 'images/logo-sportgym.png')
        : asset('images/logo-sportgym.png');

    $appName = auth()->check()
        ? auth()->user()->nombre_estudio
        : 'SportGym';
@endphp

{{-- FAVICON PERSONALIZADO --}}
<link rel="icon" href="{{ $logo }}">
<link rel="apple-touch-icon" href="{{ $logo }}">

{{-- PWA DINÁMICA --}}
<link rel="manifest" href="{{ $manifest }}">

<meta name="theme-color" content="#111827">

<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">

<meta name="apple-mobile-web-app-title" content="{{ $appName }}">

{{-- FUENTES --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

{{-- ESTILOS Y JS --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- MODO OSCURO / APARIENCIA --}}
@fluxAppearance
