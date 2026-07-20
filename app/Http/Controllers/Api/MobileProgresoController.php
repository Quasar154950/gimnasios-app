<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileProgresoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('cliente');

        $cliente = $user->cliente;

        if (! $cliente) {
            return response()->json([
                'message' => 'No existe un socio asociado a esta cuenta.',
            ], 422);
        }

        $hoy = Carbon::today();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();
        $inicioAnio = $hoy->copy()->startOfYear();
        $objetivoMensual = 12;

        /*
        |--------------------------------------------------------------------------
        | FECHAS DE ASISTENCIA
        |--------------------------------------------------------------------------
        |
        | Se toma una sola asistencia por día para evitar duplicar resultados
        | si el socio ingresó más de una vez durante la misma jornada.
        |
        */

        $fechasAsistencia = Asistencia::query()
            ->where('cliente_id', $cliente->id)
            ->whereNotNull('fecha')
            ->orderBy('fecha')
            ->get(['fecha'])
            ->pluck('fecha')
            ->map(function ($fecha) {
                return Carbon::parse($fecha)->startOfDay();
            })
            ->unique(function (Carbon $fecha) {
                return $fecha->format('Y-m-d');
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalAsistencias = $fechasAsistencia->count();

        $asistenciasMesActual = $fechasAsistencia
            ->filter(function (Carbon $fecha) use ($inicioMes, $finMes) {
                return $fecha->betweenIncluded($inicioMes, $finMes);
            })
            ->count();

        $asistenciasAnioActual = $fechasAsistencia
            ->filter(function (Carbon $fecha) use ($inicioAnio, $hoy) {
                return $fecha->betweenIncluded($inicioAnio, $hoy);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | OBJETIVO MENSUAL
        |--------------------------------------------------------------------------
        */

        $porcentajeObjetivo = $objetivoMensual > 0
            ? min(
                100,
                (int) round(
                    ($asistenciasMesActual / $objetivoMensual) * 100
                )
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | RACHAS
        |--------------------------------------------------------------------------
        */

        $rachaActual = $this->calcularRachaActual(
            $fechasAsistencia->all(),
            $hoy
        );

        $mejorRacha = $this->calcularMejorRacha(
            $fechasAsistencia->all()
        );

        /*
        |--------------------------------------------------------------------------
        | ASISTENCIAS DE LOS ÚLTIMOS 6 MESES
        |--------------------------------------------------------------------------
        */

        $asistenciasPorMes = collect();

        for ($i = 5; $i >= 0; $i--) {
            $mes = $hoy->copy()->subMonths($i);

            $cantidad = $fechasAsistencia
                ->filter(function (Carbon $fecha) use ($mes) {
                    return $fecha->year === $mes->year
                        && $fecha->month === $mes->month;
                })
                ->count();

            $asistenciasPorMes->push([
                'clave' => $mes->format('Y-m'),
                'mes' => $this->nombreMes($mes),
                'cantidad' => $cantidad,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LOGROS
        |--------------------------------------------------------------------------
        */

        $logros = [
            [
                'codigo' => 'primera_asistencia',
                'titulo' => 'Primer paso',
                'descripcion' => 'Registraste tu primera asistencia.',
                'desbloqueado' => $totalAsistencias >= 1,
            ],
            [
                'codigo' => 'diez_asistencias',
                'titulo' => 'En movimiento',
                'descripcion' => 'Alcanzaste 10 asistencias.',
                'desbloqueado' => $totalAsistencias >= 10,
            ],
            [
                'codigo' => 'cincuenta_asistencias',
                'titulo' => 'Constancia',
                'descripcion' => 'Alcanzaste 50 asistencias.',
                'desbloqueado' => $totalAsistencias >= 50,
            ],
            [
                'codigo' => 'racha_tres',
                'titulo' => 'Tres días seguidos',
                'descripcion' => 'Entrenaste durante 3 días consecutivos.',
                'desbloqueado' => $mejorRacha >= 3,
            ],
            [
                'codigo' => 'racha_siete',
                'titulo' => 'Semana imparable',
                'descripcion' => 'Entrenaste durante 7 días consecutivos.',
                'desbloqueado' => $mejorRacha >= 7,
            ],
            [
                'codigo' => 'objetivo_mensual',
                'titulo' => 'Objetivo cumplido',
                'descripcion' => 'Cumpliste el objetivo mensual.',
                'desbloqueado' =>
                    $asistenciasMesActual >= $objetivoMensual,
            ],
        ];

        return response()->json([
            'resumen' => [
                'total_asistencias' => $totalAsistencias,
                'asistencias_mes_actual' => $asistenciasMesActual,
                'asistencias_anio_actual' => $asistenciasAnioActual,
                'racha_actual' => $rachaActual,
                'mejor_racha' => $mejorRacha,
            ],

            'objetivo_mensual' => [
                'objetivo' => $objetivoMensual,
                'realizadas' => $asistenciasMesActual,
                'restantes' => max(
                    0,
                    $objetivoMensual - $asistenciasMesActual
                ),
                'porcentaje' => $porcentajeObjetivo,
                'cumplido' =>
                    $asistenciasMesActual >= $objetivoMensual,
            ],

            'asistencias_por_mes' => $asistenciasPorMes,

            'logros' => $logros,
        ]);
    }

    private function calcularRachaActual(
        array $fechas,
        Carbon $hoy
    ): int {
        if (empty($fechas)) {
            return 0;
        }

        $fechasNormalizadas = collect($fechas)
            ->map(function ($fecha) {
                return Carbon::parse($fecha)->format('Y-m-d');
            })
            ->unique()
            ->values();

        $ultimaFecha = Carbon::parse(
            $fechasNormalizadas->last()
        )->startOfDay();

        /*
        | La racha sigue activa si la última asistencia fue hoy
        | o ayer. Si fue antes, la racha actual es cero.
        */

        if (
            ! $ultimaFecha->isSameDay($hoy)
            && ! $ultimaFecha->isSameDay($hoy->copy()->subDay())
        ) {
            return 0;
        }

        $racha = 1;
        $fechaEsperada = $ultimaFecha->copy()->subDay();

        for ($i = $fechasNormalizadas->count() - 2; $i >= 0; $i--) {
            $fecha = Carbon::parse(
                $fechasNormalizadas[$i]
            )->startOfDay();

            if ($fecha->isSameDay($fechaEsperada)) {
                $racha++;
                $fechaEsperada->subDay();
                continue;
            }

            if ($fecha->lessThan($fechaEsperada)) {
                break;
            }
        }

        return $racha;
    }

    private function calcularMejorRacha(array $fechas): int
    {
        if (empty($fechas)) {
            return 0;
        }

        $fechasOrdenadas = collect($fechas)
            ->map(function ($fecha) {
                return Carbon::parse($fecha)->startOfDay();
            })
            ->unique(function (Carbon $fecha) {
                return $fecha->format('Y-m-d');
            })
            ->sort()
            ->values();

        $mejorRacha = 1;
        $rachaActual = 1;

        for ($i = 1; $i < $fechasOrdenadas->count(); $i++) {
            $fechaAnterior = $fechasOrdenadas[$i - 1];
            $fechaActual = $fechasOrdenadas[$i];

            if ($fechaAnterior->copy()->addDay()->isSameDay($fechaActual)) {
                $rachaActual++;
                $mejorRacha = max($mejorRacha, $rachaActual);
            } else {
                $rachaActual = 1;
            }
        }

        return $mejorRacha;
    }

    private function nombreMes(Carbon $fecha): string
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $meses[$fecha->month];
    }
}
