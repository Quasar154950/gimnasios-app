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
        Schema::create('saas_pagos', function (Blueprint $table) {

            $table->id();

            // CLIENTE SaaS
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // DATOS DEL PAGO
            $table->string('plan')->nullable();

            $table->integer('monto');

            $table->string('estado')
                ->default('pendiente');

            // MERCADO PAGO
            $table->string('payment_id')->nullable();

            $table->string('external_reference')->nullable();

            $table->string('metodo_pago')->nullable();

            // FECHAS
            $table->timestamp('fecha_pago')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_pagos');
    }
};
