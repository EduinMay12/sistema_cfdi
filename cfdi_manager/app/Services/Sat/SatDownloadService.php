<?php

namespace App\Services\Sat;

use App\Models\PaqueteSat;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class SatDownloadService
{
    public function __construct(
        private SatRequestService $satRequest
    ) {}

    public function descargar(
        PaqueteSat $paquete,
        string $password
    ): string {
        $paquete->loadMissing('solicitud.contribuyente');

        $solicitud = $paquete->solicitud;

        if (! $solicitud || ! $solicitud->solicitud_sat_id) {
            throw new RuntimeException(
                'El paquete no tiene una solicitud válida.'
            );
        }

        $servicio = $this->satRequest->crearServicio(
            $solicitud->contribuyente,
            $password
        );

        $respuesta = $servicio->download(
            $paquete->paquete_sat_id
        );

        if (! $respuesta->getStatus()->isAccepted()) {
            throw new RuntimeException(
                'El SAT rechazó la descarga del paquete.'
            );
        }

        $contenido = $respuesta->getPackageContent();

        if (! is_string($contenido) || $contenido === '') {
            throw new RuntimeException(
                'El SAT devolvió un paquete vacío.'
            );
        }

        $nombre = "sat/paquetes/{$paquete->id}.zip";

        Storage::disk('local')->put($nombre, $contenido);

        $ruta = Storage::disk('local')->path($nombre);

        $zip = new ZipArchive();

        if ($zip->open($ruta) !== true) {
            Storage::disk('local')->delete($nombre);

            throw new RuntimeException(
                'El paquete descargado no es un ZIP válido.'
            );
        }

        $zip->close();

        $paquete->update([
            'archivo_path' => $nombre,
            'estado' => 'descargado',
        ]);

        return $nombre;
    }
}