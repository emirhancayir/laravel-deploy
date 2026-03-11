<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'ad',
        'soyad',
        'email',
        'password',
        'telefon',
        'adres',
        'kullanici_tipi',
        'profil_resmi',
        'firma_adi',
        'vergi_no',
        'iban',
        'satici_onay_tarihi',
        'email_verified',
        'verification_token',
        'token_expiry',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'two_factor_backup_codes',
        // Admin ve ban alanlari
        'is_banned',
        'banned_at',
        'ban_reason',
        'banned_by',
        // IP takip alanlari
        'registration_ip',
        'last_login_ip',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_backup_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
            'satici_onay_tarihi' => 'datetime',
            'token_expiry' => 'datetime',
        ];
    }

    public function twoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }

    public function getAdSoyadAttribute(): string
    {
        return $this->ad . ' ' . $this->soyad;
    }

    public function saticiMi(): bool
    {
        return $this->kullanici_tipi === 'satici';
    }

    public function aliciMi(): bool
    {
        return $this->kullanici_tipi === 'alici';
    }

    public function emailDogrulandiMi(): bool
    {
        return $this->email_verified == 1;
    }

    public function urunler(): HasMany
    {
        return $this->hasMany(Urun::class, 'satici_id');
    }

    public function favoriler(): HasMany
    {
        return $this->hasMany(Favori::class, 'kullanici_id');
    }

    // Sohbet iliskileri
    public function aliciKonusmalari(): HasMany
    {
        return $this->hasMany(Konusma::class, 'alici_id');
    }

    public function saticiKonusmalari(): HasMany
    {
        return $this->hasMany(Konusma::class, 'satici_id');
    }

    public function tumKonusmalar()
    {
        return Konusma::where('alici_id', $this->id)
                      ->orWhere('satici_id', $this->id);
    }

    public function gonderdigiMesajlar(): HasMany
    {
        return $this->hasMany(Mesaj::class, 'gonderen_id');
    }

    public function teklifleri(): HasMany
    {
        return $this->hasMany(Teklif::class, 'teklif_eden_id');
    }

    public function sepetItems(): HasMany
    {
        return $this->hasMany(SepetItem::class, 'kullanici_id');
    }

    public function aliciOdemeleri(): HasMany
    {
        return $this->hasMany(Odeme::class, 'alici_id');
    }

    public function saticiOdemeleri(): HasMany
    {
        return $this->hasMany(Odeme::class, 'satici_id');
    }

    /**
     * Kullanicinin sepetindeki urun sayisi
     */
    public function sepetSayisi(): int
    {
        return $this->sepetItems()->count();
    }

    /**
     * Kullanicinin bugunlu teklif sayisi
     */
    public function bugunkuTeklifSayisi(): int
    {
        return $this->teklifleri()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Gunluk teklif limitine ulasti mi?
     */
    public function teklifLimitineUlastiMi(): bool
    {
        $limit = config('zamason.gunluk_teklif_limiti', 5);
        return $this->bugunkuTeklifSayisi() >= $limit;
    }

    /**
     * Toplam okunmamis mesaj sayisi
     */
    public function okunmamisMesajSayisi(): int
    {
        $aliciOkunmamis = $this->aliciKonusmalari()->sum('okunmamis_alici');
        $saticiOkunmamis = $this->saticiKonusmalari()->sum('okunmamis_satici');
        return $aliciOkunmamis + $saticiOkunmamis;
    }

    // ==================== ADMIN METODLARI ====================

    /**
     * Kullanicinin admin rolleri
     */
    public function adminRoles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'user_roles', 'user_id', 'role_id')
            ->withPivot('assigned_at', 'assigned_by');
    }

    /**
     * Super admin mi?
     */
    public function superAdminMi(): bool
    {
        return $this->kullanici_tipi === 'super_admin';
    }

    /**
     * Admin mi? (admin veya super_admin)
     */
    public function adminMi(): bool
    {
        return in_array($this->kullanici_tipi, ['admin', 'super_admin']);
    }

    /**
     * Belirli bir yetkiye sahip mi?
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin her seyi yapabilir
        if ($this->superAdminMi()) {
            return true;
        }

        // Admin degilse yetki yok
        if (!$this->adminMi()) {
            return false;
        }

        // Rollerinden herhangi birinde bu yetki var mi?
        return $this->adminRoles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    /**
     * Herhangi bir yetkiye sahip mi? (OR)
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->superAdminMi()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tum yetkilere sahip mi? (AND)
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->superAdminMi()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kullanicinin tum yetkileri
     */
    public function getAllPermissions(): array
    {
        if ($this->superAdminMi()) {
            return AdminPermission::pluck('name')->toArray();
        }

        return $this->adminRoles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->toArray();
    }

    // ==================== BAN METODLARI ====================

    /**
     * Kullanici banlı mi?
     */
    public function banliMi(): bool
    {
        return $this->is_banned === true;
    }

    /**
     * Kullaniciyi banla
     */
    public function banla(?string $reason = null): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason,
            'banned_by' => auth()->id(),
        ]);

        // Aktivite logu
        AdminActivityLog::log('user.banned', $this, null, [
            'reason' => $reason,
        ], "Kullanici banlandi: {$this->ad_soyad}");
    }

    /**
     * Kullanicinin banini kaldir
     */
    public function banKaldir(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
            'banned_by' => null,
        ]);

        // Aktivite logu
        AdminActivityLog::log('user.unbanned', $this, null, null, "Kullanici bani kaldirildi: {$this->ad_soyad}");
    }

    /**
     * Bani yapan admin
     */
    public function banlayanAdmin()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    // ==================== IP LOG METODLARI ====================

    /**
     * Kullanicinin IP loglari
     */
    public function ipLogs(): HasMany
    {
        return $this->hasMany(IpLog::class);
    }

    // ==================== KULLANICI ENGELLEME METODLARI ====================

    /**
     * Bu kullanicinin engelledigi kullanicilar
     */
    public function engelledikleri(): HasMany
    {
        return $this->hasMany(KullaniciEngeli::class, 'engelleyen_id');
    }

    /**
     * Bu kullaniciyi engelleyen kullanicilar
     */
    public function engellenenler(): HasMany
    {
        return $this->hasMany(KullaniciEngeli::class, 'engellenen_id');
    }

    /**
     * Kullaniciyi engelle
     */
    public function engelle(int $kullaniciId, ?string $sebep = null): KullaniciEngeli
    {
        return KullaniciEngeli::engelle($this->id, $kullaniciId, $sebep);
    }

    /**
     * Engeli kaldir
     */
    public function engelKaldir(int $kullaniciId): bool
    {
        return KullaniciEngeli::engelKaldir($this->id, $kullaniciId);
    }

    /**
     * Bu kullaniciyi engellemis mi?
     */
    public function engellemis(int $kullaniciId): bool
    {
        return KullaniciEngeli::engelliMi($this->id, $kullaniciId);
    }

    /**
     * Bu kullanici tarafindan engellenmis mi?
     */
    public function engellenmis(int $kullaniciId): bool
    {
        return KullaniciEngeli::engelliMi($kullaniciId, $this->id);
    }

    /**
     * Aralarinda engel var mi? (iki yonlu)
     */
    public function engelVarMi(int $kullaniciId): bool
    {
        return KullaniciEngeli::karsilikliEngelliMi($this->id, $kullaniciId);
    }
}
