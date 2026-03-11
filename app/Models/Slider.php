<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'sliders';

    protected $fillable = [
        'baslik',
        'alt_baslik',
        'resim',
        'link',
        'tip',
        'sira',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'sira' => 'integer',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeSirali($query)
    {
        return $query->orderBy('sira');
    }

    public function scopeTipli($query, string $tip)
    {
        return $query->where('tip', $tip);
    }

    // Attributes
    public function getResimUrlAttribute(): ?string
    {
        if (!$this->resim) {
            return null;
        }

        return asset('serve-image.php?p=sliders/' . $this->resim);
    }

    public function getTipMetniAttribute(): string
    {
        return match ($this->tip) {
            'ozel' => 'Ozel Slider',
            'populer' => 'Populer Urunler',
            'yeni' => 'Yeni Urunler',
            'indirimli' => 'Indirimli Urunler',
            default => $this->tip,
        };
    }
}
