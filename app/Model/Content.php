<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Content extends BaseModel
{
	protected $fillable = [
		'id', 
		'title', 
		'contents', 
		'sort',
		'created_at',
		'updated_at'
	];
	
}
