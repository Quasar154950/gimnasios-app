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
        Schema::create('notificaciones_push', function (Blueprint $table) {

            $table->id();

            // Gimnasio (owner)
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Destinatario individual (opcional)
            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Contenido
            $table->string('titulo');
            $table->text('mensaje');

            // manual | automatica
            $table->string('tipo')->default('manual');

            // todos | cliente | cuota_vencida | reserva | etc.
            $table->string('destinatario')->default('todos');

            // borrador | pendiente | enviada | error
            $table->string('estado')->default('pendiente');

            // Se programa para más adelante
            $table->timestamp('programada_para')->nullable();

            // Fecha real de envío
            $table->timestamp('enviada_at')->nullable();

            // Cantidad de dispositivos alcanzados
            $table->unsignedInteger('cantidad_enviada')->default(0);

            // Error general (si ocurrió)
            $table->text('error')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones_push');
    }
};
