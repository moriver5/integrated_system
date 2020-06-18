<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Year_result_access_log extends BaseModel
{
	//
	protected $fillable = [
		'access_date', 
		'no_pay', 
		'pay',
		'total',
		'created_at',
		'updated_at'
	];
}
