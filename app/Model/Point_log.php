<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Point_log extends BaseModel
{
	//
	protected $fillable = [
		'id', 
		'login_id', 
		'add_point', 
		'prev_point',
		'current_point',
		'operator',
		'created_at',
		'updated_at'
	];
}
