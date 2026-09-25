<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutina_ejercicios', function (Blueprint $table) {

            $table->unsignedInteger('duracion_segundos')
                ->nullable()
                ->after('peso');

            $table->unsignedInteger('distancia_metros')
                ->nullable()
                ->after('duracion_segundos');

            $table->unsignedInteger('repeticiones')
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('rutina_ejercicios', function (Blueprint $table) {

            $table->unsignedInteger('repeticiones')
                ->nullable(false)
                ->default(10)
                ->change();

            $table->dropColumn([
                'duracion_segundos',
                'distancia_metros',
            ]);
        });
    }
};