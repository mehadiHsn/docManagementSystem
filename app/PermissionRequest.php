<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PermissionRequest extends Model
{
    use Notifiable;

    protected $fillable = ['usercode','access_code', 'status','comment'];
}
