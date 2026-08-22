<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;

class ComprobanteController extends Controller
{
    public function show(Pago $pago)
    {
        $pago->load('cliente');

        $this->validarPago($pago);

        $logo = $this->obtenerLogo();

        return view('comprobantes.show', compact(
            'pago',
            'logo'
        ));
    }

    public function descargar(Pago $pago)
    {
        $pago->load('cliente');

        $this->validarPago($pago);

        $logo = $this->obtenerLogo();

        $pdf = Pdf::loadView('comprobantes.pdf', compact(
            'pago',
            'logo'
        ));

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

    private function validarPago(Pago $pago): void
    {
        if (
            !$pago->cliente ||
            $pago->cliente->abogado_id !== auth()->id()
        ) {
            abort(403);
        }

        if (!$pago->numero_comprobante) {
            abort(404);
        }
    }

    private function obtenerLogo(): ?string
    {
        $slug = auth()->user()->slug_estudio ?? 'sportgym';

        return match ($slug) {
            'demo' => public_path('images/logo-demo.png'),
            'sportgym' => public_path('images/logo-sportgym.png'),
            default => null,
        };
    }
}