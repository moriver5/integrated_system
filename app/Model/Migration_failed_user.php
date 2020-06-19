<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Migration_failed_user extends BaseModel
{
	protected $fillable = [
		'client_id',
		'email',
		'login_id',
		'status',
		'is_quit',
		'disable',
		'reg_date',
		'last_access_date',
		'description',
		'memo'
	];
}
