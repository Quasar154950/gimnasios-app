<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->date('fecha_nacimiento')
                ->nullable()
                ->after('dni');

            $table->decimal('peso', 5, 2)
                ->nullable()
                ->after('fecha_nacimiento');

            $table->unsignedSmallInteger('altura')
                ->nullable()
                ->after('peso');

            $table->string('contacto_emergencia')
                ->nullable()
                ->after('altura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_nacimiento',
                'peso',
                'altura',
                'contacto_emergencia',
            ]);
        });
    }
};
