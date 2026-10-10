<?php

namespace App\Services\Sat;

use App\Models\Cfdi;
use App\Models\PaqueteSat;
use Illuminate\Support\Facades\Storage;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\CfdiPackageReader;
use RuntimeException;
use SimpleXMLElement;

class CfdiXmlProcessor
{
    public function procesar(PaqueteSat $paquete): int
    {
        $paquete->loadMissing('solicitud.contribuyente');

        if ($paquete->solicitud->formato !== 'xml') {
            throw new RuntimeException(
                'Este paquete es de metadata, no de XML.'
            );
        }

        $rutaZip = $paquete->archivo_path;

        if (! $rutaZip || ! Storage::disk('local')->exists($rutaZip)) {
            throw new RuntimeException(
                'No existe el ZIP descargado.'
            );
        }

        $reader = CfdiPackageReader::createFromFile(
            Storage::disk('local')->path($rutaZip)
        );

        $contribuyenteId = $paquete->solicitud->contribuyente_id;

        $total = 0;

        foreach ($reader->cfdis() as $uuidPaquete => $contenido) {
            if (strlen($contenido) > 20 * 1024 * 1024) {
                throw new RuntimeException(
                    'El XML supera el tamaño permitido.'
                );
            }

            $xml = simplexml_load_string(
                $contenido,
                SimpleXMLElement::class,
                LIBXML_NONET
            );

            if ($xml === false) {
                continue;
            }

            $ns = $xml->getNamespaces(true);

            $comprobante = $xml->attributes();

            $emisor = $xml->children(
                $ns['cfdi'] ?? 'http://www.sat.gob.mx/cfd/4'
            )->Emisor;

            $receptor = $xml->children(
                $ns['cfdi'] ?? 'http://www.sat.gob.mx/cfd/4'
            )->Receptor;

            $timbre = $xml
                ->children($ns['cfdi'] ?? 'http://www.sat.gob.mx/cfd/4')
                ->Complemento
                ->children(
                    'http://www.sat.gob.mx/TimbreFiscalDigital'
                )
                ->TimbreFiscalDigital;

            $uuid = strtoupper(
                (string) $timbre['UUID']
            );

            if (! preg_match(
                '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/',
                $uuid
            )) {
                continue;
            }

            if (
                strtoupper((string) $uuidPaquete) !== $uuid
            ) {
                continue;
            }

            $rfcEmisor = strtoupper((string) $emisor['Rfc']);
            $rfcReceptor = strtoupper((string) $receptor['Rfc']);

            $rfcContribuyente = strtoupper(
                $paquete->solicitud->contribuyente->rfc
            );

            if (
                $rfcEmisor !== $rfcContribuyente
                && $rfcReceptor !== $rfcContribuyente
            ) {
                continue;
            }

            $rutaXml = "sat/xml/{$contribuyenteId}/{$uuid}.xml";

            Storage::disk('local')->put(
                $rutaXml,
                $contenido
            );

            Cfdi::updateOrCreate(
                [
                    'contribuyente_id' => $contribuyenteId,
                    'uuid' => $uuid,
                ],
                [
                    'rfc_emisor' => $rfcEmisor,
                    'rfc_receptor' => $rfcReceptor,
                    'fecha_emision' => (string) $comprobante['Fecha'],
                    'subtotal' => (string) $comprobante['SubTotal'],
                    'total' => (string) $comprobante['Total'],
                    'tipo_comprobante' => (string) $comprobante['TipoDeComprobante'],
                    'xml_path' => $rutaXml,
                ]
            );

            $total++;
        }

        $paquete->update([
            'estado' => 'procesado',
        ]);

        return $total;
    }
}