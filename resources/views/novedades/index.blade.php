@extends('layouts.app')

@section('title', 'Novedades')

@section('content')

<div class="max-w-7xl mx-auto p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold">
                📰 Novedades
            </h1>

            <p class="text-gray-500 mt-1">
                Administrá las publicaciones que verán los socios en la app.
            </p>
        </div>

        href="{{ route('novedades.create') }}"
           class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
            ➕ Nueva publicación
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">

        Todavía no hay publicaciones.

    </div>

</div>

@endsection
