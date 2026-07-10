<div class="space-y-4">

    {{-- ACCIONES DEL CHAT --}}
    @if(!$mensajes->isEmpty())
        <div class="flex justify-end">

            <button
                type="button"
                wire:click="vaciarConversacion"
                onclick="return confirm('¿Seguro que querés borrar toda la conversación?')"
                style="background:black;color:white;border-radius:14px;padding:8px 14px;font-size:12px;font-weight:bold;display:inline-flex;align-items:center;gap:8px;"
            >
                🗑 Borrar conversación
            </button>

        </div>
    @endif

    {{-- LISTADO MENSAJES --}}
    <div class="rounded-xl border border-stone-300 bg-stone-100 p-4 h-80 overflow-y-auto">

        @if($mensajes->isEmpty())

            <p class="text-sm text-stone-500 italic">
                Todavía no hay mensajes.
            </p>

        @else

            <div class="space-y-3">

                @foreach($mensajes as $item)

                    @php
                        $esEstudio = $item->remitente === 'estudio';
                    @endphp

                    <div class="flex {{ $esEstudio ? 'justify-end' : 'justify-start' }}">

                        <div class="max-w-[80%] rounded-2xl px-4 py-3 shadow-sm
                            {{ $esEstudio
                                ? 'bg-orange-500 text-white'
                                : 'bg-stone-200 text-stone-800' }}">

                            <p class="text-sm leading-relaxed whitespace-pre-line">
                                {{ $item->mensaje }}
                            </p>

                            <div class="mt-2 text-[10px]
                                {{ $esEstudio
                                    ? 'text-orange-100 text-right'
                                    : 'text-stone-500 text-left' }}">

                                <div>
                                    📤 Enviado · {{ $item->created_at->format('d/m/Y H:i') }} hs
                                </div>

                                @if($item->leido)

                                    <div class="mt-1">
                                        👁 Leído

                                        @if($item->leido_at)
                                            · {{ \Carbon\Carbon::parse($item->leido_at)->format('d/m/Y H:i') }} hs
                                        @endif
                                    </div>

                                @else

                                    <div class="mt-1">
                                        🔔 No leído
                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

    {{-- FORMULARIO --}}
    <div class="flex gap-3">

        <textarea
            wire:model="mensaje"
            rows="3"
            placeholder="Escribir mensaje..."
            class="w-full rounded-xl border border-stone-300 bg-stone-100 px-4 py-3 text-sm resize-none text-stone-900 outline-none focus:ring-2 focus:ring-orange-500"
        ></textarea>

        <button
            type="button"
            wire:click="enviarMensaje"
            class="shrink-0 rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-bold transition"
        >
            Enviar
        </button>

    </div>

</div>