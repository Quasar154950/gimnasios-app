<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('mercadopago_refresh_token')->nullable();
            $table->string('mercadopago_user_id')->nullable();
            $table->timestamp('mercadopago_token_expires_at')->nullable();
            $table->timestamp('mercadopago_connected_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mercadopago_refresh_token',
                'mercadopago_user_id',
                'mercadopago_token_expires_at',
                'mercadopago_connected_at',
            ]);
        });
    }
};
