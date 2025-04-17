<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_image',
        'country',
        'gender',
        'mobile',
        'last_login',
        'approved_by',
        'approved_at',
        'reset_token',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function approvedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
