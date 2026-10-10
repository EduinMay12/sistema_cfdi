<?php

namespace App\Http\Controllers;

use App\Models\Contribuyente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContribuyenteController extends Controller
{
    public function index()
    {
        return view('contribuyentes.index', [
            'contribuyentes' => auth()->user()
                ->contribuyentes()
                ->latest()
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rfc' => [
                'required',
                'string',
                'regex:/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/',
                Rule::unique('contribuyentes', 'rfc')
            ],
            'razon_social' => 'required|string|max:255',
            'correo' => 'nullable|email|max:255',
        ]);

        $data['rfc'] = strtoupper(trim($data['rfc']));

        $contribuyente = DB::transaction(function () use ($data) {
            $contribuyente = new Contribuyente($data);
            $contribuyente->user_id = auth()->id();
            $contribuyente->activo = true;
            $contribuyente->save();

            return $contribuyente;
        });

        return response()->json([
            'success' => true,
            'message' => 'Contribuyente registrado correctamente',
            'data' => $contribuyente
        ], 201);
    }

    public function show(Contribuyente $contribuyente)
    {
        abort_unless(
            $contribuyente->user_id === auth()->id(),
            403
        );

        return response()->json($contribuyente);
    }

    public function update(
        Request $request,
        Contribuyente $contribuyente
    ) {
        abort_unless(
            $contribuyente->user_id === auth()->id(),
            403
        );

        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'correo' => 'nullable|email|max:255',
            'activo' => 'required|boolean'
        ]);

        $contribuyente->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contribuyente actualizado'
        ]);
    }

    public function destroy(Contribuyente $contribuyente)
    {
        abort_unless(
            $contribuyente->user_id === auth()->id(),
            403
        );

        // Desactivar sin borrar historial fiscal.
        $contribuyente->update([
            'activo' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contribuyente desactivado'
        ]);
    }
}