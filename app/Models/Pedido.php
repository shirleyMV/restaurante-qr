<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'mesa_id',
        'cliente_id',
        'total',
        'estado',
        'metodo_pago',
        'fecha_pedido',
        'activo'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_pedido' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relación: Un pedido pertenece a una mesa
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }
    
    // Relación: Un pedido pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación: Un pedido tiene muchos detalles (productos)
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    // Relación: Un pedido puede tener muchos pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    // Calcular el total del pedido
    public function calcularTotal()
    {
        $this->total = $this->detalles->sum('subtotal');
        $this->save();
    }
}