<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Viewdocument extends Model
{
    use Notifiable;
    protected $primaryKey = 'doc_id';
    protected $fillable = ['doc_id', 'group_desc', 'type_code', 'status_desc', 'handler', 'file_path', 'created_at'];
}
