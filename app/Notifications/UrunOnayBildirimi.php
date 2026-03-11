<?php

namespace App\Notifications;

use App\Models\Urun;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UrunOnayBildirimi extends Notification
{
    use Queueable;

    public function __construct(
        public Urun $urun,
        public string $durum, // 'onaylandi' veya 'reddedildi'
        public ?string $redNedeni = null
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
        $onaylandi = $this->durum === 'onaylandi';

        $mesaj = $onaylandi
            ? '"' . $this->urun->urun_adi . '" has been approved and is now live.'
            : '"' . $this->urun->urun_adi . '" has been rejected.';

        // Red nedeni varsa mesaja ekle
        if (!$onaylandi && $this->redNedeni) {
            $mesaj .= ' Reason: ' . $this->redNedeni;
        }

        return [
            'baslik' => $onaylandi ? 'Product Approved' : 'Product Rejected',
            'mesaj' => $mesaj,
            'urun_id' => $this->urun->id,
            'urun_baslik' => $this->urun->urun_adi,
            'durum' => $this->durum,
            'red_nedeni' => $this->redNedeni,
            'ikon' => $onaylandi ? 'check-circle' : 'x-circle',
            'link' => '/seller/products',
        ];
    }
}
