<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoriler';

    protected $fillable = [
        'kategori_adi',
        'slug',
        'aciklama',
        'komisyon_orani',
        'aktif',
    ];

    protected $casts = [
        'komisyon_orani' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    /**
     * Category name accessor (short access)
     */
    public function getNameAttribute(): ?string
    {
        return $this->kategori_adi;
    }

    public function urunler(): HasMany
    {
        return $this->hasMany(Urun::class, 'kategori_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(KategoriAttribute::class, 'kategori_id')->orderBy('sira');
    }

    /**
     * Check if category has custom attributes
     */
    public function hasAttributes(): bool
    {
        return $this->attributes()->exists();
    }
}
