<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paquetes_sat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_descarga_id')
                ->constrained('solicitudes_descarga')
                ->cascadeOnDelete();

            $table->string('paquete_sat_id', 255);

            $table->string('estado', 30)
                ->default('pendiente');

            $table->string('archivo_path')->nullable();

            $table->timestamps();

            $table->unique([
                'solicitud_descarga_id',
                'paquete_sat_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paquetes_sat');
    }
};