<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YasakliKelime extends Model
{
    protected $table = 'yasakli_kelimeler';

    protected $fillable = [
        'kelime',
        'tip',
        'uygulanacak_alanlar',
        'yerine',
        'aksiyon',
        'aktif',
    ];

    protected $casts = [
        'uygulanacak_alanlar' => 'array',
        'aktif' => 'boolean',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeAlanIcin($query, string $alan)
    {
        return $query->whereJsonContains('uygulanacak_alanlar', $alan);
    }

    // Statik metodlar
    public static function metniKontrolEt(string $metin, string $alan): array
    {
        $yasaklilar = self::aktif()->alanIcin($alan)->get();
        $bulunanlar = [];

        $metinKucuk = mb_strtolower($metin, 'UTF-8');

        foreach ($yasaklilar as $yasakli) {
            $kelimeKucuk = mb_strtolower($yasakli->kelime, 'UTF-8');

            $bulundu = false;

            if ($yasakli->tip === 'tam_eslesme') {
                // Kelime sınırlarıyla tam eşleşme
                $pattern = '/\b' . preg_quote($kelimeKucuk, '/') . '\b/iu';
                $bulundu = preg_match($pattern, $metinKucuk);
            } else {
                // İçerir
                $bulundu = str_contains($metinKucuk, $kelimeKucuk);
            }

            if ($bulundu) {
                $bulunanlar[] = [
                    'kelime' => $yasakli->kelime,
                    'aksiyon' => $yasakli->aksiyon,
                    'yerine' => $yasakli->yerine,
                ];
            }
        }

        return $bulunanlar;
    }

    public static function metniSansurle(string $metin, string $alan): string
    {
        $yasaklilar = self::aktif()->alanIcin($alan)->get();

        foreach ($yasaklilar as $yasakli) {
            if ($yasakli->aksiyon === 'sansurle') {
                $yerine = $yasakli->yerine ?? str_repeat('*', mb_strlen($yasakli->kelime));

                if ($yasakli->tip === 'tam_eslesme') {
                    $pattern = '/\b' . preg_quote($yasakli->kelime, '/') . '\b/iu';
                    $metin = preg_replace($pattern, $yerine, $metin);
                } else {
                    $metin = str_ireplace($yasakli->kelime, $yerine, $metin);
                }
            }
        }

        return $metin;
    }

    public static function metinGecerliMi(string $metin, string $alan): array
    {
        $bulunanlar = self::metniKontrolEt($metin, $alan);

        $engellenenler = array_filter($bulunanlar, fn($b) => $b['aksiyon'] === 'engelle');

        if (!empty($engellenenler)) {
            return [
                'gecerli' => false,
                'mesaj' => 'Yasaklı kelime içeriyor: ' . implode(', ', array_column($engellenenler, 'kelime')),
                'kelimeler' => $engellenenler,
            ];
        }

        return ['gecerli' => true, 'mesaj' => null, 'kelimeler' => []];
    }

    // Attributes
    public function getAlanlarMetniAttribute(): string
    {
        $alanIsimleri = [
            'urun_adi' => 'Ürün Adı',
            'urun_aciklama' => 'Ürün Açıklama',
            'mesaj' => 'Mesajlar',
            'kullanici_adi' => 'Kullanıcı Adı',
            'yorum' => 'Yorumlar',
        ];

        return collect($this->uygulanacak_alanlar)
            ->map(fn($a) => $alanIsimleri[$a] ?? $a)
            ->implode(', ');
    }

    public function getAksiyonMetniAttribute(): string
    {
        return match ($this->aksiyon) {
            'engelle' => 'Engelle',
            'sansurle' => 'Sansürle',
            'uyar' => 'Uyar',
            default => $this->aksiyon,
        };
    }
}
