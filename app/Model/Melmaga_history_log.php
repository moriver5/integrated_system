<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Melmaga_history_log extends BaseModel
{
	protected $fillable = [
		'melmaga_id', 
		'client_id', 
		'read_flg', 
		'sort_date', 
		'created_at',
		'updated_at'
	];
}
