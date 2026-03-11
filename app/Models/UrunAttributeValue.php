<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrunAttributeValue extends Model
{
    protected $table = 'urun_attribute_values';

    protected $fillable = [
        'urun_id',
        'attribute_id',
        'deger',
    ];

    // Relationships
    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(KategoriAttribute::class, 'attribute_id');
    }

    // Attributes
    public function getLabelAttribute(): string
    {
        return $this->attribute?->label ?? '';
    }

    public function getFormattedDegerAttribute(): string
    {
        $attribute = $this->attribute;

        if (!$attribute) {
            return $this->deger;
        }

        // Format based on type
        if ($attribute->tip === 'number' && is_numeric($this->deger)) {
            // Special formatting for km
            if ($attribute->attribute_adi === 'kilometre') {
                return number_format((float) $this->deger, 0, ',', '.') . ' km';
            }
            return number_format((float) $this->deger, 0, ',', '.');
        }

        return $this->deger;
    }
}
