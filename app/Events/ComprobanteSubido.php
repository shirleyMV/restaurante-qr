<?php

namespace App\Events;

use App\Models\Pago;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComprobanteSubido implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pago;

    /**
     * Create a new event instance.
     */
    public function __construct(Pago $pago)
    {
        $this->pago = $pago;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('cajera'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'comprobante.subido';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'pago_id' => $this->pago->id,
            'pedido_id' => $this->pago->pedido_id,
            'monto' => $this->pago->monto,
            'codigo_transaccion' => $this->pago->codigo_transaccion,
            'mesa' => $this->pago->pedido->mesa->numero ?? 'N/A',
            'mensaje' => 'Nuevo comprobante recibido de Mesa ' . ($this->pago->pedido->mesa->numero ?? 'N/A'),
        ];
    }
}
