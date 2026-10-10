<?php

namespace App\Http\Controllers;

use App\Models\Cfdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use ZipArchive;

class CfdiController extends Controller
{
    private function cfdisDelUsuario(Request $request)
    {
        return Cfdi::query()
            ->whereHas('contribuyente', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $contribuyentes = $user->contribuyentes()
            ->orderBy('razon_social')
            ->get();

        $query = $this->cfdisDelUsuario($request)
            ->with('contribuyente');

        if ($request->filled('contribuyente_id')) {
            $query->where(
                'contribuyente_id',
                $request->integer('contribuyente_id')
            );
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate(
                'fecha_emision',
                '>=',
                $request->fecha_inicio
            );
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate(
                'fecha_emision',
                '<=',
                $request->fecha_fin
            );
        }

        $cfdis = $query->latest()->get();

        return view('cfdis.index', compact(
            'cfdis',
            'contribuyentes'
        ));
    }

    public function descargarXml(Request $request, Cfdi $cfdi)
    {
        $cfdi = $this->cfdisDelUsuario($request)
            ->findOrFail($cfdi->id);

        if (
            ! $cfdi->xml_path ||
            ! Storage::disk('local')->exists($cfdi->xml_path)
        ) {
            abort(404, 'No se encontró el archivo XML.');
        }

        return Storage::disk('local')->download(
            $cfdi->xml_path,
            $cfdi->uuid . '.xml',
            ['Content-Type' => 'application/xml']
        );
    }

    public function descargarMasivo(Request $request)
    {
        $data = $request->validate([
            'cfdis' => ['required', 'array', 'min:1', 'max:200'],
            'cfdis.*' => ['required', 'integer', 'distinct'],
        ]);

        $ids = $data['cfdis'];

        $cfdis = $this->cfdisDelUsuario($request)
            ->whereIn('id', $ids)
            ->get();

        if ($cfdis->count() !== count($ids)) {
            return back()->withErrors([
                'cfdis' => 'Hay CFDI seleccionados que no están disponibles.',
            ]);
        }

        $zip = new ZipArchive();

        $temporal = tempnam(sys_get_temp_dir(), 'cfdi_');

        if ($temporal === false) {
            abort(500, 'No se pudo crear el archivo temporal.');
        }

        if (
            $zip->open($temporal, ZipArchive::CREATE | ZipArchive::OVERWRITE)
            !== true
        ) {
            @unlink($temporal);
            abort(500, 'No se pudo crear el ZIP.');
        }

        $agregados = 0;

        foreach ($cfdis as $cfdi) {
            if (
                ! $cfdi->xml_path ||
                ! Storage::disk('local')->exists($cfdi->xml_path)
            ) {
                continue;
            }

            $ruta = Storage::disk('local')->path($cfdi->xml_path);

            if ($zip->addFile($ruta, $cfdi->uuid . '.xml')) {
                $agregados++;
            }
        }

        $zip->close();

        if ($agregados === 0) {
            @unlink($temporal);

            return back()->withErrors([
                'cfdis' => 'Ninguno de los CFDI seleccionados tiene un XML disponible.',
            ]);
        }

        return response()->download(
            $temporal,
            'CFDI_Masivos_' . now()->format('Ymd_His') . '.zip',
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);
    }
}