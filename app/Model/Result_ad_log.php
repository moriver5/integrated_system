<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Result_ad_log extends BaseModel
{
	protected $fillable = [
		'ad_cd', 
		'access_date', 
		'pv', 
		'temp_reg',
		'reg',
		'quit',
		'active',
		'order_num',
		'pay',
		'amount',
		'created_at',
		'updated_at'
	];
}
