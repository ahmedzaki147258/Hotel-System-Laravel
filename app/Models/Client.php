<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_image',
        'country_id',
        'gender',
        'mobile',
        'last_login_at',
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

    public function country(): BelongsTo {
        return $this->belongsTo(\Lwwcas\LaravelCountries\Models\Country::class, 'country_id', 'id');
    }
}
