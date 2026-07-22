<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novedades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('abogado_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('titulo');

            $table->text('descripcion');

            $table->string('tipo')
                ->default('novedad');

            $table->string('imagen')->nullable();

            $table->date('fecha_publicacion')->nullable();

            $table->boolean('activo')->default(true);

            $table->boolean('destacado')->default(false);

            $table->timestamps();

            $table->index([
                'abogado_id',
                'activo',
                'fecha_publicacion',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novedades');
    }
};
