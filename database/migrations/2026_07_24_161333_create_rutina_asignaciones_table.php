<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutina_asignaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rutina_id')
                ->constrained('rutinas')
                ->cascadeOnDelete();

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------
            | VIGENCIA
            |--------------------------------------------------------------
            */

            $table->date('fecha_inicio')
                ->nullable();

            $table->date('fecha_fin')
                ->nullable();

            $table->date('fecha_revision')
                ->nullable();

            /*
            |--------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------
            */

            $table->boolean('activa')
                ->default(true);

            $table->text('observaciones')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------
            | ÍNDICES Y REGLAS
            |--------------------------------------------------------------
            */

            $table->index([
                'cliente_id',
                'activa',
            ]);

            $table->index([
                'rutina_id',
                'activa',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutina_asignaciones');
    }
};
