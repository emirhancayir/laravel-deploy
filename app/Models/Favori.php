<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favori extends Model
{
    use HasFactory;

    protected $table = 'favoriler';

    protected $fillable = [
        'kullanici_id',
        'urun_id',
    ];

    public function kullanici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kullanici_id');
    }

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }
}
