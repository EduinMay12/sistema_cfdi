<?php

namespace App\Http\Controllers;

use App\Models\SolicitudDescarga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\Sat\SatRequestService;
use Illuminate\Support\Facades\Log;

class SolicitudDescargaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $solicitudes = SolicitudDescarga::with([
                'contribuyente',
                'paquetes',
            ])
            ->whereHas('contribuyente', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        return view('solicitudes.index', [
            'contribuyentes' => $user->contribuyentes()
                ->where('activo', true)
                ->orderBy('razon_social')
                ->get(),

            'solicitudes' => $solicitudes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contribuyente_id' => [
                'required',
                'integer',
                Rule::exists('contribuyentes', 'id')
                    ->where('user_id', $request->user()->id)
                    ->where('activo', true),
            ],
            'tipo' => [
                'required',
                Rule::in(['emitidos', 'recibidos']),
            ],
            'formato' => [
                'required',
                Rule::in(['xml', 'metadata']),
            ],
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        if (strtotime($data['fecha_fin']) > time()) {
            throw ValidationException::withMessages([
                'fecha_fin' => 'La fecha final no puede ser futura.',
            ]);
        }

        $solicitud = SolicitudDescarga::create([
            ...$data,
            'estado' => 'pendiente',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud registrada localmente. Aún no se ha enviado al SAT.',
            'solicitud_id' => $solicitud->id,
        ], 201);
    }

    public function enviarSat(
        Request $request,
        SolicitudDescarga $solicitud,
        SatRequestService $sat
    ) {
        $user = $request->user();

        $solicitud->load('contribuyente');

        abort_unless(
            $solicitud->contribuyente?->user_id === $user->id,
            403
        );

        $datos = $request->validate([
            'password_fiel' => ['required', 'string'],
        ]);

        if ($solicitud->estado !== 'pendiente') {
            return response()->json([
                'message' => 'La solicitud ya no está pendiente de envío.',
            ], 409);
        }

        // Bloqueo para evitar envíos simultáneos de la misma solicitud.
        $actualizada = SolicitudDescarga::whereKey($solicitud->id)
            ->where('estado', 'pendiente')
            ->update(['estado' => 'enviando']);

        if (! $actualizada) {
            return response()->json([
                'message' => 'La solicitud ya está siendo procesada.',
            ], 409);
        }

        try {
            $idSat = $sat->solicitar(
                $solicitud->contribuyente,
                $datos['password_fiel'],
                $solicitud->fecha_inicio,
                $solicitud->fecha_fin,
                $solicitud->tipo,
                $solicitud->formato
            );

            $solicitud->update([
                'solicitud_sat_id' => $idSat,
                'estado' => 'solicitada',
                'mensaje_sat' => 'Solicitud aceptada por el SAT.',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente al SAT.',
                'solicitud_sat_id' => $idSat,
            ]);

        } catch (\Throwable $e) {

            Log::error('Error en solicitud SAT', [
                'solicitud_id' => $solicitud->id,
                'exception' => get_class($e),
            ]);

            // Un fallo de comunicación puede ocurrir después de que
            // el SAT haya recibido la petición. No reintentar a ciegas.
            $solicitud->update([
                'estado' => 'revision',
                'mensaje_sat' => 'No se pudo confirmar el resultado del envío.',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo confirmar el envío. Revisa el estado antes de reintentar.',
            ], 502);
        }
    }
}