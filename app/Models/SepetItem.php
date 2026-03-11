<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SepetItem extends Model
{
    protected $table = 'sepet_items';

    protected $fillable = [
        'kullanici_id',
        'urun_id',
        'teklif_id',
        'konusma_id',
        'fiyat',
    ];

    protected $casts = [
        'fiyat' => 'decimal:2',
    ];

    // Relationships
    public function kullanici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }

    public function teklif(): BelongsTo
    {
        return $this->belongsTo(Teklif::class, 'teklif_id');
    }

    public function konusma(): BelongsTo
    {
        return $this->belongsTo(Konusma::class, 'konusma_id');
    }

    // Scopes
    public function scopeKullanicinin($query, ?int $kullaniciId = null)
    {
        return $query->where('kullanici_id', $kullaniciId ?? auth()->id());
    }

    // Attributes
    public function getFormatliTutarAttribute(): string
    {
        return number_format($this->fiyat, 2, ',', '.') . ' TL';
    }

    public function getSaticiAttribute(): ?User
    {
        return $this->urun?->satici;
    }
}
