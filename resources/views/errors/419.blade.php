@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#071015] text-white px-6">
    <div class="max-w-md w-full text-center bg-white/10 border border-white/10 rounded-3xl p-8 shadow-xl">
        <div class="text-5xl mb-4">⏳</div>

        <h1 class="text-2xl font-bold mb-3">
            Tu sesión expiró
        </h1>

        <p class="text-white/70 mb-6">
            Por seguridad, necesitás volver a ingresar para continuar usando la app.
        </p>

        <a href="{{ route('login') }}"
           class="inline-flex justify-center w-full rounded-2xl bg-cyan-400 text-slate-950 font-bold py-3">
            Volver a ingresar
        </a>
    </div>
</div>
@endsection