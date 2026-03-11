<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDomainListesi extends Model
{
    protected $table = 'email_domain_listeleri';

    protected $fillable = [
        'domain',
        'tip',
        'sebep',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeBlacklist($query)
    {
        return $query->where('tip', 'blacklist');
    }

    public function scopeWhitelist($query)
    {
        return $query->where('tip', 'whitelist');
    }

    // Statik metodlar
    public static function emailKontrol(string $email): array
    {
        $domain = self::emaildenDomainAl($email);

        if (!$domain) {
            return ['izinli' => false, 'sebep' => 'Geçersiz e-posta adresi'];
        }

        // Whitelist varsa sadece whitelist'tekiler kabul edilir
        $whitelistVar = self::aktif()->whitelist()->exists();

        if ($whitelistVar) {
            $whitelist = self::aktif()->whitelist()
                ->where('domain', $domain)
                ->exists();

            if (!$whitelist) {
                return ['izinli' => false, 'sebep' => 'Bu e-posta domain\'i izin verilenler listesinde değil'];
            }

            return ['izinli' => true, 'sebep' => null];
        }

        // Blacklist kontrol
        $blacklist = self::aktif()->blacklist()
            ->where('domain', $domain)
            ->first();

        if ($blacklist) {
            return ['izinli' => false, 'sebep' => $blacklist->sebep ?? 'Bu e-posta domain\'i engellenmiş'];
        }

        return ['izinli' => true, 'sebep' => null];
    }

    public static function emaildenDomainAl(string $email): ?string
    {
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return null;
        }

        return strtolower(trim($parts[1]));
    }

    // Yaygın geçici e-posta domainleri
    public static function geciciEmailDomainleri(): array
    {
        return [
            'tempmail.com',
            'temp-mail.org',
            'guerrillamail.com',
            'mailinator.com',
            '10minutemail.com',
            'throwaway.email',
            'fakeinbox.com',
            'trashmail.com',
            'maildrop.cc',
            'yopmail.com',
            'getnada.com',
            'mohmal.com',
            'tempail.com',
            'emailondeck.com',
            'mintemail.com',
            'dispostable.com',
            'mailnesia.com',
            'tempr.email',
            'discard.email',
            'spamgourmet.com',
        ];
    }

    // Yaygın geçici domainleri blacklist'e ekle
    public static function geciciDomainleriEkle(): int
    {
        $eklenen = 0;

        foreach (self::geciciEmailDomainleri() as $domain) {
            $var = self::where('domain', $domain)->where('tip', 'blacklist')->exists();

            if (!$var) {
                self::create([
                    'domain' => $domain,
                    'tip' => 'blacklist',
                    'sebep' => 'Geçici e-posta servisi',
                    'aktif' => true,
                ]);
                $eklenen++;
            }
        }

        return $eklenen;
    }
}
