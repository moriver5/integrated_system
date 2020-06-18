<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Point_category extends BaseModel
{
	protected $fillable = [
		'id', 
		'name', 
		'remarks',
		'created_at',
		'updated_at'
	];
}
