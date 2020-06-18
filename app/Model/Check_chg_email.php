<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Check_chg_email extends BaseModel
{
    //
	protected $fillable = [
		'id',
		'login_id',
		'token',
		'email',
		'created_at',
		'updated_at'
	];
}
