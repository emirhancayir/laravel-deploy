<?php

namespace App\Services;

class IcerikModerasyon
{
    /**
     * Yasakli kelimeler listesi
     */
    protected array $yasakliKelimeler = [
        // Kufurler
        'amk', 'aq', 'mk', 'mq', 'oç', 'oc', 'orospu', 'piç', 'pic', 'sik', 'sık',
        'yarrak', 'yarak', 'taşak', 'tasak', 'göt', 'got', 'meme', 'kaltak',
        'fahise', 'fahişe', 'pezevenk', 'gavat', 'ibne', 'top', 'puşt', 'pust',
        'dangalak', 'gerizekalı', 'gerizekali', 'salak', 'aptal', 'mal', 'enayi',
        'hıyar', 'hiyar', 'şerefsiz', 'serefsiz', 'namussuz', 'alçak', 'alcak',
        'pislik', 'köpek', 'kopek', 'it', 'hayvan', 'eşek', 'esek',

        // Argo
        'bok', 'boktan', 'siktir', 'siktirgit', 's1k', 's2k', 'amcık', 'amcik',
        'sikerim', 'sikim', 'ananı', 'anani', 'bacını', 'bacini',

        // Hakaret
        'lan', 'ulan', 'yavşak', 'yavsak', 'haysiyetsiz', 'karaktersiz',
        'ahlaksız', 'ahlaksiz', 'rezil', 'kepaze',

        // Tehdit
        'öldürürüm', 'oldururum', 'gebertirim', 'döverim', 'doverim',
        'vururum', 'patlatırım', 'patlatirim',

        // Spam/Dolandiricilik
        'whatsapp', 'telegram', 'instagram', 'facebook', 'tiktok',
        '+90', '0530', '0531', '0532', '0533', '0534', '0535', '0536', '0537', '0538', '0539',
        '0540', '0541', '0542', '0543', '0544', '0545', '0546', '0547', '0548', '0549',
        '0550', '0551', '0552', '0553', '0554', '0555', '0556', '0557', '0558', '0559',
        'iban', 'para gönder', 'para yolla', 'havale', 'eft',
    ];

    /**
     * Regex patternler (daha gelismis kontrol)
     */
    protected array $yasakliPatternler = [
        '/a+m+[ıi]?n+a+\s*k+o+y+/iu',  // amına koy varyasyonları
        '/s[i1][kq]+[td]?[i1]?r/iu',    // siktir varyasyonları
        '/o+r+[o0]+s+p+u+/iu',          // orospu varyasyonları
        '/p[i1]+[cç]+/iu',              // piç varyasyonları
        '/g[oö]+t+/iu',                 // göt varyasyonları
        '/(\d{3,4}[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2})/i', // telefon numarası
        '/(wa\.me|t\.me|bit\.ly|goo\.gl)/i', // kısa linkler
        '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i', // email adresi
    ];

    /**
     * Beyaz liste (bu kelimeler izin verilir)
     */
    protected array $beyazListe = [
        'italya', 'italyan', 'hospital', 'pistol', 'pistons',
        'şikayet', 'sikayet', 'basket', 'basketbol',
    ];

    /**
     * Icerigi kontrol et
     */
    public function kontrol(string $icerik): array
    {
        $temizIcerik = $this->temizle($icerik);
        $bulunanlar = [];
        $pianiEngellensin = false;

        // Beyaz listeyi kontrol et
        foreach ($this->beyazListe as $kelime) {
            if (stripos($temizIcerik, $kelime) !== false) {
                $temizIcerik = str_ireplace($kelime, '', $temizIcerik);
            }
        }

        // Yasakli kelimeleri kontrol et
        foreach ($this->yasakliKelimeler as $kelime) {
            if (stripos($temizIcerik, $kelime) !== false) {
                $bulunanlar[] = $kelime;
                $pianiEngellensin = true;
            }
        }

        // Pattern kontrolu
        foreach ($this->yasakliPatternler as $pattern) {
            if (preg_match($pattern, $temizIcerik, $matches)) {
                $bulunanlar[] = $matches[0] ?? 'pattern';
                $pianiEngellensin = true;
            }
        }

        return [
            'pianiEngellensin' => $pianiEngellensin,
            'bulunanlar' => array_unique($bulunanlar),
            'mesaj' => $pianiEngellensin
                ? 'Mesajınız uygunsuz içerik barındırmaktadır.'
                : null,
        ];
    }

    /**
     * Icerigi sansurle (yildizla)
     */
    public function sansurle(string $icerik): string
    {
        $sonuc = $icerik;

        // Yasakli kelimeleri sansurle
        foreach ($this->yasakliKelimeler as $kelime) {
            $yildiz = str_repeat('*', mb_strlen($kelime));
            $sonuc = preg_replace('/\b' . preg_quote($kelime, '/') . '\b/iu', $yildiz, $sonuc);
        }

        // Pattern'leri sansurle
        foreach ($this->yasakliPatternler as $pattern) {
            $sonuc = preg_replace_callback($pattern, function($matches) {
                return str_repeat('*', mb_strlen($matches[0]));
            }, $sonuc);
        }

        return $sonuc;
    }

    /**
     * Metni temizle (normalize et)
     */
    protected function temizle(string $icerik): string
    {
        // Kucuk harfe cevir
        $temiz = mb_strtolower($icerik, 'UTF-8');

        // Ozel karakterleri ve sayilari harf olarak kullananlari yakala
        $temiz = str_replace(['0', '1', '3', '4', '5', '@'], ['o', 'i', 'e', 'a', 's', 'a'], $temiz);

        // Tekrar eden harfleri tek harfe indir (aaaa -> a)
        $temiz = preg_replace('/(.)\1{2,}/u', '$1', $temiz);

        return $temiz;
    }

    /**
     * Kelime ekle
     */
    public function kelimeEkle(string $kelime): void
    {
        if (!in_array(mb_strtolower($kelime), $this->yasakliKelimeler)) {
            $this->yasakliKelimeler[] = mb_strtolower($kelime);
        }
    }

    /**
     * Kelime kaldir
     */
    public function kelimeKaldir(string $kelime): void
    {
        $key = array_search(mb_strtolower($kelime), $this->yasakliKelimeler);
        if ($key !== false) {
            unset($this->yasakliKelimeler[$key]);
        }
    }

    /**
     * Spam kontrolu (cok fazla link, tekrar eden metin vb.)
     */
    public function spamKontrol(string $icerik): bool
    {
        // Cok fazla buyuk harf
        $buyukHarfOrani = preg_match_all('/[A-ZÇĞİÖŞÜ]/u', $icerik) / max(mb_strlen($icerik), 1);
        if ($buyukHarfOrani > 0.7 && mb_strlen($icerik) > 10) {
            return true;
        }

        // Tekrar eden karakterler (aaaaaaa)
        if (preg_match('/(.)\1{5,}/u', $icerik)) {
            return true;
        }

        // Cok fazla ozel karakter
        $ozelKarakterOrani = preg_match_all('/[!@#$%^&*(){}[\]|\\:";\'<>,.?\/~`]/u', $icerik) / max(mb_strlen($icerik), 1);
        if ($ozelKarakterOrani > 0.3 && mb_strlen($icerik) > 5) {
            return true;
        }

        return false;
    }
}
