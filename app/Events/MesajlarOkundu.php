<?php

namespace App\Events;

use App\Models\Konusma;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MesajlarOkundu implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Konusma $konusma;
    public array $mesajIds;

    public function __construct(Konusma $konusma, array $mesajIds)
    {
        $this->konusma = $konusma;
        $this->mesajIds = $mesajIds;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('konusma.' . $this->konusma->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'mesaj_ids' => $this->mesajIds,
            'okuyan_id' => auth()->id(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'mesajlar.okundu';
    }
}
