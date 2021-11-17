<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Documentstorage extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doc_id','file_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    public function DocumentMain(){
        return $this->hasOne('App\DocumentMain','doc_id','id');
    }
}
