<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('origen')
                ->default('manual')
                ->after('metodo_pago');

            $table->string('estado')
                ->default('aprobado')
                ->after('origen');

            $table->string('mercadopago_payment_id')
                ->nullable()
                ->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn([
                'origen',
                'estado',
                'mercadopago_payment_id',
            ]);
        });
    }
};
