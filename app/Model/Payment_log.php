<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Payment_log extends BaseModel
{
	//
	protected $fillable = [
		'payment_id', 
		'agency_id',
		'pay_type', 
		'login_id',
		'type',
		'product_id',
		'order_id',
		'money',
		'point',
		'ad_cd',
		'status',
		'regist_date',
		'pay_count',
		'sendid',
		'tel',
		'email',
		'username',
		'cont',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
