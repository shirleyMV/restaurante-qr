<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $fillable = [
        'nombre',
        'activo',
        'descripcion'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación: Un método de pago puede tener muchos pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}