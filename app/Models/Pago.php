<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected static function boot()
    {
        parent::boot();

        // Auto-confirmar cuando se marca como completado
        static::updating(function ($pago) {
            if ($pago->isDirty('estado') && $pago->estado === 'completado' && !$pago->confirmado_por) {
                $pago->confirmado_por = auth()->id();
                $pago->fecha_confirmacion = now();
            }
        });
    }

    protected $fillable = [
        'pedido_id',
        'metodo_pago_id',
        'monto',
        'codigo_transaccion',
        'comprobante',
        'estado',
        'fecha_pago',
        'confirmado_por',
        'fecha_confirmacion',
        'notas',
        'activo'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'activo' => 'boolean',
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

    // Relación: Un pago fue confirmado por un usuario
    public function confirmadoPor()
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }

    /**
     * Verificar si el pago está confirmado
     */
    public function estaConfirmado(): bool
    {
        return $this->estado === 'completado' && $this->confirmado_por !== null;
    }

    /**
     * Confirmar el pago
     */
    public function confirmar(int $usuarioId, ?string $notas = null): void
    {
        $this->update([
            'estado' => 'completado',
            'confirmado_por' => $usuarioId,
            'fecha_confirmacion' => now(),
            'notas' => $notas,
        ]);
    }
}