<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Mail_content extends BaseModel
{
	//
	protected $fillable = [
		'id', 
		'name', 
		'from', 
		'from_mail',
		'subject',
		'body',
		'created_at',
		'updated_at'
	];
}
