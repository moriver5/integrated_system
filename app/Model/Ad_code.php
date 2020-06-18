<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Ad_code extends BaseModel
{
	protected $fillable = [
		'id',
		'group_id',
		'ad_cd',
		'single_opt',
		'agency_id',
		'category',
		'aggregate_flg',
		'name',
		'url',
		'memo',
		'created_at',
		'updated_at'
	];
}
