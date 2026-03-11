<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kargo extends Model
{
    protected $table = 'kargolar';

    protected $fillable = [
        'konusma_id',
        'teklif_id',
        'gonderen_id',
        'alici_id',
        'urun_id',
        'kargo_firmasi_id',
        'takip_no',
        'durum',
        'urun_fiyati',
        'kargo_ucreti',
        'alici_il_id',
        'alici_ilce_id',
        'alici_mahalle_id',
        'alici_adres_detay',
        'alici_telefon',
        'notlar',
    ];

    protected $casts = [
        'urun_fiyati' => 'decimal:2',
        'kargo_ucreti' => 'decimal:2',
    ];

    public function konusma(): BelongsTo
    {
        return $this->belongsTo(Konusma::class, 'konusma_id');
    }

    public function teklif(): BelongsTo
    {
        return $this->belongsTo(Teklif::class, 'teklif_id');
    }

    public function gonderen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gonderen_id');
    }

    public function alici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alici_id');
    }

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }

    public function kargoFirmasi(): BelongsTo
    {
        return $this->belongsTo(KargoFirmasi::class, 'kargo_firmasi_id');
    }

    public function aliciIl(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'alici_il_id');
    }

    public function aliciIlce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'alici_ilce_id');
    }

    public function aliciMahalle(): BelongsTo
    {
        return $this->belongsTo(Mahalle::class, 'alici_mahalle_id');
    }

    public function getToplamTutarAttribute(): float
    {
        return $this->urun_fiyati + $this->kargo_ucreti;
    }

    public function getFormatliFiyatAttribute(): string
    {
        return number_format($this->urun_fiyati, 2, ',', '.') . ' ₺';
    }

    public function getFormatliKargoUcretiAttribute(): string
    {
        return number_format($this->kargo_ucreti, 2, ',', '.') . ' ₺';
    }

    public function getFormatliToplamAttribute(): string
    {
        return number_format($this->toplam_tutar, 2, ',', '.') . ' ₺';
    }

    public function getDurumMetniAttribute(): string
    {
        return match ($this->durum) {
            'beklemede' => 'Adres Bekleniyor',
            'hazirlaniyor' => 'Hazırlanıyor',
            'kargoda' => 'Kargoda',
            'teslim_edildi' => 'Teslim Edildi',
            'iptal' => 'İptal Edildi',
            default => $this->durum,
        };
    }

    public function getDurumRengiAttribute(): string
    {
        return match ($this->durum) {
            'beklemede' => 'warning',
            'hazirlaniyor' => 'info',
            'kargoda' => 'primary',
            'teslim_edildi' => 'success',
            'iptal' => 'danger',
            default => 'secondary',
        };
    }

    public function getTakipLinkAttribute(): ?string
    {
        if (!$this->takip_no || !$this->kargoFirmasi || !$this->kargoFirmasi->takip_url) {
            return null;
        }

        return str_replace('{TAKIP_NO}', $this->takip_no, $this->kargoFirmasi->takip_url);
    }
}
