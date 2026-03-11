<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminPermission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'group',
        'description',
    ];

    /**
     * Bu yetkiye sahip roller
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'role_permissions', 'permission_id', 'role_id');
    }

    /**
     * Gruba gore yetkileri getir
     */
    public static function getByGroup(): array
    {
        return static::all()->groupBy('group')->toArray();
    }
}
