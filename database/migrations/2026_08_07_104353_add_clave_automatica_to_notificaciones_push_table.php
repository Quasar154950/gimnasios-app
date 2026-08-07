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
        Schema::table('notificaciones_push', function (Blueprint $table) {
            $table->string('clave_automatica')
                ->nullable()
                ->unique()
                ->after('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones_push', function (Blueprint $table) {
            $table->dropUnique(
                'notificaciones_push_clave_automatica_unique'
            );

            $table->dropColumn('clave_automatica');
        });
    }
};
