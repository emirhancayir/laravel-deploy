<?php

use App\Models\Konusma;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Konusma kanali - sadece katilimcilar erisebilir
Broadcast::channel('konusma.{konusmaId}', function ($user, $konusmaId) {
    $konusma = Konusma::find($konusmaId);

    if (!$konusma) return false;

    return $user->id === $konusma->alici_id || $user->id === $konusma->satici_id;
});

// Kullanicinin tum bildirimleri icin ozel kanal
Broadcast::channel('kullanici.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
