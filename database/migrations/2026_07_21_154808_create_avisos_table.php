<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('abogado_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('titulo');
            $table->text('mensaje');

            $table->string('prioridad')
                ->default('informativo');

            $table->dateTime('fecha_publicacion')
                ->nullable();

            $table->dateTime('fecha_vencimiento')
                ->nullable();

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->index([
                'abogado_id',
                'activo',
                'fecha_publicacion',
                'fecha_vencimiento',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
