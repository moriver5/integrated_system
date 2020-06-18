<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Create_order_id extends BaseModel
{
	protected $fillable = [
		'order_id',
		'key'
	];
}
