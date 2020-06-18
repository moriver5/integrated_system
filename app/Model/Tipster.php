<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Tipster extends BaseModel
{
	protected $fillable = [
		'id', 
		'name', 
		'contents', 
		'disp_flg',
		'is_star',
		'created_at',
		'updated_at'
	];
}
