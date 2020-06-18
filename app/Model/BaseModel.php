<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Utility;
use Session;

class BaseModel extends Model
{
	// 標準のDB接続
	protected $connection = 'mysql';

	public function __construct($attributes = array())
	{
//print_r($attributes);
		parent::__construct($attributes);
//echo Session::get('operation_select_db')."\n";
//print_r($_SERVER['SERVER_NAME']);
//exit;
		$db = Session::get('operation_select_db');
		//管理側DB切換え
		if( !empty($db) ){
			//DB切り替え
			$this->connection = Session::get('operation_select_db');
		}
	}

}
