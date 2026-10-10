<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaqueteSat extends Model
{
    protected $table = 'paquetes_sat';

    protected $fillable = [
        'solicitud_descarga_id',
        'paquete_sat_id',
        'estado',
        'archivo_path',
    ];

    public function solicitud()
    {
        return $this->belongsTo(
            SolicitudDescarga::class,
            'solicitud_descarga_id'
        );
    }
}