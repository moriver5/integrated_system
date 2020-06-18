<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Forecast extends BaseModel
{
	protected $fillable = [
		'id',
		'disp_sdate',
		'disp_edate',
		'open_sdate',
		'open_edate',
		'category',
		'groups',
		'product_id',
		'campaigns',
		'open_flg',
		'point',
		'title',
		'headline',
		'comment',
		'detail',
		'visitor',
		'disp_sort_sdate',
		'disp_sort_edate',
		'open_sort_sdate',
		'open_sort_edate',
		'created_at',
		'updated_at'
	];
}
