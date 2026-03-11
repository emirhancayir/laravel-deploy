<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'link',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // İlişkiler
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope'lar
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper metodlar
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    // Bildirim tipleri için ikonlar
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'teklif' => 'fas fa-handshake',
            'mesaj' => 'fas fa-envelope',
            'siparis' => 'fas fa-shopping-cart',
            'odeme' => 'fas fa-credit-card',
            'sistem' => 'fas fa-bell',
            'uyari' => 'fas fa-exclamation-triangle',
            'yorum' => 'fas fa-star',
            default => 'fas fa-bell',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'teklif' => 'primary',
            'mesaj' => 'info',
            'siparis' => 'success',
            'odeme' => 'warning',
            'sistem' => 'secondary',
            'uyari' => 'danger',
            'yorum' => 'warning',
            default => 'primary',
        };
    }

    // Statik bildirim oluşturma metodları
    public static function send(int $userId, string $type, string $title, string $message, ?string $link = null, ?array $data = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
        ]);
    }

    public static function teklifBildirimi(int $userId, Teklif $teklif, string $durum): self
    {
        $messages = [
            'yeni' => 'Ürününüze yeni bir teklif geldi!',
            'kabul_edildi' => 'Teklifiniz kabul edildi!',
            'reddedildi' => 'Teklifiniz reddedildi.',
        ];

        return self::send(
            $userId,
            'teklif',
            'Teklif Bildirimi',
            $messages[$durum] ?? 'Teklif durumunuz güncellendi.',
            route('chat.show', $teklif->konusma_id),
            ['teklif_id' => $teklif->id]
        );
    }

    public static function mesajBildirimi(int $userId, Konusma $konusma): self
    {
        return self::send(
            $userId,
            'mesaj',
            'Yeni Mesaj',
            'Yeni bir mesajınız var.',
            route('chat.show', $konusma->id),
            ['konusma_id' => $konusma->id]
        );
    }
}
