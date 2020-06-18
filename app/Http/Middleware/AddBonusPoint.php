<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use PDO;
use Carbon\Carbon;

class AddBonusPoint
{
	private $dbh;

	public function __construct()
	{
		//DB接続
		$this->_pdoDbCon();
	}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		//認証情報取得
		$user = \Auth::guard('user')->user();

		$now_date = Carbon::now();

		try{ 
			//トランザクション開始
			$this->dbh->beginTransaction();

			//行ロックさせユーザーID取得
			$stmt = $this->dbh->prepare("select login_id,updated_at from users where bonus_flg = 0 and login_id = :login_id and status = 1 and disable = 0 for update");
			$stmt->bindValue(":login_id", $user['login_id']);
			$stmt->execute();

			$login_id = null;
			$updated_at = null;
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$login_id = $row['login_id'];
				$updated_at = $row['updated_at'];
			}

			//すでにログインボーナス受け取り済
			if( empty($login_id) ){
//			if( $updated_at >= date('Y-m-d 00:00:00')){
				$this->dbh->commit();
				return $next($request);
			}

			//updated_atが昨日より以前ならボーナスポイントを付与しupdated_atを更新する
			//last_access_datetime、sort_last_access_datetimeも更新する
			$stmt = $this->dbh->prepare("update users, grant_points set users.bonus_flg = 1, users.point = users.point + grant_points.point, users.updated_at = now(), users.last_access_datetime = :last_access_datetime, sort_last_access_datetime = :sort_last_access_datetime where users.login_id = :login_id and grant_points.type = :type");
//			$stmt = $this->dbh->prepare("update users, grant_points set users.point = users.point + grant_points.point, users.updated_at = now(), users.last_access_datetime = :last_access_datetime, sort_last_access_datetime = :sort_last_access_datetime where users.login_id = :login_id and grant_points.type = :type and users.updated_at < DATE_FORMAT(now(),'%Y:%m:%d 00:00:00')");
			$stmt->bindValue(":last_access_datetime", $now_date);
			$stmt->bindValue(":sort_last_access_datetime", (int)preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $now_date), PDO::PARAM_INT);
			$stmt->bindValue(":login_id", $login_id);
			$stmt->bindValue(":type", 'loginbonus');
			$update_flg = $stmt->execute();

			$this->dbh->commit();

		//例外エラー
		}catch(\Exception $e){
			$this->dbh->rollback();

			//ログインボーナスは貰えないがそのままスルーさせる
			return $next($request);
		}

		//$update_flgが1なら更新されている
		if( $update_flg ){
			return redirect(config('const.member_bonus_url_path'));
		}

        return $next($request);
    }

	private function _pdoDbCon(){
		//DB接続
		try {
			$this->dbh = DB::connection('mysql')->getPdo();
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策

		} catch (\PDOException $e) {
//			abort('403', __("messages.pdo_connection_err_msg"));
		}
	}

}
