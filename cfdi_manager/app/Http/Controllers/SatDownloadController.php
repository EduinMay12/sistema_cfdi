<?php

namespace App\Http\Controllers;

use App\Models\PaqueteSat;
use App\Services\Sat\SatDownloadService;
use App\Services\Sat\CfdiXmlProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SatDownloadController extends Controller
{
    public function descargar(
        Request $request,
        PaqueteSat $paquete,
        SatDownloadService $sat,
        CfdiXmlProcessor $processor
    ) {
        $paquete->load('solicitud.contribuyente');

        abort_unless(
            (int) $paquete->solicitud?->contribuyente?->user_id
                === (int) $request->user()->id,
            403
        );

        $datos = $request->validate([
            'password_fiel' => ['required', 'string'],
        ]);

        if (! $paquete->solicitud->solicitud_sat_id) {
            return response()->json([
                'message' => 'El paquete no tiene una solicitud SAT válida.',
            ], 422);
        }

        try {
            $ruta = $sat->descargar(
                $paquete,
                $datos['password_fiel']
            );

            $procesados = null;

            if ($paquete->solicitud->formato === 'xml') {
                $procesados = $processor->procesar($paquete);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paquete descargado correctamente.',
                'archivo' => basename($ruta),
                'cfdis_procesados' => $procesados,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error descargando paquete SAT', [
                'paquete_id' => $paquete->id,
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'No fue posible descargar o procesar el paquete.',
            ], 502);
        }
    }
}