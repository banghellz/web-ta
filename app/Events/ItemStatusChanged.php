<?php

// app/Events/ItemStatusChanged.php
namespace App\Events;

use App\Models\Item;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;
    public $previousStatus;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Item $item, $previousStatus, $user = null)
    {
        $this->item = $item;
        $this->previousStatus = $previousStatus;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('items'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'item' => [
                'id' => $this->item->id,
                'epc' => $this->item->epc,
                'nama_barang' => $this->item->nama_barang,
                'status' => $this->item->status,
                'user_id' => $this->item->user_id,
                'updated_at' => $this->item->updated_at->toISOString(),
            ],
            'previousStatus' => $this->previousStatus,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ItemStatusChanged';
    }
}
