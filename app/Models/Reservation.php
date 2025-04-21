<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'client_id',
        'room_id',
        'accompany_number',
        'paid_price_in_cents',
        'payment_id',
        'check_out_at',
    ];

    protected $casts = [
        'check_out_at' => 'datetime',
    ];

    public function room(): BelongsTo {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
