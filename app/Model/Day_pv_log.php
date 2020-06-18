<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Day_pv_log extends BaseModel
{
	protected $fillable = [
		'ad_cd', 
		'login_id', 
		'access_date',
		'created_at',
		'updated_at'
	];
}
