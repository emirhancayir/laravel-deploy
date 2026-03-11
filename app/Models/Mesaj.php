<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mesaj extends Model
{
    use HasFactory;

    protected $table = 'mesajlar';

    protected $fillable = [
        'konusma_id',
        'gonderen_id',
        'mesaj',
        'tip',
        'okundu',
        'okunma_tarihi',
    ];

    protected $casts = [
        'okundu' => 'boolean',
        'okunma_tarihi' => 'datetime',
    ];

    public function konusma(): BelongsTo
    {
        return $this->belongsTo(Konusma::class, 'konusma_id');
    }

    public function gonderen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gonderen_id');
    }

    public function teklif(): HasOne
    {
        return $this->hasOne(Teklif::class, 'mesaj_id');
    }

    /**
     * Mesajin gondereni mi kontrolu
     */
    public function gonderenMi(User $user): bool
    {
        return $this->gonderen_id === $user->id;
    }

    /**
     * Mesaji okundu olarak isaretle
     */
    public function okunduIsaretle(): void
    {
        if (!$this->okundu) {
            $this->update([
                'okundu' => true,
                'okunma_tarihi' => now(),
            ]);
        }
    }

    /**
     * Formatli tarih
     */
    public function getFormatliTarihAttribute(): string
    {
        $now = now();
        $created = $this->created_at;

        if ($created->isToday()) {
            return $created->format('H:i');
        } elseif ($created->isYesterday()) {
            return 'Dun ' . $created->format('H:i');
        } elseif ($created->year === $now->year) {
            return $created->translatedFormat('d M H:i');
        }
        return $created->format('d.m.Y H:i');
    }
}
