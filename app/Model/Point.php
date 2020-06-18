<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Point extends BaseModel
{
	//
	protected $fillable = [
		'id', 
		'pay_type', 
		'money', 
		'point',
		'disp_msg',
		'memo',
		'created_at',
		'updated_at'
	];
}
