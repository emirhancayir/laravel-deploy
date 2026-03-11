<?php

namespace App\Notifications;

use App\Models\Teklif;
use App\Models\User;
use App\Models\Urun;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YeniTeklifBildirimi extends Notification
{
    use Queueable;

    public function __construct(
        public Teklif $teklif,
        public User $teklifEden,
        public Urun $urun
    ) {}

    /**
     * Bildirimin gonderilecegi kanallar
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Veritabaninda saklanacak data
     */
    public function toArray($notifiable): array
    {
        return [
            'baslik' => 'Yeni Teklif',
            'mesaj' => $this->teklifEden->ad_soyad . ' ürününüz için ' . number_format($this->teklif->tutar, 2, ',', '.') . ' TL teklif verdi',
            'teklif_eden_id' => $this->teklifEden->id,
            'teklif_eden_adi' => $this->teklifEden->ad_soyad,
            'teklif_id' => $this->teklif->id,
            'konusma_id' => $this->teklif->konusma_id,
            'urun_id' => $this->urun->id,
            'urun_baslik' => $this->urun->baslik,
            'tutar' => $this->teklif->tutar,
            'ikon' => 'offer',
            'link' => '/sohbet/' . $this->teklif->konusma_id,
        ];
    }
}
