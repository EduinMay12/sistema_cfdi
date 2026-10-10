<?php

namespace App\Http\Controllers;

use App\Models\Cfdi;
use App\Models\SolicitudDescarga;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $year = (int) $request->query('year', now()->year);

        abort_unless(
            $year >= 2020 && $year <= now()->year + 1,
            422,
            'Ejercicio fiscal inválido.'
        );

        $contribuyentes = $user->contribuyentes()
            ->orderBy('razon_social')
            ->get();

        $contribuyenteId = $request->filled('contribuyente_id')
            ? $request->integer('contribuyente_id')
            : null;

        if ($contribuyenteId !== null) {
            abort_unless(
                $contribuyentes->contains('id', $contribuyenteId),
                403
            );
        }

        $query = Cfdi::query()
            ->whereHas('contribuyente', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereYear('fecha_emision', $year);

        if ($contribuyenteId !== null) {
            $query->where('contribuyente_id', $contribuyenteId);
        }

        $base = clone $query;

        // Clasificación por RFC del contribuyente.
        $emitidos = (clone $query)
            ->whereHas('contribuyente', function ($q) {
                $q->whereColumn('contribuyentes.rfc', 'cfdis.rfc_emisor');
            });

        $recibidos = (clone $query)
            ->whereHas('contribuyente', function ($q) {
                $q->whereColumn('contribuyentes.rfc', 'cfdis.rfc_receptor');
            });

        $resumen = [
            'total_cfdis' => (clone $base)->count(),
            'emitidos' => (clone $emitidos)->count(),
            'recibidos' => (clone $recibidos)->count(),
            'ingresos' => (clone $emitidos)
                ->where('tipo_comprobante', 'I')
                ->sum('total'),
            'egresos' => (clone $recibidos)
                ->where('tipo_comprobante', 'I')
                ->sum('total'),
        ];

        $resumen['diferencia'] =
            $resumen['ingresos'] - $resumen['egresos'];

        $movimientos = (clone $base)
            ->selectRaw('MONTH(fecha_emision) as mes')
            ->selectRaw('COUNT(*) as cantidad')
            ->groupByRaw('MONTH(fecha_emision)')
            ->pluck('cantidad', 'mes');

        $meses = collect(range(1, 12));

        $grafica = [
            'labels' => [
                'Ene', 'Feb', 'Mar', 'Abr',
                'May', 'Jun', 'Jul', 'Ago',
                'Sep', 'Oct', 'Nov', 'Dic'
            ],
            'cfdis' => $meses
                ->map(fn ($mes) => (int) ($movimientos[$mes] ?? 0))
                ->values(),
        ];

        $solicitudesQuery = SolicitudDescarga::query()
            ->whereHas('contribuyente', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        if ($contribuyenteId !== null) {
            $solicitudesQuery->where(
                'contribuyente_id',
                $contribuyenteId
            );
        }

        $solicitudes = $solicitudesQuery
            ->with('contribuyente')
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'resumen',
            'grafica',
            'solicitudes',
            'contribuyentes',
            'year',
            'contribuyenteId'
        ));
    }
}