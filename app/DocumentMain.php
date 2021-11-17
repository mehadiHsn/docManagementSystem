<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DocumentMain extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doc_code', 'doc_rev', 'desc1', 'doc_group', 'doc_type', 'doc_class', 'doc_info', 'doc_status', 'handler'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function User()
    {
        return $this->belongsTo('App\User', 'handler', 'usercode');
    }

    public function Documentstatus()
    {
        return $this->hasOne('App\DocmentStatus', 'doc_status', 'status_code');
    }

    public function Documentstorage()
    {
        return $this->belongsTo('App\Documentstorage', 'id', 'doc_id');
    }
    public function Documenttype()
    {
        return $this->belongsTo('App\Documenttype', 'doc_type', 'type_code');
    }

    public function Documentgroup()
    {
        return $this->belongsTo('App\Documentgroup', 'doc_group', 'group_code');
    }

    public function ChangeRequest(){
        return $this->hasMany('App\ChangeRequest','doc_id','id');
    }
}
