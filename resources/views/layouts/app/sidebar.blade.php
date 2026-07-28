<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

    <head>
        @include('partials.head')

        <link rel="stylesheet" href="{{ asset('css/app-effects.css') }}">
    </head>

    <body
        x-data
        x-on:keydown.window.ctrl.k.prevent="$dispatch('open-global-search')"
        x-on:keydown.window.meta.k.prevent="$dispatch('open-global-search')"
        class="min-h-screen bg-zinc-950 text-zinc-100"
    >

        <flux:sidebar
            sticky
            collapsible="mobile"
            class="border-e border-zinc-800 bg-zinc-950"
        >

            <flux:sidebar.header>

                <x-app-logo
                    :sidebar="true"
                    href="{{ route('dashboard') }}"
                    wire:navigate
                />

                <flux:sidebar.collapse class="lg:hidden" />

            </flux:sidebar.header>


            {{-- BUSCADOR --}}
            <div class="px-3 mt-4 mb-2">

                <button
                    type="button"
                    x-on:click="$dispatch('open-global-search')"
                    class="w-full flex items-center justify-between pl-3 pr-2 py-1.5 text-xs rounded-lg border border-zinc-800 bg-zinc-900 text-zinc-400 hover:border-orange-500 transition-all cursor-text group"
                >

                    <div class="flex items-center gap-2">

                        <flux:icon
                            name="magnifying-glass"
                            variant="micro"
                        />

                        <span>Buscar...</span>

                    </div>

                    <div class="hidden sm:flex items-center gap-1 bg-zinc-800 px-1.5 py-0.5 rounded text-[9px] text-zinc-300">
                        <span>Ctrl</span>
                        <span>K</span>
                    </div>

                </button>

            </div>


            {{-- SIDEBAR --}}
            <flux:sidebar.nav>

                {{-- SOPORTE --}}
                @if(auth()->user()->email === 'soporte@tuempresa.com')

                    <flux:sidebar.group
                        heading="Navegación"
                        class="grid"
                    >

                        <flux:sidebar.item
                            icon="home"
                            href="/soporte"
                        >
                            Panel de soporte
                        </flux:sidebar.item>

                    </flux:sidebar.group>


                {{-- SOCIO GIMNASIO --}}
                @elseif(auth()->user()->role === 'cliente')

                    <flux:sidebar.group
                        heading="Navegación"
                        class="grid"
                    >

                        <flux:sidebar.item
                            icon="home"
                            :href="route('cliente.dashboard')"
                            :current="request()->routeIs('cliente.dashboard')"
                        >
                            Panel
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="calendar-days"
                            :href="route('cliente.turnos')"
                            :current="request()->routeIs('cliente.turnos')"
                            wire:navigate
                        >
                            Reservas
                        </flux:sidebar.item>

                    </flux:sidebar.group>


                {{-- ADMIN GIMNASIO --}}
                @else

                    {{-- NAVEGACIÓN PRINCIPAL --}}
                    <flux:sidebar.group
                        heading="Navegación"
                        class="grid"
                    >

                        <flux:sidebar.item
                            icon="home"
                            :href="route('dashboard')"
                            :current="request()->routeIs('dashboard')"
                            wire:navigate
                        >
                            Panel
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="users"
                            :href="route('clientes.index')"
                            :current="request()->routeIs('clientes.*')"
                            wire:navigate
                        >
                            Socios
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="calendar-days"
                            :href="route('turnos.index')"
                            :current="request()->routeIs('turnos.*')"
                            wire:navigate
                        >
                            Reservas
                        </flux:sidebar.item>

                    </flux:sidebar.group>


                    {{-- ENTRENAMIENTO --}}
                    <flux:sidebar.group
                        expandable
                        heading="Entrenamiento"
                        icon="rectangle-stack"
                        :expanded="request()->routeIs('ejercicios.*') || request()->routeIs('rutinas.*')"
                        class="grid"
                    >

                        <flux:sidebar.item
                            icon="book-open"
                            :href="route('ejercicios.index')"
                            :current="request()->routeIs('ejercicios.*')"
                            wire:navigate
                        >
                            Biblioteca de ejercicios
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="clipboard-document-list"
                            :href="route('rutinas.index')"
                            :current="request()->routeIs('rutinas.*')"
                            wire:navigate
                        >
                            Rutinas
                        </flux:sidebar.item>

                    </flux:sidebar.group>


                    {{-- GESTIÓN --}}
                    <flux:sidebar.group
                        heading="Gestión"
                        class="grid"
                    >

                        <flux:sidebar.item
                            icon="banknotes"
                            :href="route('pagos.index')"
                            :current="request()->routeIs('pagos.*')"
                            wire:navigate
                        >
                            Pagos / Cuotas
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="megaphone"
                            :href="route('avisos.index')"
                            :current="request()->routeIs('avisos.*')"
                            wire:navigate
                        >
                            Avisos
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="newspaper"
                            :href="route('novedades.index')"
                            :current="request()->routeIs('novedades.*')"
                            wire:navigate
                        >
                            Novedades
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="credit-card"
                            :href="route('suscripcion.index')"
                            :current="request()->routeIs('suscripcion.*')"
                            wire:navigate
                        >
                            Suscripción
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="credit-card"
                            :href="route('mercadopago.index')"
                            :current="request()->routeIs('mercadopago.*')"
                            wire:navigate
                        >
                            Mercado Pago
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="qr-code"
                            :href="route('asistencias.index')"
                            :current="request()->routeIs('asistencias.index')"
                            wire:navigate
                        >
                            Asistencias
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="camera"
                            :href="route('asistencias.escanear')"
                            :current="request()->routeIs('asistencias.escanear')"
                        >
                            Escanear QR
                        </flux:sidebar.item>

                    </flux:sidebar.group>

                @endif

            </flux:sidebar.nav>


            <flux:spacer />


            <x-desktop-user-menu
                class="hidden lg:block"
                :name="auth()->user()->name"
            />

        </flux:sidebar>


        {{-- HEADER MOBILE --}}
        <flux:header class="lg:hidden">

            <flux:sidebar.toggle
                icon="bars-2"
                inset="left"
            />

            <flux:spacer />

            <flux:dropdown
                position="top"
                align="end"
            >

                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>

                    <flux:menu.item :href="route('profile.edit')">
                        Configuración
                    </flux:menu.item>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <flux:menu.item
                            as="button"
                            type="submit"
                        >
                            Cerrar sesión
                        </flux:menu.item>

                    </form>

                </flux:menu>

            </flux:dropdown>

        </flux:header>


        {{-- CONTENIDO --}}
        <flux:main class="w-full bg-zinc-950">

            <div class="w-full max-w-[1100px] mx-auto px-0 md:px-4">
                {{ $slot }}
            </div>

        </flux:main>


        <livewire:actions.global-search />


        @fluxScripts


        <script src="{{ asset('js/app-effects.js') }}"></script>


        <!-- PWA desactivada -->

        </script>

    </body>

</html>