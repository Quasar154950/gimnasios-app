<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Mi Gimnasio')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="{{ asset('css/app-effects.css') }}"
    >

    @stack('styles')
</head>

<body class="@yield('body-class', 'bg-[#071015] text-white')">

    <main id="app-page" class="page-enter">
        @yield('content')
    </main>

    @yield('fixed-ui')

    <script src="{{ asset('js/app-effects.js') }}"></script>

    @stack('scripts')
</body>
</html>
