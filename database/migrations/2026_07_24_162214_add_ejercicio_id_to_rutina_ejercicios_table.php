<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutina_ejercicios', function (Blueprint $table) {
            $table->foreignId('ejercicio_id')
                ->nullable()
                ->after('rutina_dia_id')
                ->constrained('ejercicios')
                ->nullOnDelete();

            $table->index([
                'ejercicio_id',
                'activo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rutina_ejercicios', function (Blueprint $table) {
            $table->dropForeign([
                'ejercicio_id',
            ]);

            $table->dropIndex([
                'ejercicio_id',
                'activo',
            ]);

            $table->dropColumn('ejercicio_id');
        });
    }
};
