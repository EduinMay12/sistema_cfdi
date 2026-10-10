<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudDescarga extends Model
{
    protected $table = 'solicitudes_descarga';

    protected $fillable = [
        'contribuyente_id',
        'solicitud_sat_id',
        'tipo',
        'formato',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'mensaje_sat',
        'ultima_verificacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'ultima_verificacion' => 'datetime',
        ];
    }

    public function contribuyente()
    {
        return $this->belongsTo(
            Contribuyente::class
        );
    }

    public function paquetes()
    {
        return $this->hasMany(
            PaqueteSat::class,
            'solicitud_descarga_id'
        );
    }
}