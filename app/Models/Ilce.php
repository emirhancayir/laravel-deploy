<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ilce extends Model
{
    protected $table = 'ilceler';
    public $timestamps = false;

    protected $fillable = ['il_id', 'ilce_adi'];

    public function il(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function mahalleler(): HasMany
    {
        return $this->hasMany(Mahalle::class, 'ilce_id');
    }
}
