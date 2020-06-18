<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Personal_mail_log extends BaseModel
{
    //
	protected $fillable = [
		'id', 
		'client_id', 
		'subject', 
		'body', 
		'created_at',
		'updated_at'
	];
}
