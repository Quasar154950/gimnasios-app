<div class="min-h-screen bg-slate-100 text-slate-900">
    <div class="max-w-6xl mx-auto px-4 py-6">

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
