<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Client_export_log extends BaseModel
{
	//
	protected $fillable = [
		'id',
		'login_id',
		'file',
		'created_at',
		'updated_at'
	];
}
