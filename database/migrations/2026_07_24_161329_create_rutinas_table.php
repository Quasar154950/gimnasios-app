<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------
            | GIMNASIO
            |--------------------------------------------------------------
            */

            $table->foreignId('abogado_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------
            | DATOS DE LA RUTINA
            |--------------------------------------------------------------
            */

            $table->string('nombre');

            $table->text('descripcion')
                ->nullable();

            $table->string('objetivo')
                ->nullable();

            $table->unsignedInteger('duracion_semanas')
                ->nullable();

            $table->boolean('activa')
                ->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------
            */

            $table->index([
                'abogado_id',
                'activa',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutinas');
    }
};
