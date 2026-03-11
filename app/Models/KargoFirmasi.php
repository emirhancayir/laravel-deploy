<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KargoFirmasi extends Model
{
    protected $table = 'kargo_firmalari';

    protected $fillable = [
        'firma_adi',
        'logo',
        'takip_url',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function kargolar(): HasMany
    {
        return $this->hasMany(Kargo::class, 'kargo_firmasi_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function getTakipLinkAttribute(): ?string
    {
        if (!$this->takip_url) {
            return null;
        }
        return $this->takip_url;
    }
}
