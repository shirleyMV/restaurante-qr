<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'pedido_id',
        'metodo_pago_id',
        'monto',
        'codigo_transaccion',
        'estado',
        'fecha_pago'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    // Relación: Un pago pertenece a un pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación: Un pago pertenece a un método de pago
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
}