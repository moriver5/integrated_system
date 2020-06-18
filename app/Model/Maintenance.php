<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Maintenance extends BaseModel
{
	protected $fillable = [
		'type', 
		'mode', 
		'body',
		'created_at',
		'updated_at'
	];
}
