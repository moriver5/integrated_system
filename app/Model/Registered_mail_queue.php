<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Registered_mail_queue extends BaseModel
{
	protected $fillable = [
		'send_id',
		'ad_cd',
		'client_id',
		'mail',
		'group_id',
		'device',
		'created_at',
		'updated_at'
	];
}
