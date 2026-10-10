<?php

namespace App\Services\Sat;

use App\Models\Contribuyente;
use App\Models\CredencialSat;
use Illuminate\Support\Facades\Crypt;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\Fiel;
use RuntimeException;

class SatAuthenticationService
{
    public function crearFiel(
        Contribuyente $contribuyente,
        string $password
    ): Fiel {
        $registro = CredencialSat::where(
            'contribuyente_id',
            $contribuyente->id
        )->firstOrFail();

        $cer = base64_decode(
            Crypt::decryptString($registro->certificado_cifrado),
            true
        );

        $key = base64_decode(
            Crypt::decryptString($registro->llave_cifrada),
            true
        );

        if ($cer === false || $key === false) {
            throw new RuntimeException(
                'No se pudieron recuperar los archivos de e.firma.'
            );
        }

        $fiel = Fiel::create($cer, $key, $password);

        if (! $fiel->isValid()) {
            throw new RuntimeException(
                'La e.firma no es válida o está vencida.'
            );
        }

        if (strtoupper($fiel->getRfc()) !== strtoupper($contribuyente->rfc)) {
            throw new RuntimeException(
                'El RFC del certificado no coincide con el contribuyente.'
            );
        }

        return $fiel;
    }
}