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

                $duracionesMinutos = collect();
        $duracionesMesActual = collect();

        foreach ($asistencias as $asistencia) {
            /*
            | Una entrada todavía abierta no tiene hora de salida.
            | No se calcula hasta que el socio registre su salida.
            */
            if (
                is_null($asistencia->hora_ingreso)
                || is_null($asistencia->hora_salida)
            ) {
                continue;
            }

            try {
                $fecha = Carbon::parse($asistencia->fecha)
                    ->format('Y-m-d');

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

                $duracion = (int) $ingreso->diffInMinutes(
                    $salida,
                    true
                );

                $duracionesMinutos->push($duracion);

                $fechaAsistencia = Carbon::parse(
                    $asistencia->fecha
                )->startOfDay();

                if (
                    $fechaAsistencia->betweenIncluded(
                        $inicioMes,
                        $finMes
                    )
                ) {
                    $duracionesMesActual->push($duracion);
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $tiempoTotalMinutos = (int) $duracionesMinutos->sum();

        $duracionPromedioMinutos = $duracionesMinutos->isNotEmpty()
            ? (int) round($duracionesMinutos->average())
            : 0;

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
        | ACTIVIDAD DEL AÑO ACTUAL
        |--------------------------------------------------------------------------
        */

        $asistenciasPorMes = collect();

for ($numeroMes = 1; $numeroMes <= 12; $numeroMes++) {
    $mes = Carbon::create(
        $hoy->year,
        $numeroMes,
        1
    )->startOfMonth();

    $cantidad = $fechasAsistencia
        ->filter(function (Carbon $fecha) use ($mes) {
            return $fecha->year === $mes->year
                && $fecha->month === $mes->month;
        })
        ->count();

    $nombreMes = $this->nombreMes($mes);

    $asistenciasPorMes->push([
        'clave' => $mes->format('Y-m'),
        'mes' => $nombreMes . ' ' . $mes->year,
        'mes_nombre' => $nombreMes . ' ' . $mes->year,
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
    'codigo' => 'racha_seis',
    'titulo' => 'Semana imparable',
    'descripcion' =>
        'Entrenaste durante 6 días de apertura consecutivos.',
    'icono' => 'fuego',
    'desbloqueado' => $mejorRacha >= 6,
],
[
    'codigo' => 'racha_treinta',
    'titulo' => 'Imparable',
    'descripcion' =>
        'Alcanzaste una racha de 30 días de apertura consecutivos.',
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
            return Carbon::parse($fecha)->startOfDay();
        })
        ->unique(function (Carbon $fecha) {
            return $fecha->format('Y-m-d');
        })
        ->sort()
        ->values();

    $ultimaFecha = $fechasNormalizadas->last()->copy();

    /*
    |--------------------------------------------------------------------------
    | ÚLTIMO DÍA DE ENTRENAMIENTO ESPERADO
    |--------------------------------------------------------------------------
    |
    | Los domingos no cuentan como día de entrenamiento y tampoco cortan
    | la racha. Por ejemplo, si hoy es lunes, una asistencia del sábado
    | todavía mantiene activa la racha.
    |
    */

$ultimaFechaEsperada = $hoy->copy();

if ($hoy->isSunday()) {
    /*
    | Si hoy es domingo, el último entrenamiento válido
    | para mantener la racha debe haber sido el sábado.
    */
    $ultimaFechaEsperada->subDay();

    if (! $ultimaFecha->isSameDay($ultimaFechaEsperada)) {
        return 0;
    }
} else {
    /*
    | En un día de apertura, la racha sigue activa si el socio
    | entrenó hoy o en el día de apertura inmediatamente anterior.
    */
    if (
        ! $ultimaFecha->isSameDay($ultimaFechaEsperada)
        && ! $ultimaFecha->isSameDay(
            $this->diaAnteriorDeEntrenamiento(
                $ultimaFechaEsperada
            )
        )
    ) {
        return 0;
    }
}

    $racha = 1;
    $fechaEsperada = $this->diaAnteriorDeEntrenamiento(
        $ultimaFecha
    );

    for (
        $i = $fechasNormalizadas->count() - 2;
        $i >= 0;
        $i--
    ) {
        $fecha = $fechasNormalizadas[$i]->copy();

        if ($fecha->isSameDay($fechaEsperada)) {
            $racha++;

            $fechaEsperada = $this->diaAnteriorDeEntrenamiento(
                $fechaEsperada
            );

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
        $fechaAnterior = $fechasOrdenadas[$i - 1]->copy();
        $fechaActual = $fechasOrdenadas[$i]->copy();

        $siguienteDiaEsperado = $fechaAnterior->copy()->addDay();

        if ($siguienteDiaEsperado->isSunday()) {
            $siguienteDiaEsperado->addDay();
        }

        if ($siguienteDiaEsperado->isSameDay($fechaActual)) {
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

    private function diaAnteriorDeEntrenamiento(
    Carbon $fecha
): Carbon {
    $fechaAnterior = $fecha->copy()->subDay();

    if ($fechaAnterior->isSunday()) {
        $fechaAnterior->subDay();
    }

    return $fechaAnterior;
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
