<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Settlement_type extends BaseModel
{
	protected $fillable = [
		'id',
		'name',
		'active',
		'clientip',
		'netbank_clientip',
		'sendid_length',
		'speed_credit_url',
		'credit_url',
		'netbank_url',
		'created_at',
		'updated_at'
	];
}
