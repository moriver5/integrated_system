<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Email_ng_word extends BaseModel
{
    //
	protected $fillable = [
		'type',
		'word',
		'created_at',
		'updated_at'
	];
}
