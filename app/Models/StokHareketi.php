<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokHareketi extends Model
{
    protected $table = 'stok_hareketleri';

    protected $fillable = [
        'urun_id',
        'kullanici_id',
        'hareket_tipi',
        'miktar',
        'onceki_stok',
        'sonraki_stok',
        'aciklama',
    ];

    protected $casts = [
        'miktar' => 'integer',
        'onceki_stok' => 'integer',
        'sonraki_stok' => 'integer',
    ];

    // Relationships
    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }

    public function kullanici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    // Scopes
    public function scopeGiris($query)
    {
        return $query->where('hareket_tipi', 'giris');
    }

    public function scopeCikis($query)
    {
        return $query->where('hareket_tipi', 'cikis');
    }

    public function scopeSatis($query)
    {
        return $query->where('hareket_tipi', 'satis');
    }

    // Helper Methods
    public static function kaydet(
        int $urunId,
        string $hareketTipi,
        int $miktar,
        int $oncekiStok,
        ?string $aciklama = null,
        ?int $kullaniciId = null
    ): self {
        return self::create([
            'urun_id' => $urunId,
            'kullanici_id' => $kullaniciId ?? auth()->id(),
            'hareket_tipi' => $hareketTipi,
            'miktar' => $miktar,
            'onceki_stok' => $oncekiStok,
            'sonraki_stok' => $oncekiStok + $miktar,
            'aciklama' => $aciklama,
        ]);
    }

    public function getHareketTipiMetniAttribute(): string
    {
        return match ($this->hareket_tipi) {
            'giris' => 'Stok Girisi',
            'cikis' => 'Stok Cikisi',
            'duzeltme' => 'Stok Duzeltme',
            'satis' => 'Satis',
            'iptal' => 'Iptal',
            default => $this->hareket_tipi,
        };
    }
}
