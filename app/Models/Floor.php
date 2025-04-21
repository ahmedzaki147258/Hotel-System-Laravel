<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Floor extends Model
{
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'floor_id', 'id');
    }
}
