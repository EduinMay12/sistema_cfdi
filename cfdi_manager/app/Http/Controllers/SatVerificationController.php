<?php

namespace App\Http\Controllers;

use App\Models\PaqueteSat;
use App\Models\SolicitudDescarga;
use App\Services\Sat\SatVerifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SatVerificationController extends Controller
{
    public function verificar(
        Request $request,
        SolicitudDescarga $solicitud,
        SatVerifyService $sat
    ) {
        $solicitud->load('contribuyente');

        abort_unless(
            (int) $solicitud->contribuyente?->user_id
                === (int) $request->user()->id,
            403
        );

        $datos = $request->validate([
            'password_fiel' => ['required', 'string'],
        ]);

        if (! $solicitud->solicitud_sat_id) {
            return response()->json([
                'message' => 'La solicitud aún no fue aceptada por el SAT.',
            ], 422);
        }

        try {
            $resultado = $sat->verificar(
                $solicitud,
                $datos['password_fiel']
            );

            if (! $resultado['aceptada']) {
                return response()->json([
                    'message' => $resultado['mensaje'],
                ], 422);
            }

            DB::transaction(function () use ($solicitud, $resultado) {
                foreach ($resultado['paquetes'] as $paqueteId) {
                    PaqueteSat::firstOrCreate([
                        'solicitud_descarga_id' => $solicitud->id,
                        'paquete_sat_id' => $paqueteId,
                    ]);
                }

                $solicitud->update([
                    'estado' => $resultado['estado_solicitud'],
                    'mensaje_sat' => $resultado['mensaje'],
                    'ultima_verificacion' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Verificación realizada.',
                'estado' => $resultado['estado_solicitud'],
                'paquetes' => count($resultado['paquetes']),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error verificando solicitud SAT', [
                'solicitud_id' => $solicitud->id,
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'No se pudo verificar la solicitud ante el SAT.',
            ], 502);
        }
    }
}