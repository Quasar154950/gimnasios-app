<x-layouts::app.sidebar :title="$title ?? null">
    <div
        x-data
        x-on:keydown.window.ctrl.k.prevent="$dispatch('open-global-search')"
        x-on:keydown.window.meta.k.prevent="$dispatch('open-global-search')"
    >
        <flux:main class="px-0 sm:px-2 md:px-4">
            <div style="background:linear-gradient(135deg,#ff6a00,#ff9500); min-height:100vh;" class="-mx-4 px-4 py-6 sm:-mx-6 sm:px-6">
                <div class="w-full md:mx-auto md:max-w-5xl">

                    @if(session('soporte_original_id') || session('soporte_central_impersonando'))
                        <div class="mb-4 rounded-xl border border-yellow-300 bg-yellow-50 p-3 text-sm text-yellow-900 flex items-center justify-between gap-3">
                            <div>
                                ⚠️ Estás viendo el sistema como:
                                <strong>{{ auth()->user()->email }}</strong>
                            </div>

                            @if(session('soporte_central_impersonando'))
                                <a
                                    href="{{ route('soporte.gimnasios.volver-central') }}"
                                    class="shrink-0 px-3 py-2 rounded bg-yellow-600 hover:bg-yellow-700 text-white cursor-pointer transition"
                                >
                                    ↩ Volver a soporte
                                </a>
                            @else
                                <form method="POST" action="{{ route('soporte.volver') }}" class="shrink-0">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="px-3 py-2 rounded bg-yellow-600 hover:bg-yellow-700 text-white cursor-pointer transition"
                                    >
                                        ↩ Volver a soporte
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </flux:main>
    </div>
</x-layouts::app.sidebar>