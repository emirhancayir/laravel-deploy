<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Odeme extends Model
{
    protected $table = 'odemeler';

    protected $fillable = [
        'alici_id',
        'satici_id',
        'urun_id',
        'teklif_id',
        'konusma_id',
        'sepet_item_id',
        'conversation_id',
        'iyzico_payment_id',
        'iyzico_token',
        'payment_transaction_id',
        'urun_tutari',
        'kargo_tutari',
        'komisyon_orani',
        'komisyon_tutari',
        'toplam_tutar',
        'satici_tutari',
        'durum',
        'odeme_tarihi',
        'onay_tarihi',
        'hata_mesaji',
        'iyzico_response',
        'teslimat_il_id',
        'teslimat_ilce_id',
        'teslimat_mahalle_id',
        'teslimat_adres_detay',
        'teslimat_telefon',
    ];

    protected $casts = [
        'urun_tutari' => 'decimal:2',
        'kargo_tutari' => 'decimal:2',
        'komisyon_orani' => 'decimal:2',
        'komisyon_tutari' => 'decimal:2',
        'toplam_tutar' => 'decimal:2',
        'satici_tutari' => 'decimal:2',
        'odeme_tarihi' => 'datetime',
        'onay_tarihi' => 'datetime',
        'iyzico_response' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($odeme) {
            if (!$odeme->conversation_id) {
                $odeme->conversation_id = 'ZAM-' . Str::uuid()->toString();
            }
        });
    }

    // Relationships
    public function alici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alici_id');
    }

    public function satici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'satici_id');
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

    public function sepetItem(): BelongsTo
    {
        return $this->belongsTo(SepetItem::class, 'sepet_item_id');
    }

    public function teslimatIl(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'teslimat_il_id');
    }

    public function teslimatIlce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'teslimat_ilce_id');
    }

    public function teslimatMahalle(): BelongsTo
    {
        return $this->belongsTo(Mahalle::class, 'teslimat_mahalle_id');
    }

    // Scopes
    public function scopeBeklemede($query)
    {
        return $query->where('durum', 'beklemede');
    }

    public function scopeOdendi($query)
    {
        return $query->where('durum', 'odendi');
    }

    public function scopeOnaylandi($query)
    {
        return $query->where('durum', 'onaylandi');
    }

    public function scopeBasarili($query)
    {
        return $query->whereIn('durum', ['odendi', 'onaylandi']);
    }

    // Helper Methods
    public function odendiMi(): bool
    {
        return in_array($this->durum, ['odendi', 'onaylandi']);
    }

    public function onaylanabilirMi(): bool
    {
        return $this->durum === 'odendi' && $this->payment_transaction_id;
    }

    public function iptalEdilebilirMi(): bool
    {
        return $this->durum === 'beklemede';
    }

    // Attributes
    public function getFormatliToplamAttribute(): string
    {
        return number_format($this->toplam_tutar, 2, ',', '.') . ' TL';
    }

    public function getFormatliUrunTutariAttribute(): string
    {
        return number_format($this->urun_tutari, 2, ',', '.') . ' TL';
    }

    public function getFormatliKargoTutariAttribute(): string
    {
        return number_format($this->kargo_tutari, 2, ',', '.') . ' TL';
    }

    public function getDurumMetniAttribute(): string
    {
        return match ($this->durum) {
            'beklemede' => 'Odeme Bekleniyor',
            'odendi' => 'Odeme Alindi',
            'onaylandi' => 'Tamamlandi',
            'iptal' => 'Iptal Edildi',
            'iade' => 'Iade Edildi',
            'basarisiz' => 'Basarisiz',
            default => $this->durum,
        };
    }

    public function getDurumRengiAttribute(): string
    {
        return match ($this->durum) {
            'beklemede' => 'warning',
            'odendi' => 'info',
            'onaylandi' => 'success',
            'iptal', 'basarisiz' => 'danger',
            'iade' => 'secondary',
            default => 'secondary',
        };
    }

    public function getTeslimatAdresiAttribute(): string
    {
        $parts = array_filter([
            $this->teslimatMahalle?->mahalle_adi,
            $this->teslimatIlce?->ilce_adi,
            $this->teslimatIl?->il_adi,
        ]);

        $adres = implode(', ', $parts);

        if ($this->teslimat_adres_detay) {
            $adres = $this->teslimat_adres_detay . ' - ' . $adres;
        }

        return $adres ?: 'Adres belirtilmemis';
    }
}
