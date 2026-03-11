<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Il extends Model
{
    protected $table = 'iller';
    public $timestamps = false;

    protected $fillable = ['il_adi'];

    public function ilceler(): HasMany
    {
        return $this->hasMany(Ilce::class, 'il_id');
    }
}
