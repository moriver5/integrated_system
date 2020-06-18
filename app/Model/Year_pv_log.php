<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Year_pv_log extends BaseModel
{
	protected $fillable = [
		'access_date', 
		'url', 
		'total',
		'created_at',
		'updated_at'
	];
}
