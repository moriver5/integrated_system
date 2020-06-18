<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Relay_server extends BaseModel
{
	protected $fillable = [
		'type',
		'ip',
		'port',
		'created_at',
		'updated_at'
	];
}
