<?php

namespace App\Notifications;

use App\Models\Mesaj;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YeniMesajBildirimi extends Notification
{
    use Queueable;

    public function __construct(
        public Mesaj $mesaj,
        public User $gonderen
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
            'baslik' => 'Yeni Mesaj',
            'mesaj' => $this->gonderen->ad_soyad . ' size yeni bir mesaj gönderdi',
            'gonderen_id' => $this->gonderen->id,
            'gonderen_adi' => $this->gonderen->ad_soyad,
            'mesaj_id' => $this->mesaj->id,
            'konusma_id' => $this->mesaj->konusma_id,
            'ikon' => 'message',
            'link' => '/sohbet/' . $this->mesaj->konusma_id,
        ];
    }
}
