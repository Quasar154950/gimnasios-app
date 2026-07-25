<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejercicios', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------
            | GIMNASIO
            |--------------------------------------------------------------
            | NULL = ejercicio global del sistema.
            | Valor = ejercicio creado por un gimnasio.
            */

            $table->foreignId('abogado_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------
            | DATOS
            |--------------------------------------------------------------
            */

            $table->string('nombre');

            $table->string('grupo_muscular');

            $table->text('descripcion')
                ->nullable();

            $table->text('instrucciones')
                ->nullable();

            /*
            |--------------------------------------------------------------
            | MULTIMEDIA
            |--------------------------------------------------------------
            */

            $table->string('video_url')
                ->nullable();

            /*
            |--------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------
            */

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->index([
                'grupo_muscular',
                'activo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejercicios');
    }
};
