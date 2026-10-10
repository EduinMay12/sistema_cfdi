<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cfdi extends Model
{
     protected $fillable = [
        'contribuyente_id',
        'uuid',
        'rfc_emisor',
        'rfc_receptor',
        'fecha_emision',
        'subtotal',
        'total',
        'tipo_comprobante',
        'estado_sat',
        'xml_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function contribuyente()
    {
        return $this->belongsTo(
            Contribuyente::class
        );
    }
}