<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Floor extends Model
{
    protected $fillable = [
        'name',
        'number',
        'manager_id', // Assuming you have a manager_id field in your floors table
    ];
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'floor_id', 'id');
    }
}
