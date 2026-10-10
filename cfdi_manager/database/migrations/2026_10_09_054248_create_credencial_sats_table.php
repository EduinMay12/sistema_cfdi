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
        Schema::create('credenciales_sat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contribuyente_id')
                ->unique()
                ->constrained('contribuyentes')
                ->cascadeOnDelete();

            $table->longText('certificado_cifrado');
            $table->longText('llave_cifrada');

            $table->string('numero_certificado')->nullable();
            $table->timestamp('vigencia_hasta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credencial_sats');
    }
};
