<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Top_product extends BaseModel
{
	protected $fillable = [
		'id',
		'title',
		'comment',
		'tipster',
		'quantity',
		'open_flg',
		'groups',
		'saddle',
		'tickets',
		'order_num',
		'money',
		'point',
		'discount',
		'start_date',
		'end_date',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
