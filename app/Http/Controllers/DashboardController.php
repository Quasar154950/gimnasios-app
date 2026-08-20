<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Nota;
use App\Models\Seguimiento;
use App\Models\MensajeCliente;
use App\Models\ReservaTurno;
use App\Models\Asistencia;
use App\Models\Turno;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->email === 'soporte@tuempresa.com') {
            return redirect('/soporte');
        }

        $hoy = Carbon::today();
        $abogadoId = auth()->id();
        $usuario = auth()->user();

        $diasRestantes = $usuario->fecha_vencimiento
            ? max(0, now()->startOfDay()->diffInDays($usuario->fecha_vencimiento->startOfDay(), false))
            : null;

        $totalClientesActivos = Cliente::where('abogado_id', $abogadoId)
            ->where('archivado', false)
            ->count();

        $reservasHoy = ReservaTurno::whereHas('turno', function ($q) use ($hoy, $abogadoId) {
            $q->where('abogado_id', $abogadoId)
              ->whereDate('fecha', $hoy);
        })->count();

        $cuposOcupadosHoy = $reservasHoy;

        $proximasReservas = ReservaTurno::whereHas('turno', function ($q) use ($hoy, $abogadoId) {
            $q->where('abogado_id', $abogadoId)
               ->whereDate('fecha', '>', $hoy)
               ->whereDate('fecha', '<=', $hoy->copy()->addDays(6));
        })->count();

        $proximasActividades = Turno::where('abogado_id', $abogadoId)
            ->whereDate('fecha', '>', $hoy)
            ->whereDate('fecha', '<=', $hoy->copy()->addDays(6))
            ->whereIn('id', ReservaTurno::select('turno_id'))
            ->withCount('reservas')
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->take(10)
            ->get();

        $pagosPendientes = Cliente::where('abogado_id', $abogadoId)
            ->where('archivado', false)
            ->whereNotNull('fecha_vencimiento_cuota')
            ->whereDate('fecha_vencimiento_cuota', '<', $hoy)
            ->count();

        $presentesAhora = Asistencia::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->where('presente', true)
            ->whereNull('hora_salida')
            ->count();

        $conteoTotalNotas = Nota::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })->count();

        $conteoNotasDeHoy = Nota::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })->whereDate('created_at', $hoy)->count();

        $cantHoy = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->whereDate('fecha_recordatorio', $hoy)
            ->count();

        $cantVencidos = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->whereNotNull('fecha_recordatorio')
            ->whereDate('fecha_recordatorio', '<', $hoy)
            ->count();

        $resueltosHoy = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->where('estado', 'resuelto')
            ->whereDate('updated_at', $hoy)
            ->count();

        $totalActivos = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->count();

        $porcentajeSalud = ($totalActivos > 0)
            ? round((($totalActivos - $cantVencidos) / $totalActivos) * 100)
            : 100;

        $totalMetaHoy = $cantHoy + $resueltosHoy;

        if ($totalMetaHoy == 0) {
            $porcentajeRendimiento = 100;
            $sinActividadHoy = true;
        } else {
            $porcentajeRendimiento = round(($resueltosHoy / $totalMetaHoy) * 100);
            $sinActividadHoy = false;
        }

        $notasFijadas = Nota::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->where('is_pinned', true)
            ->with('cliente')
            ->latest()
            ->take(5)
            ->get();

        $ultimosClientes = Cliente::where('abogado_id', $abogadoId)
            ->where('archivado', false)
            ->latest()
            ->take(5)
            ->get();

        $vencidos = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->whereNotNull('fecha_recordatorio')
            ->whereDate('fecha_recordatorio', '<', $hoy)
            ->with('cliente')
            ->get();

        $paraHoy = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->whereNotNull('fecha_recordatorio')
            ->whereDate('fecha_recordatorio', $hoy)
            ->with('cliente')
            ->get();

        $mensajesNoLeidos = MensajeCliente::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->where('remitente', 'cliente')
            ->where('leido', false)
            ->count();

        $proximosRecordatorios = Seguimiento::whereHas('cliente', function ($q) use ($abogadoId) {
            $q->where('abogado_id', $abogadoId);
        })
            ->whereIn('estado', ['pendiente', 'en_curso'])
            ->whereNotNull('fecha_recordatorio')
            ->whereDate('fecha_recordatorio', '>', $hoy)
            ->orderBy('fecha_recordatorio', 'asc')
            ->take(5)
            ->with('cliente')
            ->get();

        return view('dashboard', [
            'diasRestantes'           => $diasRestantes,
            'totalClientes'           => $totalClientesActivos,

            'sociosActivos'           => $totalClientesActivos,
            'reservasHoy'             => $reservasHoy,
            'cuposOcupadosHoy'        => $cuposOcupadosHoy,
            'proximasReservas'        => $proximasReservas,
            'proximasActividades'     => $proximasActividades,
            'pagosPendientes'         => $pagosPendientes,
            'presentesAhora'          => $presentesAhora,

            'historicoNotas'          => $conteoTotalNotas,
            'soloHoyNotas'            => $conteoNotasDeHoy,
            'cantHoy'                 => $cantHoy,
            'cantVencidos'            => $cantVencidos,
            'resueltosHoy'            => $resueltosHoy,
            'porcentaje'              => $porcentajeSalud,
            'porcentajeRendimiento'   => $porcentajeRendimiento,
            'sinActividadHoy'         => $sinActividadHoy,
            'ultimosClientes'         => $ultimosClientes,
            'notasFijadas'            => $notasFijadas,
            'vencidos'                => $vencidos,
            'paraHoy'                 => $paraHoy,
            'proximosRecordatorios'   => $proximosRecordatorios,
            'mensajesNoLeidos'        => $mensajesNoLeidos,
        ]);
    }
}