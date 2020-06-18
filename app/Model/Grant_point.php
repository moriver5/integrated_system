<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Grant_point extends BaseModel
{
	//
	protected $fillable = [
		'type',
		'point',
		'dispmsg',
		'disptime',
		'disp_flg',
		'created_at',
		'updated_at'
	];
}
