<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{ 
	protected $fillable = ['doc_id','attr_name','new_value','request_by','request_status'];

	public function DocumentMain(){
		return $this->belongsTo('App\DocumentMain', 'id', 'doc_id');
	}
}
