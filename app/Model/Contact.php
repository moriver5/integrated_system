<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Contact extends BaseModel
{
	//
	protected $fillable = [
		'id',
		'client_id',
		'email',
		'reply_date',
		'group_id',
		'status',
		'subject',
		'msg',
		'memo',
		'created_at',
		'updated_at'
	];
}
