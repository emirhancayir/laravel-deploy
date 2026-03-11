<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ip_address',
        'user_id',
        'action',
        'user_agent',
        'extra_data',
        'created_at',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Iliskili kullanici
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * IP logu olustur
     */
    public static function log(string $action, ?array $extraData = null): static
    {
        return static::create([
            'ip_address' => request()->ip(),
            'user_id' => auth()->id(),
            'action' => $action,
            'user_agent' => request()->userAgent(),
            'extra_data' => $extraData,
            'created_at' => now(),
        ]);
    }

    /**
     * Belirli bir IP icin kayitlar
     */
    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Belirli bir aksiyon icin kayitlar
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Bugunun kayitlari
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Belirli bir IP'den bugun yapilan teklif sayisi
     */
    public static function getDailyOfferCount(string $ip): int
    {
        return static::forIp($ip)
            ->forAction('offer')
            ->today()
            ->count();
    }

    /**
     * Belirli bir IP'den kayit yapan kullanici sayisi
     */
    public static function getRegistrationCount(string $ip): int
    {
        return static::forIp($ip)
            ->forAction('registration')
            ->count();
    }
}
