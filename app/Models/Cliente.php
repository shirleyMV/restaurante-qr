<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación: Un cliente puede tener muchos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}