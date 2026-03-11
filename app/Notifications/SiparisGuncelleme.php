<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SiparisGuncelleme extends Notification
{
    use Queueable;

    public function __construct(
        public int $siparisId,
        public string $siparisDurumu,
        public string $takipNo = ''
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
        $durumMesajlari = [
            'hazirlaniyor' => 'Siparişiniz hazırlanıyor',
            'kargoda' => 'Siparişiniz kargoya verildi',
            'teslim_edildi' => 'Siparişiniz teslim edildi',
            'iptal_edildi' => 'Siparişiniz iptal edildi',
        ];

        return [
            'baslik' => 'Sipariş Güncelleme',
            'mesaj' => $durumMesajlari[$this->siparisDurumu] ?? 'Sipariş durumunuz güncellendi',
            'siparis_id' => $this->siparisId,
            'siparis_durumu' => $this->siparisDurumu,
            'takip_no' => $this->takipNo,
            'ikon' => 'truck',
            'link' => '/siparislerim/' . $this->siparisId,
        ];
    }
}
