<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Payment_log;
use App\Model\Point_log;
use App\Libs\SysLog;
use Carbon\Carbon;
use DB;
use Session;
use PDO;

class CredixPaymentController extends Controller
{
	private $credit_log_obj;
	private $netbank_log_obj;

	//
	public function __construct()
	{
		//クレジットの決済結果用ログ
		$this->credit_log_obj = new SysLog(config('const.payment_log_name'), config('const.payment_log_dir_path').config('const.payment_log_credit_file_name').date('Ymd').'.log');

		//ネットバンク決済結果用ログ
		$this->netbank_log_obj = new SysLog(config('const.payment_log_name'), config('const.payment_log_dir_path').config('const.payment_log_netbank_file_name').date('Ymd').'.log');

		//クライアントログ(テキスト)
		$this->log_obj = new SysLog(config('const.client_history_log_name'), config('const.client_log_dir_path').config('const.client_member_history_file_name'));
	}
	
	/*
	 * Axes社からクレジット決済結果を受取る処理(Axes社からアクセスされる)
	 */
	public function credixCreditPaymentResponse(Request $request)
	{
		$listResonse = [];

		//現在の時刻を取得
		$now_date = Carbon::now();

		$listResonse = $request->all();

		//クレジット決済結果ログ出力
		$this->credit_log_obj->addLog(implode(",", $listResonse));

		try {
//			$dbh = new PDO('mysql:host=172.16.0.36;dbname=keiba_db;port=3306;charset=utf8','elephpant','elephpant5963');
			$dbh = DB::connection()->getPdo();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策

		} catch (\PDOException $e) {
//error_log("pdo error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			return 'ng';
		}

		try{ 
			//トランザクション開始
			$dbh->beginTransaction();

			//必要なデータ取得
			//select ～ for updateでロック(多重アクセスされてもデータ取得不可)
			//commitされるまでロックされる
			$stmt = $dbh->prepare("select users.login_id, users.mail_address, users.pay_count, users.point, sum(payment_logs.point) as add_point, sum(payment_logs.money) as amount from payment_logs inner join users on payment_logs.login_id = users.login_id where users.mail_address = :mail_address and payment_logs.sendid = :sendid and payment_logs.order_id = :order_id and payment_logs.status != :status group by users.login_id, users.mail_address, payment_logs.order_id, users.pay_count, users.point for update");
			$stmt->bindValue(":mail_address", $listResonse['email']);
			$stmt->bindValue(":sendid", $listResonse['sendid']);
			$stmt->bindValue(":order_id", $listResonse['sendpoint']);
			$stmt->bindValue(":status", config('const.settlement_result')[3]);
			$stmt->execute();
//			throw new \Exception("テスト例外エラー");

			$listUserInfo = [];
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$listUserInfo['login_id']		 = $row['login_id'];
				$listUserInfo['mail_address']	 = $row['mail_address'];
				$listUserInfo['pay_count']		 = $row['pay_count'] + 1;		//以前の入金回数＋１
				$listUserInfo['point']			 = $row['point'];
				$listUserInfo['add_point']		 = $row['add_point'];
				$listUserInfo['amount']			 = $row['amount'];
			}
/*
			//usersテーブルからユーザー情報取得
			$db_data = Payment_log::query()->join('users', 'payment_logs.login_id', '=', 'users.login_id')
					->select('users.login_id', 'users.mail_address', 'users.pay_count', 'users.point', DB::raw('sum(payment_logs.point) as add_point'), DB::raw('sum(payment_logs.money) as amount'))
					->where('users.mail_address', $listResonse['email'])
					->where('payment_logs.sendid', $listResonse['sendid'])
					->where('payment_logs.order_id', $listResonse['sendpoint'])
					->where('payment_logs.status', '!=', config('const.settlement_result')[3])
					->groupBy('users.login_id', 'users.mail_address', 'payment_logs.order_id', 'users.pay_count', 'users.point')
					->lockForUpdate()
					->first();
*/
			//データ存在しないとき
			if( empty($listUserInfo) ){
//error_log("nodata\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
				return 'ng';
			}

			try{
				//決済完了
				if( $listResonse['result'] == 'ok' && $listUserInfo['amount'] == $listResonse['money'] ){

					//pay_type: 1→銀行振込　2→クレジット 3→ネットバンク　0→管理手動(管理画面)
					//payment_logsテーブルに決済情報を登録
					$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[3]);
//						throw new \Exception("テスト例外エラー");

//error_log("アクセス回数：".$listResonse['access_num']." LOGIN ID:".$listUserInfo['login_id']." POINT:".$listUserInfo['add_point']."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

					//ポイント追加、購入回数１加算、入金合計を加算、最終入金日付を更新
					$stmt = $dbh->prepare("update users set pay_amount = pay_amount + :pay_amount, pay_count = pay_count + 1, point = point + :point, pay_datetime = :pay_datetime, sort_pay_datetime = :sort_pay_datetime where login_id = :login_id");
					$stmt->bindValue(":pay_amount", (int)$listResonse['money'], PDO::PARAM_INT);
					$stmt->bindValue(":point", (int)$listUserInfo['add_point'], PDO::PARAM_INT);
					$stmt->bindValue(":login_id", $listUserInfo['login_id']);
					$stmt->bindValue(":pay_datetime", $now_date);
					$stmt->bindValue(":sort_pay_datetime", preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00');
					$stmt->execute();
//					throw new \Exception("テスト例外エラー");

					//追加ポイントがある場合、ポイント履歴更新
					if( $listUserInfo['add_point'] > 0 ){
						//ポイントログ履歴更新
						$log = new Point_log([
							'login_id'					=> $listUserInfo['login_id'],
							'add_point'					=> $listUserInfo['add_point'],
							'prev_point'				=> $listUserInfo['point'],
							'current_point'				=> $listUserInfo['point'] + $listUserInfo['add_point'],
							'operator'					=> 'credit'
						]);

						//データをinsert
						$log->save();
					}
//					throw new \Exception("テスト例外エラー");

					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['mem_pay_credit'].",{$listUserInfo['login_id']}");
//					throw new \Exception("テスト例外エラー");

					$dbh->commit();

					return 'ok';

				//決済失敗
				}else{
//error_log("rollback2\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

					$dbh->rollback();

					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['mem_pay_failed'].",{$listUserInfo['login_id']}");

					//payment_logsテーブルに決済情報を登録
					$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[2]);
//					throw new \Exception("テスト例外エラー");

					return 'ng';	
				}

			//例外エラー
			}catch(\Exception $e){
//error_log($listUserInfo['pay_count']."rollback1\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

				$dbh->rollback();

				//ログ出力
				$this->log_obj->addLog(config('const.display_list')['mem_pay_except_err'].",{$listUserInfo['login_id']}");

				//payment_logsテーブルに決済情報を登録
				$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[2]);
//				throw new \Exception("テスト例外エラー");

				return 'ng';
			}

		}catch(\Exception $e){
			$user_db_data = User::select("login_id", "pay_count")->where("mail_address", $listResonse['email'])->first();
//error_log($user_db_data->login_id.":".$user_db_data->pay_count.":error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			//payment_logsテーブルに決済情報を登録
			$this->_updateProductInfo($dbh, $user_db_data, $listResonse, $now_date, $user_db_data->pay_count, config('const.settlement_result')[2]);

			return 'ng';
		}
	}
	
	/*
	 * Axes社からネットバンク決済結果を受取る処理(Axes社からアクセスされる)
	 */
	public function credixNetbankPaymentResponse(Request $request)
	{
		//現在の時刻を取得
		$now_date = Carbon::now();

		$listResonse = $request->all();

		//ネットバンク決済結果ログ出力
		$this->netbank_log_obj->addLog(implode(",", $listResonse));

		try {
//			$dbh = new PDO('mysql:host=172.16.0.36;dbname=keiba_db;port=3306;charset=utf8','elephpant','elephpant5963');
			$dbh = DB::connection('mysql')->getPdo();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策

		} catch (\PDOException $e) {
//error_log("pdo error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			return 'ng';
		}

		try{ 
			//トランザクション開始
			$dbh->beginTransaction();

			//必要なデータ取得
			//select ～ for updateでロック(多重アクセスされてもデータ取得付加)
			//commitされるまでロックされる
			$stmt = $dbh->prepare("select users.login_id, users.mail_address, users.pay_count, users.point, sum(payment_logs.point) as add_point, sum(payment_logs.money) as amount from payment_logs inner join users on payment_logs.login_id = users.login_id where users.mail_address = :mail_address and payment_logs.sendid = :sendid and payment_logs.order_id = :order_id and payment_logs.status != :status group by users.login_id, users.mail_address, payment_logs.order_id, users.pay_count, users.point for update");
			$stmt->bindValue(":mail_address", $listResonse['email']);
			$stmt->bindValue(":sendid", $listResonse['sendid']);
			$stmt->bindValue(":order_id", $listResonse['sendpoint']);
			$stmt->bindValue(":status", config('const.settlement_result')[3]);
			$stmt->execute();
//			throw new \Exception("テスト例外エラー");

			$listUserInfo = [];
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$listUserInfo['login_id']		 = $row['login_id'];
				$listUserInfo['mail_address']	 = $row['mail_address'];
				$listUserInfo['pay_count']		 = $row['pay_count'] + 1;		//以前の入金回数＋１
				$listUserInfo['point']			 = $row['point'];
				$listUserInfo['add_point']		 = $row['add_point'];
				$listUserInfo['amount']			 = $row['amount'];
			}
/*
			//usersテーブルからユーザー情報取得
			$db_data = Payment_log::query()->join('users', 'payment_logs.login_id', '=', 'users.login_id')
					->select('users.login_id', 'users.mail_address', 'users.pay_count', 'users.point', DB::raw('sum(payment_logs.point) as add_point'), DB::raw('sum(payment_logs.money) as amount'))
					->where('users.mail_address', $listResonse['email'])
					->where('payment_logs.sendid', $listResonse['sendid'])
					->where('payment_logs.order_id', $listResonse['sendpoint'])
					->where('payment_logs.status', '!=', config('const.settlement_result')[3])
					->groupBy('users.login_id', 'users.mail_address', 'payment_logs.order_id', 'users.pay_count', 'users.point')
					->lockForUpdate()
					->first();
*/
			//データ存在しないとき
			if( empty($listUserInfo) ){
//error_log("nodata\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
//				return '0005';
				return 'ng';
			}

			try{

				//振込結果：入金済
				if( $listResonse['status'] == '03' && $listUserInfo['amount'] == $listResonse['money'] ){

					//pay_type: 1→銀行振込　2→クレジット 3→ネットバンク　0→管理手動(管理画面)
					//payment_logsテーブルに決済情報を登録
					$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[3]);
//					throw new \Exception("テスト例外エラー");

//error_log("アクセス回数：".$listResonse['access_num']." LOGIN ID:".$listUserInfo['login_id']." POINT:".$listUserInfo['add_point']."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

					//ポイント追加、購入回数１加算、入金合計を加算、最終入金日付を更新
					$stmt = $dbh->prepare("update users set pay_amount = pay_amount + :pay_amount, pay_count = pay_count + 1, point = point + :point, pay_datetime = :pay_datetime, sort_pay_datetime = :sort_pay_datetime where login_id = :login_id");
					$stmt->bindValue(":pay_amount", (int)$listResonse['money'], PDO::PARAM_INT);
					$stmt->bindValue(":point", (int)$listUserInfo['add_point'], PDO::PARAM_INT);
					$stmt->bindValue(":login_id", $listUserInfo['login_id']);
					$stmt->bindValue(":pay_datetime", $now_date);
					$stmt->bindValue(":sort_pay_datetime", preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00');
					$stmt->execute();
//					throw new \Exception("テスト例外エラー");

					//追加ポイントがある場合、ポイント履歴更新
					if( $listUserInfo['add_point'] > 0 ){
						//ポイントログ履歴更新
						$log = new Point_log([
							'login_id'					=> $listUserInfo['login_id'],
							'add_point'					=> $listUserInfo['add_point'],
							'prev_point'				=> $listUserInfo['point'],
							'current_point'				=> $listUserInfo['point'] + $listUserInfo['add_point'],
							'operator'					=> 'netbank'
						]);

						//データをinsert
						$log->save();
					}
//					throw new \Exception("テスト例外エラー");

					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['mem_pay_netbank'].",{$listUserInfo['login_id']}");
//					throw new \Exception("テスト例外エラー");

					$dbh->commit();

					return 'ok';

				//振込結果：エラー
				}elseif( $listResonse['status'] == '04' ){
					$dbh->rollback();

					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['mem_pay_err']."{$listResonse['error_message']},{$listUserInfo['login_id']}");

					//payment_logsテーブルに決済情報を登録
					$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[6]);

					//0002→金額不足 0003→過剰入金
	//				return $listResonse['error_message'];
					return 'ng';

				//振込結果：入金失敗
				}elseif( $listResonse['status'] == '05' ){
					$dbh->rollback();

					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['mem_pay_failed']."0001,{$listUserInfo['login_id']}");

					//payment_logsテーブルに決済情報を登録
					$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[6]);

					//0001→入金失敗
	//				return '0001';
					return 'ng';
				}

			//例外エラー
			}catch(\Exception $e){
//error_log($listUserInfo['pay_count']."rollback1\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

				$dbh->rollback();

				//ログ出力
				$this->log_obj->addLog(config('const.display_list')['mem_pay_except_err'].",{$listUserInfo['login_id']}");

				//payment_logsテーブルに決済情報を登録
				$this->_updateProductInfo($dbh, $listUserInfo, $listResonse, $now_date, $listUserInfo['pay_count'], config('const.settlement_result')[6]);
//				throw new \Exception("テスト例外エラー");

				return 'ng';
			}

		//例外エラー
		} catch (\Exception $e){
			$user_db_data = User::select("login_id", "pay_count")->where("mail_address", $listResonse['email'])->first();
//error_log($user_db_data->login_id.":".$user_db_data->pay_count.":error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			//payment_logsテーブルに決済情報を登録
			$this->_updateProductInfo($dbh, $user_db_data, $listResonse, $now_date, $user_db_data->pay_count, config('const.settlement_result')[6]);

//			return '0004';
			return 'ng';
		}
	}
	
	private function _updateProductInfo($dbh, $user, $listResonse, $now_date, $pay_count, $status){
		//payment_logsテーブルを更新
		$stmt = $dbh->prepare("update payment_logs set pay_count = :pay_count, status = :status, regist_date = :regist_date where order_id = :order_id");
		$stmt->bindValue(":pay_count", $pay_count);
		$stmt->bindValue(":status", $status);
		$stmt->bindValue(":regist_date", $now_date);
		$stmt->bindValue(":order_id", $listResonse['sendpoint']);
		$stmt->execute();

		//注文IDを破棄
		Session::forget('order_product_ids_'.$user['login_id']);
		Session::forget('order_id_'.$user['login_id']);
	}
}
