<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
//オリジナルコード
//use Illuminate\Foundation\Auth\User as Authenticatable;
//データベースをデフォルト固定のためにこちらを使用
use Illuminate\Foundation\Auth\Admin as Authenticatable;

class Admin extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password', 'type', 'id', 'remember_token',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public function __construct($attributes = array())
	{   
		parent::__construct($attributes);
//echo Session::get('operation_select_db')."\n";
		//DB切り替え
		$this->connection = 'mysql';
	}
}
