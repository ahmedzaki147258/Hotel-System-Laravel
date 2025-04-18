<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    //
    use HasApiTokens, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_image',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        //'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


}
