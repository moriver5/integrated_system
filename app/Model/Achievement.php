<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Achievement extends BaseModel
{
	protected $fillable = [
		'id',
		'type',
		'product_id',
		'priority_id',
		'open_flg',
		'race_date',
		'race_name',
		'msg1',
		'msg2',
		'msg3',
		'memo',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
