<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Operation_dbs extends Model
{
	protected $fillable = [
		'id', 
		'name', 
		'db', 
		'host', 
		'port', 
		'username', 
		'password', 
		'created_at',
		'updated_at'
	];
}
