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
            $table->string('pantalla')
                ->default('inicio')
                ->after('destinatario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificaciones_push', function (Blueprint $table) {
            $table->dropColumn('pantalla');
        });
    }
};
