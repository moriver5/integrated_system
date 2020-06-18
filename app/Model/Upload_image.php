<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Upload_image extends BaseModel
{
	protected $fillable = [
		'id',
		'ext',
		'size',
		'created_at',
		'updated_at'
	];
}
