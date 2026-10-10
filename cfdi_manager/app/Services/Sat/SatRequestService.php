<?php

namespace App\Services\Sat;

use App\Models\Contribuyente;
use PhpCfdi\SatWsDescargaMasiva\Service;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\FielRequestBuilder;
use PhpCfdi\SatWsDescargaMasiva\WebClient\GuzzleWebClient;
use PhpCfdi\SatWsDescargaMasiva\Services\Query\QueryParameters;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTimePeriod;
use PhpCfdi\SatWsDescargaMasiva\Shared\DownloadType;
use PhpCfdi\SatWsDescargaMasiva\Shared\RequestType;
use PhpCfdi\SatWsDescargaMasiva\Shared\DocumentStatus;
use InvalidArgumentException;
use RuntimeException;

class SatRequestService
{
    public function __construct(
        private SatAuthenticationService $authentication
    ) {}

    public function crearServicio(
        Contribuyente $contribuyente,
        string $password
    ): Service {
        $fiel = $this->authentication->crearFiel(
            $contribuyente,
            $password
        );

        return new Service(
            new FielRequestBuilder($fiel),
            new GuzzleWebClient()
        );
    }

    public function solicitar(
        Contribuyente $contribuyente,
        string $password,
        string $fechaInicio,
        string $fechaFin,
        string $tipo,
        string $formato
    ): string {

        if (! in_array($tipo, ['emitidos', 'recibidos'], true)) {
            throw new InvalidArgumentException('Tipo de descarga inválido.');
        }

        if (! in_array($formato, ['xml', 'metadata'], true)) {
            throw new InvalidArgumentException('Formato de descarga inválido.');
        }

        $periodo = DateTimePeriod::createFromValues(
            $fechaInicio . ' 00:00:00',
            $fechaFin . ' 23:59:59'
        );

        $tipoDescarga = $tipo === 'emitidos'
            ? DownloadType::issued()
            : DownloadType::received();

        $tipoSolicitud = $formato === 'xml'
            ? RequestType::xml()
            : RequestType::metadata();

        $parametros = QueryParameters::create($periodo)
            ->withDownloadType($tipoDescarga)
            ->withRequestType($tipoSolicitud);

        // Restricción del servicio SAT 1.5:
        // XML recibidos requiere documentos vigentes.
        if ($tipo === 'recibidos' && $formato === 'xml') {
            $parametros = $parametros->withDocumentStatus(
                DocumentStatus::active()
            );
        }

        $errores = $parametros->validate();

        if ($errores !== []) {
            throw new InvalidArgumentException(
                implode('; ', $errores)
            );
        }

        $servicio = $this->crearServicio(
            $contribuyente,
            $password
        );

        $respuesta = $servicio->query($parametros);

        if (! $respuesta->getStatus()->isAccepted()) {
            throw new RuntimeException(
                $respuesta->getStatus()->getMessage()
            );
        }

        $idSolicitud = $respuesta->getRequestId();

        if (! $idSolicitud) {
            throw new RuntimeException(
                'El SAT aceptó la operación, pero no devolvió un identificador.'
            );
        }

        return $idSolicitud;
    }
}