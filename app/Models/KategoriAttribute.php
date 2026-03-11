<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAttribute extends Model
{
    protected $table = 'kategori_attributes';

    protected $fillable = [
        'kategori_id',
        'attribute_adi',
        'label',
        'tip',
        'secenekler',
        'zorunlu',
        'sira',
    ];

    protected $casts = [
        'secenekler' => 'array',
        'zorunlu' => 'boolean',
        'sira' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function urunValues(): HasMany
    {
        return $this->hasMany(UrunAttributeValue::class, 'attribute_id');
    }

    public function scopeSirali($query)
    {
        return $query->orderBy('sira');
    }

    public function scopeZorunlu($query)
    {
        return $query->where('zorunlu', true);
    }

    public function hasSecenekler(): bool
    {
        return in_array($this->tip, ['select', 'multiselect']) && !empty($this->secenekler);
    }

    public function getInputType(): string
    {
        return match ($this->tip) {
            'number' => 'number',
            'select' => 'select',
            'multiselect' => 'select',
            default => 'text',
        };
    }

    public function getTipMetniAttribute(): string
    {
        return match ($this->tip) {
            'text' => 'Metin',
            'number' => 'Sayi',
            'select' => 'Tekli Secim',
            'multiselect' => 'Coklu Secim',
            default => $this->tip,
        };
    }
}
