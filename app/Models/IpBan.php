<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpBan extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'banned_by',
        'banned_at',
        'expires_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Bani yapan admin
     */
    public function bannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * IP banlı mi kontrol et
     */
    public static function isBanned(string $ip): bool
    {
        return static::where('ip_address', $ip)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * IP banla
     */
    public static function ban(string $ip, ?string $reason = null, ?\DateTime $expiresAt = null): static
    {
        return static::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'banned_by' => auth()->id(),
                'banned_at' => now(),
                'expires_at' => $expiresAt,
            ]
        );
    }

    /**
     * IP banini kaldir
     */
    public static function unban(string $ip): bool
    {
        return static::where('ip_address', $ip)->delete() > 0;
    }

    /**
     * Aktif banlar (suresi dolmamis)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Suresi dolmus banlar
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Ban suresi dolmus mu?
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Kalici ban mi?
     */
    public function isPermanent(): bool
    {
        return $this->expires_at === null;
    }
}
