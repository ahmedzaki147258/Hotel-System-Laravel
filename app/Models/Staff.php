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
        'created_at',
        'manager_id'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function manager()
    {
        return $this->belongsTo(Staff::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Staff::class, 'manager_id');
    }
}
