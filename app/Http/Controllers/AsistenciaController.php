<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Cliente;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AsistenciaController extends Controller
{
    public function index()
    {
        $abogadoId = auth()->id();

        $presentes = Asistencia::with('cliente')
            ->whereHas('cliente', function ($q) use ($abogadoId) {
                $q->where('abogado_id', $abogadoId);
            })
            ->where('presente', true)
            ->whereNull('hora_salida')
            ->latest('hora_ingreso')
            ->get();

        $ingresosHoy = Asistencia::with('cliente')
           ->whereHas('cliente', function ($q) use ($abogadoId) {
               $q->where('abogado_id', $abogadoId);
            })
            ->whereDate('fecha', today())
            ->latest('hora_ingreso')
            ->get();

        return view('asistencias.index', compact('presentes', 'ingresosHoy'));
    }

    public function marcar(Cliente $cliente)
    {
        // Seguridad: que el socio pertenezca al gimnasio/admin logueado
        if ($cliente->abogado_id !== auth()->id()) {
            abort(403);
        }

        $asistenciaAbierta = Asistencia::where('cliente_id', $cliente->id)
            ->where('presente', true)
            ->whereNull('hora_salida')
            ->latest('hora_ingreso')
            ->first();

        if ($asistenciaAbierta) {
            $asistenciaAbierta->update([
                'hora_salida' => now(),
                'presente' => false,
            ]);

            return redirect()
                ->route('asistencias.index')
                ->with('success', 'Salida registrada para ' . $cliente->nombre);
        }

        Asistencia::create([
            'cliente_id' => $cliente->id,
            'fecha' => today(),
            'hora_ingreso' => now(),
            'hora_salida' => null,
            'presente' => true,
        ]);

        return redirect()
            ->route('asistencias.index')
            ->with('success', 'Ingreso registrado para ' . $cliente->nombre);
    }

    public function salidaManual(Asistencia $asistencia)
    {
        if ($asistencia->cliente->abogado_id !== auth()->id()) {
            abort(403);
        }

        $asistencia->update([
            'hora_salida' => now(),
            'presente' => false,
        ]);

        return back()->with('success', 'Salida manual registrada correctamente.');
    }

    public function qr(Cliente $cliente)
    {
        // Seguridad: que el socio pertenezca al gimnasio/admin logueado
        if ($cliente->abogado_id !== auth()->id()) {
            abort(403);
        }

        $url = route('asistencias.marcar', $cliente->id);

        $renderer = new ImageRenderer(
            new RendererStyle(260),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $qrSvg = $writer->writeString($url);

        return view('asistencias.qr', compact('cliente', 'qrSvg', 'url'));
    }

    public function miQr()
    {
        $cliente = Cliente::where('user_id', auth()->id())->firstOrFail();

        $url = route('asistencias.marcar', $cliente->id);

        $renderer = new ImageRenderer(
            new RendererStyle(260),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $qrSvg = $writer->writeString($url);

        return view('cliente.mi-qr', compact('cliente', 'qrSvg', 'url'));
    }

    public function escanear()
    {
        return view('asistencias.escanear');
    }
}
