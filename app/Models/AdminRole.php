<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminRole extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Bu role sahip kullanicilar
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Bu rolun yetkileri
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AdminPermission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Belirli bir yetkiye sahip mi?
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Sistem rolu mu? (Silinemez)
     */
    public function isSystemRole(): bool
    {
        return $this->is_system;
    }
}
