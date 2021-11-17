<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PermissionList extends Model
{
    use Notifiable;

    protected $fillable = ['access_code', 'access_desc'];
}
