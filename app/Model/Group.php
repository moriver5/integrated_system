<?php

namespace App\Model;

//use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Group extends BaseModel
{
	//
	protected $fillable = [
		'id', 
		'name', 
		'memo',
		'created_at',
		'updated_at'
	];
}
