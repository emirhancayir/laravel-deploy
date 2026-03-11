<?php

namespace App\Notifications;

use App\Models\Yorum;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YeniYorumBildirimi extends Notification
{
    use Queueable;

    protected Yorum $yorum;
    protected string $durum; // 'yeni' veya 'onaylandi'

    public function __construct(Yorum $yorum, string $durum = 'yeni')
    {
        $this->yorum = $yorum;
        $this->durum = $durum;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $urun = $this->yorum->urun;
        $kullanici = $this->yorum->kullanici;

        if ($this->durum === 'onaylandi') {
            $mesaj = "{$kullanici->ad} ürününüze {$this->yorum->puan} yıldız verdi";
            $baslik = 'Yorum Onaylandı';
        } else {
            $mesaj = "{$kullanici->ad} ürününüze yorum yaptı (onay bekliyor)";
            $baslik = 'Yeni Yorum';
        }

        return [
            'type' => 'yorum',
            'title' => $baslik,
            'message' => $mesaj,
            'yorum_id' => $this->yorum->id,
            'urun_id' => $urun->id,
            'urun_adi' => $urun->urun_adi,
            'puan' => $this->yorum->puan,
            'yorumcu_id' => $kullanici->id,
            'yorumcu_adi' => $kullanici->ad,
            'durum' => $this->durum,
            'link' => route('products.show', $urun),
        ];
    }
}
