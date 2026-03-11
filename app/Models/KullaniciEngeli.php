<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KullaniciEngeli extends Model
{
    protected $table = 'kullanici_engelleri';

    protected $fillable = [
        'engelleyen_id',
        'engellenen_id',
        'sebep',
    ];

    // İlişkiler
    public function engelleyen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engelleyen_id');
    }

    public function engellenen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engellenen_id');
    }

    // Statik metodlar
    public static function engelliMi(int $engelleyenId, int $engellenenId): bool
    {
        return self::where('engelleyen_id', $engelleyenId)
            ->where('engellenen_id', $engellenenId)
            ->exists();
    }

    public static function karsilikliEngelliMi(int $kullanici1Id, int $kullanici2Id): bool
    {
        return self::where(function ($q) use ($kullanici1Id, $kullanici2Id) {
            $q->where('engelleyen_id', $kullanici1Id)
              ->where('engellenen_id', $kullanici2Id);
        })->orWhere(function ($q) use ($kullanici1Id, $kullanici2Id) {
            $q->where('engelleyen_id', $kullanici2Id)
              ->where('engellenen_id', $kullanici1Id);
        })->exists();
    }

    public static function engelle(int $engelleyenId, int $engellenenId, ?string $sebep = null): self
    {
        return self::firstOrCreate(
            [
                'engelleyen_id' => $engelleyenId,
                'engellenen_id' => $engellenenId,
            ],
            ['sebep' => $sebep]
        );
    }

    public static function engelKaldir(int $engelleyenId, int $engellenenId): bool
    {
        return self::where('engelleyen_id', $engelleyenId)
            ->where('engellenen_id', $engellenenId)
            ->delete() > 0;
    }
}
