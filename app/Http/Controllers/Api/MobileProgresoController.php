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
        | TODAS LAS ASISTENCIAS
        |--------------------------------------------------------------------------
        */

        $asistencias = Asistencia::query()
            ->where('cliente_id', $cliente->id)
            ->whereNotNull('fecha')
            ->orderBy('fecha')
            ->orderBy('hora_ingreso')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FECHAS ÚNICAS DE ENTRENAMIENTO
        |--------------------------------------------------------------------------
        |
        | Para estadísticas de entrenamientos se considera una sola asistencia
        | por día, aunque el socio haya ingresado varias veces.
        |
        */

        $fechasAsistencia = $asistencias
            ->pluck('fecha')
            ->map(function ($fecha) {
                return Carbon::parse($fecha)->startOfDay();
            })
            ->unique(function (Carbon $fecha) {
                return $fecha->format('Y-m-d');
            })
            ->sort()
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
        | DURACIÓN DE LOS ENTRENAMIENTOS
        |--------------------------------------------------------------------------
        |
        | Solo se calculan asistencias que tengan ingreso y salida.
        |
        */

$duracionesMinutos = $asistencias
    ->filter(function ($asistencia) {
        return ! empty($asistencia->fecha)
            && ! empty($asistencia->hora_ingreso)
            && ! empty($asistencia->hora_salida);
    })
    ->map(function ($asistencia) {
        $fecha = Carbon::parse($asistencia->fecha)->format('Y-m-d');

        $horaIngreso = Carbon::parse(
            $asistencia->hora_ingreso
        )->format('H:i:s');

        $horaSalida = Carbon::parse(
            $asistencia->hora_salida
        )->format('H:i:s');

        $ingreso = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $fecha . ' ' . $horaIngreso
        );

        $salida = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $fecha . ' ' . $horaSalida
        );

        if ($salida->lessThan($ingreso)) {
            $salida->addDay();
        }

        return (int) $ingreso->diffInMinutes($salida);
    });
        $tiempoTotalMinutos = (int) $duracionesMinutos->sum();

        $duracionPromedioMinutos = $duracionesMinutos->isNotEmpty()
            ? (int) round($duracionesMinutos->average())
            : 0;

        $duracionesMesActual = $asistencias
            ->filter(function ($asistencia) use ($inicioMes, $finMes) {
                if (
                    ! $asistencia->fecha
                    || ! $asistencia->hora_ingreso
                    || ! $asistencia->hora_salida
                ) {
                    return false;
                }

                $fecha = Carbon::parse($asistencia->fecha);

                return $fecha->betweenIncluded(
                    $inicioMes,
                    $finMes
                );
            })
            ->map(function ($asistencia) {
                $fecha = Carbon::parse($asistencia->fecha)
                    ->format('Y-m-d');

                $ingreso = Carbon::parse(
                    $fecha . ' ' . $asistencia->hora_ingreso
                );

                $salida = Carbon::parse(
                    $fecha . ' ' . $asistencia->hora_salida
                );

                if ($salida->lessThan($ingreso)) {
                    $salida->addDay();
                }

                return max(
                    0,
                    $ingreso->diffInMinutes($salida)
                );
            });

        $tiempoMesMinutos = (int) $duracionesMesActual->sum();

        /*
        |--------------------------------------------------------------------------
        | PROMEDIOS
        |--------------------------------------------------------------------------
        */

        if ($fechasAsistencia->isNotEmpty()) {
            $primeraAsistencia = $fechasAsistencia->first();

            $cantidadSemanas = max(
                1,
                $primeraAsistencia->diffInWeeks($hoy) + 1
            );

            $cantidadMeses = max(
                1,
                $primeraAsistencia->diffInMonths($hoy) + 1
            );
        } else {
            $cantidadSemanas = 1;
            $cantidadMeses = 1;
        }

        $promedioSemanal = round(
            $totalAsistencias / $cantidadSemanas,
            1
        );

        $promedioMensual = round(
            $totalAsistencias / $cantidadMeses,
            1
        );

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
        | ACTIVIDAD DE LOS ÚLTIMOS 12 MESES
        |--------------------------------------------------------------------------
        */

        $asistenciasPorMes = collect();

        for ($i = 11; $i >= 0; $i--) {
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
                'mes_nombre' => $this->nombreMes($mes),
                'anio' => $mes->year,
                'cantidad' => $cantidad,
                'asistencias' => $cantidad,
                'entrenamientos' => $cantidad,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LOGROS AUTOMÁTICOS
        |--------------------------------------------------------------------------
        */

        $logros = [
            [
                'codigo' => 'primera_asistencia',
                'titulo' => 'Primer paso',
                'descripcion' => 'Registraste tu primera asistencia.',
                'icono' => 'entrenamiento',
                'desbloqueado' => $totalAsistencias >= 1,
            ],
            [
                'codigo' => 'diez_asistencias',
                'titulo' => 'En movimiento',
                'descripcion' => 'Alcanzaste 10 entrenamientos.',
                'icono' => 'fitness',
                'desbloqueado' => $totalAsistencias >= 10,
            ],
            [
                'codigo' => 'veinticinco_asistencias',
                'titulo' => 'Buen ritmo',
                'descripcion' => 'Alcanzaste 25 entrenamientos.',
                'icono' => 'medalla',
                'desbloqueado' => $totalAsistencias >= 25,
            ],
            [
                'codigo' => 'cincuenta_asistencias',
                'titulo' => 'Constancia',
                'descripcion' => 'Alcanzaste 50 entrenamientos.',
                'icono' => 'trofeo',
                'desbloqueado' => $totalAsistencias >= 50,
            ],
            [
                'codigo' => 'cien_asistencias',
                'titulo' => 'Centenario',
                'descripcion' => 'Alcanzaste 100 entrenamientos.',
                'icono' => 'premium',
                'desbloqueado' => $totalAsistencias >= 100,
            ],
            [
                'codigo' => 'doscientas_cincuenta_asistencias',
                'titulo' => 'Leyenda del gimnasio',
                'descripcion' => 'Alcanzaste 250 entrenamientos.',
                'icono' => 'trofeo',
                'desbloqueado' => $totalAsistencias >= 250,
            ],
            [
                'codigo' => 'racha_tres',
                'titulo' => 'Tres días seguidos',
                'descripcion' => 'Entrenaste durante 3 días consecutivos.',
                'icono' => 'racha',
                'desbloqueado' => $mejorRacha >= 3,
            ],
            [
                'codigo' => 'racha_siete',
                'titulo' => 'Semana imparable',
                'descripcion' => 'Entrenaste durante 7 días consecutivos.',
                'icono' => 'fuego',
                'desbloqueado' => $mejorRacha >= 7,
            ],
            [
                'codigo' => 'racha_treinta',
                'titulo' => 'Imparable',
                'descripcion' => 'Alcanzaste una racha de 30 días.',
                'icono' => 'fuego',
                'desbloqueado' => $mejorRacha >= 30,
            ],
            [
                'codigo' => 'objetivo_mensual',
                'titulo' => 'Objetivo cumplido',
                'descripcion' => 'Cumpliste el objetivo mensual.',
                'icono' => 'trofeo',
                'desbloqueado' =>
                    $asistenciasMesActual >= $objetivoMensual,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
        |
        | Se incluyen nombres alternativos para mantener compatibilidad con
        | la pantalla Flutter actual.
        |
        */

        return response()->json([
            'resumen' => [
                'total_asistencias' => $totalAsistencias,
                'total_entrenamientos' => $totalAsistencias,
                'entrenamientos_totales' => $totalAsistencias,

                'asistencias_mes' => $asistenciasMesActual,
                'asistencias_mes_actual' => $asistenciasMesActual,
                'entrenamientos_mes' => $asistenciasMesActual,

                'asistencias_anio_actual' => $asistenciasAnioActual,

                'racha_actual' => $rachaActual,
                'mejor_racha' => $mejorRacha,

                'tiempo_total_minutos' => $tiempoTotalMinutos,
                'tiempo_total_formateado' =>
                    $this->formatearMinutos($tiempoTotalMinutos),

                'tiempo_mes_minutos' => $tiempoMesMinutos,
                'tiempo_mes_formateado' =>
                    $this->formatearMinutos($tiempoMesMinutos),

                'duracion_promedio_minutos' =>
                    $duracionPromedioMinutos,

                'duracion_promedio_formateada' =>
                    $this->formatearMinutos(
                        $duracionPromedioMinutos
                    ),

                'promedio_semanal' => $promedioSemanal,
                'promedio_mensual' => $promedioMensual,
            ],

            'objetivo_mensual' => [
                'meta' => $objetivoMensual,
                'objetivo' => $objetivoMensual,

                'actual' => $asistenciasMesActual,
                'progreso' => $asistenciasMesActual,
                'realizadas' => $asistenciasMesActual,
                'asistencias_mes' => $asistenciasMesActual,

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
            ->sort()
            ->values();

        $ultimaFecha = Carbon::parse(
            $fechasNormalizadas->last()
        )->startOfDay();

        /*
        | La racha continúa activa si el último entrenamiento fue
        | hoy o ayer.
        */

        if (
            ! $ultimaFecha->isSameDay($hoy)
            && ! $ultimaFecha->isSameDay(
                $hoy->copy()->subDay()
            )
        ) {
            return 0;
        }

        $racha = 1;
        $fechaEsperada = $ultimaFecha->copy()->subDay();

        for (
            $i = $fechasNormalizadas->count() - 2;
            $i >= 0;
            $i--
        ) {
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

            if (
                $fechaAnterior
                    ->copy()
                    ->addDay()
                    ->isSameDay($fechaActual)
            ) {
                $rachaActual++;

                $mejorRacha = max(
                    $mejorRacha,
                    $rachaActual
                );
            } else {
                $rachaActual = 1;
            }
        }

        return $mejorRacha;
    }

    private function formatearMinutos(int $minutos): string
    {
        if ($minutos <= 0) {
            return '0 min';
        }

        $horas = intdiv($minutos, 60);
        $minutosRestantes = $minutos % 60;

        if ($horas <= 0) {
            return $minutosRestantes . ' min';
        }

        if ($minutosRestantes <= 0) {
            return $horas . ' h';
        }

        return $horas . ' h ' . $minutosRestantes . ' min';
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
