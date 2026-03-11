<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Urun extends Model
{
    use HasFactory;

    protected $table = 'urunler';

    protected $fillable = [
        'satici_id',
        'kategori_id',
        'urun_adi',
        'aciklama',
        'fiyat',
        'eski_fiyat',
        'stok',
        'resim',
        'durum',
        'onay_durumu',
        'red_nedeni',
        'onaylandi_tarih',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'adres_detay',
        'satildi',
        'goruntulenme_sayisi',
    ];

    protected $casts = [
        'fiyat' => 'decimal:2',
        'eski_fiyat' => 'decimal:2',
        'stok' => 'integer',
        'satildi' => 'boolean',
        'goruntulenme_sayisi' => 'integer',
    ];

    public function satici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'satici_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function il(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle(): BelongsTo
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    public function resimler(): HasMany
    {
        return $this->hasMany(UrunResim::class, 'urun_id')->orderBy('sira');
    }

    public function favoriler(): HasMany
    {
        return $this->hasMany(Favori::class, 'urun_id');
    }

    public function konusmalar(): HasMany
    {
        return $this->hasMany(Konusma::class, 'urun_id');
    }

    public function stokHareketleri(): HasMany
    {
        return $this->hasMany(StokHareketi::class, 'urun_id');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(UrunAttributeValue::class, 'urun_id');
    }

    public function odemeler(): HasMany
    {
        return $this->hasMany(Odeme::class, 'urun_id');
    }

    public function yorumlar(): HasMany
    {
        return $this->hasMany(Yorum::class, 'urun_id');
    }

    public function onayliYorumlar(): HasMany
    {
        return $this->hasMany(Yorum::class, 'urun_id')->where('onaylandi', true);
    }

    public function scopeAktif($query)
    {
        return $query->where('durum', 'aktif');
    }

    public function scopeStokta($query)
    {
        return $query->where('stok', '>', 0);
    }

    public function scopeSatilmamis($query)
    {
        return $query->where('satildi', false);
    }

    public function scopeBeklemede($query)
    {
        return $query->where('onay_durumu', 'beklemede');
    }

    public function scopeOnaylandi($query)
    {
        return $query->where('onay_durumu', 'onaylandi');
    }

    public function scopeReddedildi($query)
    {
        return $query->where('onay_durumu', 'reddedildi');
    }

    public function getFormatliFiyatAttribute(): string
    {
        return number_format($this->fiyat, 2, ',', '.') . ' TL';
    }

    public function getFormatliEskiFiyatAttribute(): ?string
    {
        if (!$this->eski_fiyat) {
            return null;
        }
        return number_format($this->eski_fiyat, 2, ',', '.') . ' TL';
    }

    public function indirimliMi(): bool
    {
        return $this->eski_fiyat && $this->eski_fiyat > $this->fiyat;
    }

    public function getIndirimOraniAttribute(): ?int
    {
        if (!$this->indirimliMi()) {
            return null;
        }
        return (int) round((($this->eski_fiyat - $this->fiyat) / $this->eski_fiyat) * 100);
    }

    /**
     * Kategori ozelliklerini dizi olarak dondur
     */
    public function getAttributesArrayAttribute(): array
    {
        return $this->attributeValues()
            ->with('attribute')
            ->get()
            ->mapWithKeys(function ($value) {
                return [$value->attribute->attribute_adi => $value->deger];
            })
            ->toArray();
    }

    /**
     * Stok hareketi kaydet
     */
    public function stokGuncelle(int $miktar, string $hareketTipi, ?string $aciklama = null): void
    {
        $oncekiStok = $this->stok;

        StokHareketi::kaydet(
            $this->id,
            $hareketTipi,
            $miktar,
            $oncekiStok,
            $aciklama
        );

        $this->update(['stok' => $oncekiStok + $miktar]);
    }

    /**
     * Ortalama puan
     */
    public function ortalamaPuan(): float
    {
        return round($this->onayliYorumlar()->avg('puan') ?? 0, 1);
    }

    /**
     * Yorum sayısı
     */
    public function yorumSayisi(): int
    {
        return $this->onayliYorumlar()->count();
    }

    /**
     * Puan dağılımı (1-5 arası her puan için sayı)
     */
    public function puanDagilimi(): array
    {
        $dagilim = [];
        for ($i = 5; $i >= 1; $i--) {
            $dagilim[$i] = $this->onayliYorumlar()->where('puan', $i)->count();
        }
        return $dagilim;
    }

    /**
     * Yıldız gösterimi (HTML)
     */
    public function getYildizlarAttribute(): string
    {
        $puan = $this->ortalamaPuan();
        $dolu = (int) floor($puan);
        $yarim = ($puan - $dolu) >= 0.5 ? 1 : 0;
        $bos = 5 - $dolu - $yarim;

        $html = str_repeat('<i class="fas fa-star text-warning"></i>', $dolu);
        if ($yarim) {
            $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
        }
        $html .= str_repeat('<i class="far fa-star text-warning"></i>', $bos);

        return $html;
    }
}
