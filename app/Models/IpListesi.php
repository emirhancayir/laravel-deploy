<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpListesi extends Model
{
    protected $table = 'ip_listeleri';

    protected $fillable = [
        'ip_adresi',
        'tip',
        'sebep',
        'bitis_tarihi',
        'ekleyen_id',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'bitis_tarihi' => 'datetime',
    ];

    // İlişkiler
    public function ekleyen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ekleyen_id');
    }

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

    public function scopeGecerli($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('bitis_tarihi')
              ->orWhere('bitis_tarihi', '>', now());
        });
    }

    // Statik metodlar
    public static function ipKontrol(string $ip): array
    {
        // Önce whitelist kontrol
        $whitelist = self::aktif()->whitelist()->gecerli()
            ->where('ip_adresi', $ip)
            ->exists();

        if ($whitelist) {
            return ['izinli' => true, 'sebep' => null];
        }

        // Blacklist kontrol
        $blacklist = self::aktif()->blacklist()->gecerli()
            ->where('ip_adresi', $ip)
            ->first();

        if ($blacklist) {
            return ['izinli' => false, 'sebep' => $blacklist->sebep];
        }

        // Wildcard kontrol (192.168.1.*)
        $ipParts = explode('.', $ip);
        if (count($ipParts) === 4) {
            $wildcardPatterns = [
                $ipParts[0] . '.*.*.*',
                $ipParts[0] . '.' . $ipParts[1] . '.*.*',
                $ipParts[0] . '.' . $ipParts[1] . '.' . $ipParts[2] . '.*',
            ];

            $wildcardBlock = self::aktif()->blacklist()->gecerli()
                ->whereIn('ip_adresi', $wildcardPatterns)
                ->first();

            if ($wildcardBlock) {
                return ['izinli' => false, 'sebep' => $wildcardBlock->sebep];
            }
        }

        return ['izinli' => true, 'sebep' => null];
    }

    // Attributes
    public function getKaliciMiAttribute(): bool
    {
        return is_null($this->bitis_tarihi);
    }

    public function getSuresiDolduMuAttribute(): bool
    {
        if ($this->kalici_mi) {
            return false;
        }
        return $this->bitis_tarihi->isPast();
    }
}
