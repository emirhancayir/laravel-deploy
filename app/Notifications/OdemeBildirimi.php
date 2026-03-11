<?php

namespace App\Notifications;

use App\Models\Odeme;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OdemeBildirimi extends Notification
{
    use Queueable;

    public function __construct(
        public Odeme $odeme,
        public string $tip = 'yeni' // yeni, onaylandi, iptal
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
        $mesajlar = [
            'yeni' => $this->odeme->alici->ad_soyad . ' ürününüz için ' . number_format($this->odeme->toplam_tutar, 2, ',', '.') . ' TL ödeme yaptı!',
            'onaylandi' => 'Ödemeniz onaylandı. Kargo hazırlanıyor.',
            'iptal' => 'Ödeme iptal edildi.',
        ];

        $basliklar = [
            'yeni' => 'Yeni Ödeme Alındı!',
            'onaylandi' => 'Ödeme Onaylandı',
            'iptal' => 'Ödeme İptal Edildi',
        ];

        return [
            'baslik' => $basliklar[$this->tip] ?? 'Ödeme Bildirimi',
            'mesaj' => $mesajlar[$this->tip] ?? 'Ödeme durumunuz güncellendi.',
            'odeme_id' => $this->odeme->id,
            'urun_id' => $this->odeme->urun_id,
            'urun_adi' => $this->odeme->urun->urun_adi ?? '',
            'tutar' => $this->odeme->toplam_tutar,
            'alici_id' => $this->odeme->alici_id,
            'alici_adi' => $this->odeme->alici->ad_soyad ?? '',
            'konusma_id' => $this->odeme->konusma_id,
            'ikon' => 'credit-card',
            'link' => '/sohbet/' . $this->odeme->konusma_id,
        ];
    }
}
