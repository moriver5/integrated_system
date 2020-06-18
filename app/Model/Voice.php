<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Voice extends BaseModel
{
	protected $fillable = [
		'id',
		'open_flg',
		'title',
		'name',
		'msg',
		'img',
		'post_date',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
