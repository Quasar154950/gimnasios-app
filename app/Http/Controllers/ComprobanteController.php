<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function descargar(Pago $pago)
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

        $pdf = Pdf::loadView('comprobantes.pdf', compact('pago'));

        $numero = str_pad(
            $pago->numero_comprobante,
            6,
            '0',
            STR_PAD_LEFT
        );

        return $pdf->download(
            'comprobante-' . $numero . '.pdf'
        );
    }
}