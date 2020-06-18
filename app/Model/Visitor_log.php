<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Visitor_log extends BaseModel
{
	protected $fillable = [
		'forecast_id',
		'client_id',
		'category',
		'created_at',
		'updated_at'
	];
}
