<?php

namespace App\Libs;

use Utility;
use DB;
use PDO;

class DbPdo
{

	public function PdoCon()
	{
		try {
//			$admin_info = Utility::getAdminDefaultDispParam();
			$dbh = DB::connection($admin_info['select_db'])->getPdo();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策
//			throw new \PDOException("テスト例外エラー");
			return $dbh;

		} catch (\PDOException $e) {
			abort('403', __("messages.pdo_connection_err_msg"));
		}
	}
	
	
}