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
        Schema::create('cfdis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contribuyente_id')
                ->constrained('contribuyentes')
                ->cascadeOnDelete();

            $table->uuid('uuid');
            $table->string('rfc_emisor', 13);
            $table->string('rfc_receptor', 13);

            $table->dateTime('fecha_emision');

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            $table->string('tipo_comprobante', 5)->nullable();
            $table->string('estado_sat')->nullable();

            $table->string('xml_path')->nullable();

            $table->timestamps();

            $table->unique(['contribuyente_id', 'uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cfdis');
    }
};
