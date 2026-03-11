<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Konusma extends Model
{
    use HasFactory;

    protected $table = 'konusmalar';

    protected $fillable = [
        'urun_id',
        'alici_id',
        'satici_id',
        'son_mesaj_tarihi',
        'okunmamis_alici',
        'okunmamis_satici',
        'durum',
    ];

    protected $casts = [
        'son_mesaj_tarihi' => 'datetime',
    ];

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }

    public function alici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alici_id');
    }

    public function satici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'satici_id');
    }

    public function mesajlar(): HasMany
    {
        return $this->hasMany(Mesaj::class, 'konusma_id')->orderBy('created_at', 'asc');
    }

    public function teklifler(): HasMany
    {
        return $this->hasMany(Teklif::class, 'konusma_id')->orderBy('created_at', 'desc');
    }

    public function sonTeklif(): HasOne
    {
        return $this->hasOne(Teklif::class, 'konusma_id')->latestOfMany();
    }

    public function sonMesaj(): HasOne
    {
        return $this->hasOne(Mesaj::class, 'konusma_id')->latestOfMany();
    }

    /**
     * Kullanicinin bu konusmadaki rolunu bul
     */
    public function kullaniciRolu(User $user): ?string
    {
        if ($user->id === $this->alici_id) return 'alici';
        if ($user->id === $this->satici_id) return 'satici';
        return null;
    }

    /**
     * Karsi tarafi bul
     */
    public function karsiTaraf(User $user): ?User
    {
        if ($user->id === $this->alici_id) return $this->satici;
        if ($user->id === $this->satici_id) return $this->alici;
        return null;
    }

    /**
     * Okunmamis mesaj sayisini al
     */
    public function okunmamisSayisi(User $user): int
    {
        if ($user->id === $this->alici_id) return $this->okunmamis_alici;
        if ($user->id === $this->satici_id) return $this->okunmamis_satici;
        return 0;
    }

    /**
     * Aktif konusmalar scope
     */
    public function scopeAktif($query)
    {
        return $query->where('durum', 'aktif');
    }

    /**
     * Kullanicinin konusmalari scope
     */
    public function scopeKullanicininKonusmalari($query, $userId)
    {
        return $query->where('alici_id', $userId)
                     ->orWhere('satici_id', $userId);
    }
}
