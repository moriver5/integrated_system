<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Top_content extends BaseModel
{
	//
	protected $fillable = [
		'id',
		'title',
		'type',
		'open_flg',
		'link_flg',
		'groups',
		'order_num',
		'start_date',
		'sort_start_date',
		'end_date',
		'sort_end_date',
		'sold_out_date',
		'sort_sold_out_date',
		'url',
		'img',
		'html_body',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
