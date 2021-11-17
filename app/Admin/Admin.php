<?php

namespace App\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use Notifiable;
    protected $guard = 'admin';
    protected $fillable = ['usercode','name','email','password'];
    protected $hidden = [
        'password', 'remember_token','updated_at','created_at'
    ];
}
