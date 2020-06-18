<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Point_setting extends BaseModel
{
	protected $fillable = [
		'id', 
		'category_id', 
		'money', 
		'point',
		'disp_msg',
		'remarks',
		'created_at',
		'updated_at'
	];
}
