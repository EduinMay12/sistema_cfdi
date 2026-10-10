<?php

namespace App\Http\Controllers;

use App\Models\SolicitudDescarga;
use App\Models\PaqueteSat;
use Illuminate\Http\Request;

class SatOperationsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $solicitudesQuery = SolicitudDescarga::query()
            ->whereHas('contribuyente', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        $paquetesQuery = PaqueteSat::query()
            ->whereHas('solicitud.contribuyente', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        $estadisticas = [
            'solicitudes' => (clone $solicitudesQuery)->count(),

            'seguimiento' => (clone $solicitudesQuery)
                ->whereIn('estado', [
                    'pendiente',
                    'enviando',
                    'solicitada',
                    'revision',
                ])
                ->count(),

            'paquetes' => (clone $paquetesQuery)->count(),

            'procesados' => (clone $paquetesQuery)
                ->where('estado', 'procesado')
                ->count(),
        ];

        $solicitudes = (clone $solicitudesQuery)
            ->with(['contribuyente', 'paquetes'])
            ->latest()
            ->paginate(10);

        return view('sat.operaciones', compact(
            'estadisticas',
            'solicitudes'
        ));
    }
}