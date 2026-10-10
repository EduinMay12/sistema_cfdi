<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CredencialSat extends Model
{
    protected $table = 'credenciales_sat';

    protected $fillable = [
        'contribuyente_id',
        'certificado_cifrado',
        'llave_cifrada',
        'numero_certificado',
        'vigencia_hasta',
    ];

    protected $hidden = [
        'certificado_cifrado',
        'llave_cifrada',
    ];

    protected $casts = [
        'vigencia_hasta' => 'datetime',
    ];

    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class);
    }
}