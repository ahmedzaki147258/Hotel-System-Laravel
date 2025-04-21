<?php

namespace App\Models;

use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Cog\Contracts\Ban\Bannable as BannableContract;

class Staff extends Authenticatable implements BannableContract
{
    //
    use HasApiTokens, HasRoles, Bannable;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_image',
        'created_at'
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
