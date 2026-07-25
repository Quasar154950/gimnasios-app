<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutina_ejercicios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rutina_dia_id')
                ->constrained('rutina_dias')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------
            | EJERCICIO
            |--------------------------------------------------------------
            */

            $table->string('ejercicio');

            $table->unsignedInteger('series')
                ->default(3);

            $table->unsignedInteger('repeticiones')
                ->default(10);

            $table->string('peso')
                ->nullable();

            $table->unsignedInteger('descanso_segundos')
                ->default(60);

            $table->text('observaciones')
                ->nullable();

            $table->unsignedInteger('orden')
                ->default(1);

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->index([
                'rutina_dia_id',
                'orden',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutina_ejercicios');
    }
};
