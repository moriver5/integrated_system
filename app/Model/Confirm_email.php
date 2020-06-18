<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Confirm_email extends BaseModel
{
	protected $fillable = [
		'id',
		'name',
		'email',
		'created_at',
		'updated_at'
	];
}
