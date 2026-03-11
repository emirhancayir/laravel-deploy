<?php

namespace App\Events;

use App\Models\Mesaj;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MesajGonderildi implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Mesaj $mesaj;

    public function __construct(Mesaj $mesaj)
    {
        $this->mesaj = $mesaj;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('konusma.' . $this->mesaj->konusma_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'mesaj' => [
                'id' => $this->mesaj->id,
                'konusma_id' => $this->mesaj->konusma_id,
                'gonderen_id' => $this->mesaj->gonderen_id,
                'gonderen_ad' => $this->mesaj->gonderen->ad,
                'gonderen_resim' => $this->mesaj->gonderen->profil_resmi,
                'mesaj' => $this->mesaj->mesaj,
                'tip' => $this->mesaj->tip,
                'created_at' => $this->mesaj->created_at->toISOString(),
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'mesaj.gonderildi';
    }
}
