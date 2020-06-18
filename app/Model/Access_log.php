<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Access_log extends BaseModel
{
	protected $fillable = [
		'id', 
		'login_id',
		'pay_type',
		'login_date',
		'created_at',
		'updated_at'
	];
}
