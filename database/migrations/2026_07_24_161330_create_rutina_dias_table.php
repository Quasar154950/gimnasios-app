<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutina_dias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rutina_id')
                ->constrained('rutinas')
                ->cascadeOnDelete();

            $table->string('nombre');

            $table->text('descripcion')
                ->nullable();

            $table->unsignedInteger('orden')
                ->default(1);

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->index([
                'rutina_id',
                'orden',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutina_dias');
    }
};
