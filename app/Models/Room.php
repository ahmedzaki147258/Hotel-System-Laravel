<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Room extends Model
{
    protected $fillable = [
        'floor_id',
        'number',
        'price',
        'status',
        'capacity',
        'manager_id',
    ];

    public function floor(): BelongsTo {
        return $this->belongsTo(Floor::class, 'floor_id', 'id');
    }
}
