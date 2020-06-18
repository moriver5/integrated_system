<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class User_info extends BaseModel
{
	protected $fillable = [
		'id',
		'userinfo',
		'order',
		'disptime',
		'disp_flg',
		'created_at',
		'updated_at'
	];
}
