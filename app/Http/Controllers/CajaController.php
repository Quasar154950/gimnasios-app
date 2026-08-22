<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | FILTRO DE FECHAS
        |--------------------------------------------------------------------------
        */

        $periodo = $request->get('periodo', 'mes');

        $desde = null;
        $hasta = null;

        switch ($periodo) {
            case 'hoy':
                $desde = now()->startOfDay();
                $hasta = now()->endOfDay();
                break;

            case 'semana':
                $desde = now()->startOfWeek();
                $hasta = now()->endOfWeek();
                break;

            case 'personalizado':
                $desde = $request->filled('desde')
                    ? Carbon::parse($request->desde)->startOfDay()
                    : now()->startOfMonth();

                $hasta = $request->filled('hasta')
                    ? Carbon::parse($request->hasta)->endOfDay()
                    : now()->endOfDay();
                break;

            case 'mes':
            default:
                $desde = now()->startOfMonth();
                $hasta = now()->endOfMonth();
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | PAGOS DEL GIMNASIO
        |--------------------------------------------------------------------------
        |
        | Solamente mostramos pagos aprobados y pertenecientes a socios
        | del administrador actualmente autenticado.
        |
        */

        $query = Pago::with('cliente')
            ->where('estado', 'aprobado')
            ->whereHas('cliente', function ($query) use ($usuario) {
                $query->where('abogado_id', $usuario->id);
            })
            ->whereBetween('fecha_pago', [
                $desde->toDateString(),
                $hasta->toDateString(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalIngresos = (clone $query)->sum('monto');

        $cantidadPagos = (clone $query)->count();

        /*
        |--------------------------------------------------------------------------
        | INGRESOS POR MEDIO DE PAGO
        |--------------------------------------------------------------------------
        */

        $ingresosPorMetodo = (clone $query)
            ->selectRaw('metodo_pago, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('metodo_pago')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MOVIMIENTOS
        |--------------------------------------------------------------------------
        */

        $movimientos = (clone $query)
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->get();

        return view('caja.index', compact(
            'periodo',
            'desde',
            'hasta',
            'totalIngresos',
            'cantidadPagos',
            'ingresosPorMetodo',
            'movimientos'
        ));
    }
}
