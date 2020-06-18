<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Landing_pages_content extends BaseModel
{
	protected $fillable = [
		'lp_id', 
		'type',				//0:PC/SP 1:PC 2:MB 3:SP
		'url_open_flg', 
		'name', 
		'content',
		'created_at',
		'updated_at'
	];
}
