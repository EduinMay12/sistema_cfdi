<?php

namespace App\Http\Controllers;

use App\Models\Contribuyente;
use App\Models\CredencialSat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CredencialSatController extends Controller
{
    public function store(
        Request $request,
        Contribuyente $contribuyente
    ) {
        abort_unless(
            $contribuyente->user_id === auth()->id(),
            403
        );

        $request->validate([
            'certificado' => [
                'required',
                'file',
                'max:2048',
                'extensions:cer'
            ],
            'llave' => [
                'required',
                'file',
                'max:2048',
                'extensions:key'
            ],
        ]);

        $certificado = file_get_contents(
            $request->file('certificado')->getRealPath()
        );

        $llave = file_get_contents(
            $request->file('llave')->getRealPath()
        );

        DB::transaction(function () use (
            $contribuyente,
            $certificado,
            $llave
        ) {
            CredencialSat::updateOrCreate(
                [
                    'contribuyente_id' => $contribuyente->id
                ],
                [
                    'certificado_cifrado' =>
                        Crypt::encryptString(base64_encode($certificado)),

                    'llave_cifrada' =>
                        Crypt::encryptString(base64_encode($llave)),
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Archivos de e.firma almacenados de forma cifrada'
        ]);
    }
}