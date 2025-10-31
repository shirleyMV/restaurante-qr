<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
        'disponible'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
        'stock' => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class);
    }

    // NUEVA: Obtener URL de imagen desde public
    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) {
            return null;
        }
        return asset($this->imagen);
    }

    /**
     * Verificar si el producto está agotado
     */
    public function estaAgotado(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Verificar si hay stock suficiente
     */
    public function tieneStock(int $cantidad = 1): bool
    {
        return $this->stock >= $cantidad;
    }

    /**
     * Descontar stock del producto
     */
    public function descontarStock(int $cantidad): bool
    {
        if (!$this->tieneStock($cantidad)) {
            return false;
        }

        $this->stock -= $cantidad;
        $this->save();

        return true;
    }

    /**
     * Aumentar stock del producto
     */
    public function aumentarStock(int $cantidad): void
    {
        $this->stock += $cantidad;
        $this->save();
    }
}