<?php

use App\Http\Controllers\Api\ActivarCuentaSocioController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobileAvisoController;
use App\Http\Controllers\Api\MobileHomeController;
use App\Http\Controllers\Api\MobileMensajeController;
use App\Http\Controllers\Api\MobileNovedadController;
use App\Http\Controllers\Api\MobileProgresoController;
use App\Http\Controllers\Api\MobileReservaController;
use App\Http\Controllers\Api\MobileRutinaController;
use App\Http\Controllers\Api\MobilePagoController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | RUTAS PÚBLICAS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );

    Route::post(
        '/forgot-password',
        [AuthController::class, 'forgotPassword']
    );

    Route::post(
        '/activar-cuenta',
        [ActivarCuentaSocioController::class, 'activar']
    );

    /*
    |--------------------------------------------------------------------------
    | RUTAS PROTEGIDAS
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        Route::get(
            '/me',
            [AuthController::class, 'me']
        );

        Route::get(
            '/home',
            [MobileHomeController::class, 'index']
        );

        /*
        |--------------------------------------------------------------------------
        | RESERVAS Y TURNOS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reservas',
            [MobileReservaController::class, 'index']
        );

        Route::post(
            '/turnos/{turno}/reservar',
            [MobileReservaController::class, 'reservar']
        );

        Route::delete(
            '/reservas/{reserva}',
            [MobileReservaController::class, 'cancelar']
        );

        /*
        |--------------------------------------------------------------------------
        | RUTINAS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/rutina',
            [MobileRutinaController::class, 'index']
        );

        /*
        |--------------------------------------------------------------------------
        | PROGRESO
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/progreso',
            [MobileProgresoController::class, 'index']
        );

        /*
        |--------------------------------------------------------------------------
        | MENSAJES
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/mensajes',
            [MobileMensajeController::class, 'index']
        );

        Route::post(
            '/mensajes',
            [MobileMensajeController::class, 'store']
        );

        Route::delete(
            '/mensajes',
            [MobileMensajeController::class, 'clear']
        );

        /*
        |--------------------------------------------------------------------------
        | AVISOS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/avisos',
            [MobileAvisoController::class, 'index']
        );

        /*
        |--------------------------------------------------------------------------
        | NOVEDADES
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/novedades',
            [MobileNovedadController::class, 'index']
        );
        
        /*
        |--------------------------------------------------------------------------
        | PAGOS
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/cuota/pagar',
            [MobilePagoController::class, 'crearPago']
        );

        /*
        |--------------------------------------------------------------------------
        | SESIÓN
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );
    });
});