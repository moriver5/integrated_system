<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Convert_table extends BaseModel
{
	protected $fillable = [
		'id', 
		'type',
		'key', 
		'value', 
		'memo',
		'created_at',
		'updated_at'
	];
}
