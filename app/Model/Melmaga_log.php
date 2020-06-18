<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Melmaga_log extends BaseModel
{
	protected $fillable = [
		'id', 
		'send_status', 
		'send_count',
		'send_method',
		'from_name',
		'from_mail',
		'subject',
		'text_body',
		'html_body',
		'query',
		'bindings',
		'send_date',
		'reserve_send_date',
		'sort_reserve_send_date',
		'created_at',
		'updated_at'
	];
}
