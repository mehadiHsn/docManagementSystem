<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserPermission extends Model
{
    use Notifiable;
   // protected $primaryKey = 'usercode';
    protected $fillable = ['usercode', 'access_code'];

    public function User()
    {
        return $this->belongsTo('App\User', 'usercode', 'usercode');
    }
}
