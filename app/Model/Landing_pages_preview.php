<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Landing_pages_preview extends BaseModel
{
	protected $fillable = [
		'lp_id', 
		'type',
		'name', 
		'content',
		'created_at',
		'updated_at'
	];
}
