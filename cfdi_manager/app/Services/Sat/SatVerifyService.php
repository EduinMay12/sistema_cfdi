<?php

namespace App\Services\Sat;

use App\Models\SolicitudDescarga;
use App\Services\Sat\SatRequestService;
use RuntimeException;

class SatVerifyService
{
    public function __construct(
        private SatRequestService $satRequest
    ) {}

    public function verificar(
        SolicitudDescarga $solicitud,
        string $password
    ): array {
        if (! $solicitud->solicitud_sat_id) {
            throw new RuntimeException(
                'La solicitud no tiene identificador del SAT.'
            );
        }

        $servicio = $this->satRequest->crearServicio(
            $solicitud->contribuyente,
            $password
        );

        $respuesta = $servicio->verify(
            $solicitud->solicitud_sat_id
        );

        return [
            'aceptada' => $respuesta->getStatus()->isAccepted(),
            'mensaje' => $respuesta->getStatus()->getMessage(),
            'estado_solicitud' => $respuesta->getStatusRequest()->getValue(),
            'paquetes' => $respuesta->getPackagesIds(),
        ];
    }
}