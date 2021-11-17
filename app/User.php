<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usercode', 'name', 'email', 'password', 'is_active', 'is_owner', 'owner_group'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function DocumentMain()
    {
        return $this->hasMany('App\DocumentMain', 'usercode', 'handler');
    }
    public function UserPermission()
    {
        return $this->hasMany('App\UserPermission', 'usercode', 'usercode');
    }
}
