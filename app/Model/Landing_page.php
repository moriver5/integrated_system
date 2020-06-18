<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Landing_page extends BaseModel
{
	protected $fillable = [
		'id', 
		'open_flg', 
		'memo', 
		'img', 
		'sort_date',
		'created_at',
		'updated_at'
	];
}
