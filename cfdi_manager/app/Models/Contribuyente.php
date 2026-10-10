<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    protected $fillable = [
        'rfc',
        'razon_social',
        'correo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudDescarga::class);
    }

    public function cfdis()
    {
        return $this->hasMany(Cfdi::class);
    }
}