<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Yorum extends Model
{
    protected $table = 'yorumlar';

    protected $fillable = [
        'kullanici_id',
        'urun_id',
        'siparis_id',
        'puan',
        'yorum',
        'resimler',
        'onaylandi',
        'onay_tarihi',
    ];

    protected $casts = [
        'puan' => 'integer',
        'resimler' => 'array',
        'onaylandi' => 'boolean',
        'onay_tarihi' => 'datetime',
    ];

    // İlişkiler
    public function kullanici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }

    public function siparis(): BelongsTo
    {
        return $this->belongsTo(Siparis::class, 'siparis_id');
    }

    // Scope'lar
    public function scopeOnaylanan($query)
    {
        return $query->where('onaylandi', true);
    }

    public function scopeBekleyen($query)
    {
        return $query->where('onaylandi', false);
    }

    public function scopeForUrun($query, $urunId)
    {
        return $query->where('urun_id', $urunId);
    }

    // Helper metodlar
    public function onayla()
    {
        $this->update([
            'onaylandi' => true,
            'onay_tarihi' => now(),
        ]);
    }

    public function reddet()
    {
        $this->delete();
    }

    // Puan yıldızları
    public function getYildizlarAttribute(): string
    {
        $dolu = str_repeat('★', $this->puan);
        $bos = str_repeat('☆', 5 - $this->puan);
        return $dolu . $bos;
    }

    // Puan rengi
    public function getPuanRengiAttribute(): string
    {
        return match(true) {
            $this->puan >= 4 => 'success',
            $this->puan >= 3 => 'warning',
            default => 'danger',
        };
    }
}
