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
        Schema::table('users', function (Blueprint $table) {

            $table->boolean('mercadopago_enabled')
                ->default(false);

            $table->text('mercadopago_public_key')
                ->nullable();

            $table->text('mercadopago_access_token')
                ->nullable();

            $table->boolean('mercadopago_sandbox')
                ->default(true);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'mercadopago_enabled',
                'mercadopago_public_key',
                'mercadopago_access_token',
                'mercadopago_sandbox',
            ]);

        });
    }
};
