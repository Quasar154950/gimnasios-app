@props(['url'])

@php
    $slug = session('slug_estudio', 'sportgym');

    $logoRelativo = "images/logo-{$slug}.png";

    $logo = file_exists(public_path($logoRelativo))
        ? asset($logoRelativo)
        : asset('images/logo-sportgym.png');
@endphp

<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
    <img
        src="{{ $logo }}"
        alt="Logo"
        style="max-height: 80px;"
    >
</a>
</td>
</tr>
