<button
    wire:click="reservar"
    wire:loading.attr="disabled"
    wire:target="reservar"

    style="
        background:#f97316;
        color:white;
        border-radius:18px;
        padding:10px 16px;
        font-size:14px;
        font-weight:bold;
        width:100%;
        transition:0.2s;
    "

    class="hover:scale-[1.01] active:scale-[0.99]"
>

    <span wire:loading.remove wire:target="reservar">
        Reservar actividad
    </span>

    <span wire:loading wire:target="reservar">
        ⏳ Reservando...
    </span>

</button>