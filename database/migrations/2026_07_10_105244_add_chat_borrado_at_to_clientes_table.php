<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->timestamp('chat_borrado_cliente_at')->nullable();
            $table->timestamp('chat_borrado_abogado_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'chat_borrado_cliente_at',
                'chat_borrado_abogado_at',
            ]);
        });
    }
};