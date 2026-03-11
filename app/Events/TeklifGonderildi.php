<?php

namespace App\Events;

use App\Models\Teklif;
use App\Models\Mesaj;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeklifGonderildi implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Teklif $teklif;
    public Mesaj $mesaj;

    public function __construct(Teklif $teklif, Mesaj $mesaj)
    {
        $this->teklif = $teklif;
        $this->mesaj = $mesaj;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('konusma.' . $this->teklif->konusma_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'teklif' => [
                'id' => $this->teklif->id,
                'tutar' => $this->teklif->formatli_tutar,
                'tutar_raw' => $this->teklif->tutar,
                'durum' => $this->teklif->durum,
                'teklif_eden_id' => $this->teklif->teklif_eden_id,
            ],
            'mesaj' => [
                'id' => $this->mesaj->id,
                'konusma_id' => $this->mesaj->konusma_id,
                'gonderen_id' => $this->mesaj->gonderen_id,
                'gonderen_ad' => $this->mesaj->gonderen->ad,
                'mesaj' => $this->mesaj->mesaj,
                'tip' => $this->mesaj->tip,
                'created_at' => $this->mesaj->created_at->toISOString(),
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'teklif.gonderildi';
    }
}
