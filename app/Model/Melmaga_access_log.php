<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Melmaga_access_log extends BaseModel
{
	protected $fillable = [
		'melmaga_id', 
		'login_id', 
		'access_date', 
		'created_at',
		'updated_at'
	];
}
