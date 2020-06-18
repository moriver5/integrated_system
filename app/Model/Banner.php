<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Banner extends BaseModel
{
	protected $fillable = [
		'id',
		'banner',
		'disp_flg',
		'created_at',
		'updated_at'
	];
}
