<?php

namespace App\Jobs;

use App\Models\SolicitudDescarga;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcesarSolicitudSat implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $solicitudId
    ) {}

    public function handle(): void
    {
        $solicitud = SolicitudDescarga::findOrFail(
            $this->solicitudId
        );

        // Próxima implementación:
        // 1. Obtener e.firma autorizada
        // 2. Autenticarse con SAT
        // 3. Enviar solicitud SOAP firmada
        // 4. Guardar solicitud_sat_id
        // 5. Actualizar estado según respuesta real
    }
}