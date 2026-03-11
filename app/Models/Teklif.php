<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teklif extends Model
{
    use HasFactory;

    protected $table = 'teklifler';

    protected $fillable = [
        'konusma_id',
        'mesaj_id',
        'teklif_eden_id',
        'tutar',
        'durum',
        'cevap_tarihi',
        'gecerlilik_tarihi',
        'not',
    ];

    protected $casts = [
        'tutar' => 'decimal:2',
        'cevap_tarihi' => 'datetime',
        'gecerlilik_tarihi' => 'datetime',
    ];

    public function konusma(): BelongsTo
    {
        return $this->belongsTo(Konusma::class, 'konusma_id');
    }

    public function mesaj(): BelongsTo
    {
        return $this->belongsTo(Mesaj::class, 'mesaj_id');
    }

    public function teklifEden(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teklif_eden_id');
    }

    /**
     * Formatli tutar
     */
    public function getFormatliTutarAttribute(): string
    {
        return number_format($this->tutar, 2, ',', '.') . ' TL';
    }

    /**
     * Teklif beklemede mi?
     */
    public function beklemedeMi(): bool
    {
        return $this->durum === 'beklemede';
    }

    /**
     * Teklifi kabul et
     */
    public function kabulEt(): void
    {
        $this->update([
            'durum' => 'kabul_edildi',
            'cevap_tarihi' => now(),
        ]);
    }

    /**
     * Teklifi reddet
     */
    public function reddet(): void
    {
        $this->update([
            'durum' => 'reddedildi',
            'cevap_tarihi' => now(),
        ]);
    }

    /**
     * Teklifi iptal et
     */
    public function iptalEt(): void
    {
        $this->update([
            'durum' => 'iptal',
            'cevap_tarihi' => now(),
        ]);
    }

    /**
     * Suresi doldu mu kontrolu
     */
    public function suresiDolduMu(): bool
    {
        if ($this->gecerlilik_tarihi === null) return false;
        return now()->isAfter($this->gecerlilik_tarihi);
    }

    /**
     * Beklemede teklifler scope
     */
    public function scopeBeklemede($query)
    {
        return $query->where('durum', 'beklemede');
    }
}
