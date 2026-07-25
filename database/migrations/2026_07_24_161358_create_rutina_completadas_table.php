<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutina_completadas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rutina_asignacion_id')
                ->constrained('rutina_asignaciones')
                ->cascadeOnDelete();

            $table->foreignId('rutina_dia_id')
                ->constrained('rutina_dias')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------
            | ENTRENAMIENTO REALIZADO
            |--------------------------------------------------------------
            */

            $table->date('fecha');

            $table->time('hora_inicio')
                ->nullable();

            $table->time('hora_fin')
                ->nullable();

            $table->text('observaciones')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------
            | EVITAR DOBLE REGISTRO
            |--------------------------------------------------------------
            */

            $table->unique([
                'rutina_asignacion_id',
                'rutina_dia_id',
                'fecha',
            ], 'rutina_completada_unica');

            $table->index([
                'rutina_asignacion_id',
                'fecha',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutina_completadas');
    }
};
