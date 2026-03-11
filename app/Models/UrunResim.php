<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrunResim extends Model
{
    use HasFactory;

    protected $table = 'urun_resimleri';

    protected $fillable = [
        'urun_id',
        'resim',
        'sira',
    ];

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class, 'urun_id');
    }
}
