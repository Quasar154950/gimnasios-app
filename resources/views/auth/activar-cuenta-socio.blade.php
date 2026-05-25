<x-layouts::app>

    <div class="max-w-md mx-auto mt-10">

        <div class="rounded-3xl border border-stone-300 bg-stone-200 p-8 shadow-xl">

            <div class="text-center mb-6">

                <h1 class="text-3xl font-black text-stone-900">
                    Activar mi cuenta
                </h1>

                <p class="mt-2 text-sm text-stone-600 leading-relaxed">
                    Si ya sos socio del gimnasio, podés activar tu acceso usando tu DNI o email.
                </p>

            </div>

            @if(session('error'))

                <div class="mb-4 rounded-xl bg-red-100 border border-red-300 text-red-700 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>

            @endif

            <form method="POST"
                  action="{{ route('activar-cuenta-socio.activar') }}"
                  class="space-y-4">

                @csrf

                {{-- DNI O EMAIL --}}
                <div>

                    <label class="block text-sm font-bold text-stone-700">
                        DNI o Email
                    </label>

                    <input
                        type="text"
                        name="identificador"
                        value="{{ old('identificador') }}"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500"
                        required
                    >

                </div>

                {{-- PASSWORD --}}
                <div>

                    <label class="block text-sm font-bold text-stone-700">
                        Crear contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500"
                        required
                    >

                </div>

                {{-- CONFIRMAR PASSWORD --}}
                <div>

                    <label class="block text-sm font-bold text-stone-700">
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-stone-900 outline-none focus:ring-2 focus:ring-orange-500"
                        required
                    >

                </div>

                {{-- BOTÓN --}}
                <button
                    type="submit"
                    class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 transition text-white font-bold py-3"
                >
                    Activar cuenta
                </button>

            </form>

        </div>

    </div>

</x-layouts::app>