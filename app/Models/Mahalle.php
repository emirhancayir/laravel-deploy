<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahalle extends Model
{
    protected $table = 'mahalleler';
    public $timestamps = false;

    protected $fillable = ['il_id', 'ilce_id', 'semt_id', 'mahalle_adi', 'posta_kodu'];

    public function ilce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }
}
