<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gunluk Teklif Limiti
    |--------------------------------------------------------------------------
    |
    | Her kullanicinin gunluk verebilecegi maksimum teklif sayisi.
    | Bot korumasina yardimci olur.
    |
    */
    'gunluk_teklif_limiti' => env('GUNLUK_TEKLIF_LIMITI', 5),

    /*
    |--------------------------------------------------------------------------
    | IP Bazli Gunluk Kayit Limiti
    |--------------------------------------------------------------------------
    |
    | Ayni IP adresinden gunluk acilabilecek maksimum hesap sayisi.
    | Bot ve coklu hesap korumasina yardimci olur.
    |
    */
    'gunluk_kayit_limiti' => env('GUNLUK_KAYIT_LIMITI', 1),

    /*
    |--------------------------------------------------------------------------
    | Komisyon Orani
    |--------------------------------------------------------------------------
    |
    | Platform komisyon orani (yuzde olarak).
    | Odeme islemlerinde satici tutarindan dusulur.
    |
    */
    'komisyon_orani' => env('KOMISYON_ORANI', 5),

    /*
    |--------------------------------------------------------------------------
    | Varsayilan Kargo Ucreti
    |--------------------------------------------------------------------------
    |
    | Kargo ucreti belirtilmediginde kullanilacak varsayilan ucret.
    |
    */
    'varsayilan_kargo_ucreti' => env('VARSAYILAN_KARGO_UCRETI', 50),

    /*
    |--------------------------------------------------------------------------
    | Slider Ayarlari
    |--------------------------------------------------------------------------
    |
    | Ana sayfa slider bolumlerinde gosterilecek urun sayilari.
    |
    */
    'slider' => [
        'populer_urun_sayisi' => 8,
        'yeni_urun_sayisi' => 8,
        'indirimli_urun_sayisi' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Stok Uyari Limiti
    |--------------------------------------------------------------------------
    |
    | Bu sayinin altindaki stoklar icin uyari gosterilir.
    |
    */
    'stok_uyari_limiti' => env('STOK_UYARI_LIMITI', 5),
];
