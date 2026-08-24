<x-layouts::app :title="'Configurar administrador'">

    <div class="mx-auto max-w-2xl space-y-6">

        {{-- CABECERA --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl md:p-6">

            <div class="mb-2 inline-flex items-center rounded-full border border-blue-900 bg-blue-950/40 px-3 py-1 text-xs font-black text-blue-300">
                ⚙️ MCTandil · Soporte SaaS
            </div>

            <h1 class="text-2xl font-black text-white">
                Configurar administrador
            </h1>

            <p class="mt-2 text-sm text-zinc-400">
                Modificá el nombre y el email de acceso del administrador SaaS.
            </p>

        </div>


        {{-- INFO CLIENTE --}}
        <div class="rounded-3xl border border-zinc-800 bg-zinc-950 p-5 shadow-xl">

            <div class="flex flex-wrap items-center gap-2">

                @if($user->tipo_app === 'gimnasios')

                    <span class="rounded-full border border-orange-900 bg-orange-950/40 px-3 py-1 text-xs font-black text-orange-300">
                        🏋️ GIMNASIOS
                    </span>

                @elseif($user->tipo_app === 'abogados')

                    <span class="rounded-full border border-indigo-900 bg-indigo-950/40 px-3 py-1 text-xs font-black text-indigo-300">
                        ⚖️ ABOGADOS
                    </span>

                @endif

                @if($user->nombre_estudio)

                    <span class="text-sm font-black text-white">
                        {{ $user->nombre_estudio }}
                    </span>

                @endif

            </div>

            <div class="mt-4 text-xs text-zinc-500">
                El email configurado acá será utilizado para iniciar sesión
                y para recuperar la contraseña mediante correo electrónico.
            </div>

        </div>


        {{-- FORMULARIO --}}
        <form
            method="POST"
            action="{{ route('soporte.guardar.administrador', $user) }}"
            class="rounded-3xl border border-zinc-800 bg-zinc-900 p-5 shadow-xl md:p-6"
        >

            @csrf

            <div class="space-y-5">

                {{-- NOMBRE --}}
                <div>

                    <label
                        for="name"
                        class="mb-2 block text-sm font-black text-zinc-300"
                    >
                        Nombre del administrador
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        autocomplete="name"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none
                               transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30"
                        placeholder="Ej: Juan Pérez"
                    >

                    @error('name')
                        <p class="mt-2 text-xs font-bold text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- EMAIL --}}
                <div>

                    <label
                        for="email"
                        class="mb-2 block text-sm font-black text-zinc-300"
                    >
                        Email de acceso
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="email"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white outline-none
                               transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30"
                        placeholder="administracion@gimnasio.com"
                    >

                    @error('email')
                        <p class="mt-2 text-xs font-bold text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- ACLARACIÓN --}}
                <div class="rounded-2xl border border-yellow-900/70 bg-yellow-950/30 p-4 text-sm text-yellow-300">

                    ⚠️ Cambiar el email modifica inmediatamente el usuario
                    con el que este administrador inicia sesión.

                    <div class="mt-2 text-xs text-yellow-400">
                        La contraseña no se modifica desde esta pantalla.
                        Para eso utilizá el botón 🔑 Reset del Panel de Soporte.
                    </div>

                </div>


                {{-- BOTONES --}}
                <div class="flex flex-wrap gap-2 pt-2">

                    <button
                        type="submit"
                        style="cursor: pointer !important;"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-black text-white shadow-md
                               transition duration-150 hover:-translate-y-0.5 hover:bg-blue-500 hover:shadow-xl
                               active:scale-[0.97]"
                    >
                        ✅ Guardar cambios
                    </button>


                    <a
                        href="{{ url('/soporte') }}"
                        style="cursor: pointer !important;"
                        class="inline-flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-800 px-5 py-2.5 text-sm font-black text-zinc-200
                               transition duration-150 hover:-translate-y-0.5 hover:bg-zinc-700
                               active:scale-[0.97]"
                    >
                        ← Volver
                    </a>

                </div>

            </div>

        </form>

    </div>

</x-layouts::app>


