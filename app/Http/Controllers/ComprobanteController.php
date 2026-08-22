<?php

namespace App\Http\Controllers;

use App\Models\Pago;

class ComprobanteController extends Controller
{
    public function show(Pago $pago)
    {
        $pago->load('cliente');

        if (
            !$pago->cliente ||
            $pago->cliente->abogado_id !== auth()->id()
        ) {
            abort(403);
        }

        if (!$pago->numero_comprobante) {
            abort(404);
        }

        return view('comprobantes.show', compact('pago'));
    }
}