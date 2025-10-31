<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PedidoConfirmado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pedido;

    /**
     * Create a new event instance.
     */
    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('cocina'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'pedido.confirmado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'pedido_id' => $this->pedido->id,
            'mesa' => $this->pedido->mesa->numero ?? 'N/A',
            'total' => $this->pedido->total,
            'estado' => $this->pedido->estado,
            'mensaje' => 'Nuevo pedido confirmado - Mesa ' . ($this->pedido->mesa->numero ?? 'N/A'),
        ];
    }
}
