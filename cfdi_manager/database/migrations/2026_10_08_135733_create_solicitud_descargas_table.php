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
        Schema::create('solicitudes_descarga', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contribuyente_id')
                ->constrained('contribuyentes')
                ->cascadeOnDelete();

            $table->string('solicitud_sat_id')->nullable();

            $table->enum('tipo', ['emitidos', 'recibidos']);
            $table->enum('formato', ['xml', 'metadata']);

            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');

            $table->string('estado')->default('pendiente');

            $table->text('mensaje_sat')->nullable();
            $table->timestamp('ultima_verificacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_descargas');
    }
};
