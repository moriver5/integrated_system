<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Personal_access_log extends BaseModel
{
    //
	protected $fillable = [
		'login_id', 
		'melmaga_id', 
		'page',
		'created_at',
		'updated_at'
	];
}
