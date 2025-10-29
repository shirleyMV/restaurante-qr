<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;
    
    protected function afterCreate(): void
    {
        // Calcular el total y subtotal basado en los detalles
        $pedido = $this->record;
        
        foreach ($pedido->detalles as $detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            $detalle->save();
        }
        
        $total = $pedido->detalles->sum('subtotal');
        $pedido->update(['total' => $total]);
    }
}
