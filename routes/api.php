<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobileHomeController;
use App\Http\Controllers\Api\MobileMensajeController;
use App\Http\Controllers\Api\MobileReservaController;
use App\Http\Controllers\Api\MobileProgresoController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );

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

        Route::get(
            '/progreso',
            [MobileProgresoController::class, 'index']);

        Route::delete(
            '/reservas/{reserva}',
            [MobileReservaController::class, 'cancelar']
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

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );

    });

});
