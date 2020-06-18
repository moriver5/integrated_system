<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Create_order_id;
use App\Model\Payment_log;
use Session;
use Carbon\Carbon;
use DB;

class MemberDataMigrationController extends Controller
{
	protected $connection = 'mysql_premium';

	public function __construct()
	{

	}

	/*
	 * データ移行画面を表示
	 */
	public function index($access_key)
	{
		//PREMIUMのDB接続
		$premium_con = DB::connection('mysql_premium');

		//ユーザー情報取得
		$db_data = $premium_con->select("select * from user where is_quit = 0 and disable = 0 and regist_status = 1 and access_key = '{$access_key}';");

		//ログインID・パスワードでも認証前なので会員登録前のトップ画面へリダイレクト
		if( empty($db_data) ){
			return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : config('const.nonmember_top_path'));
		}

		//既にデータ移行済かチェック
		$db_jray_data = DB::select("select * from users where disable = 0 and status = 1 and login_id = '".$db_data[0]->login_id."';");
//		$db_jray_data = DB::select("select * from users where disable = 0 and status = 1 and ( mail_address = '".$db_data[0]->pc_mail_address."' or mail_address = '".$db_data[0]->mb_mail_address."' );");

		//既にデータ移行済なら
		if( !empty($db_jray_data) ){
			$disp_data = [
				'title'					=> 'データ移行',
				'login_url'				=> config('const.base_url').config('const.login_exec_path'),
				'finished_flg'			=> 1,
				'login_id'				=> $db_jray_data[0]->login_id,
				'password'				=> $db_jray_data[0]->password_raw
			];

			return view('data_migration', $disp_data);
		}

		$disp_data = [
			'title'					=> 'データ移行',
			'access_key'			=> $access_key,
			'data_migration_url'	=> config('const.base_url').'/data_migration/send',
		];

		if( !empty($db_data[0]->mb_mail_address) ){
			$disp_data['mb_mail_address'] = $db_data[0]->mb_mail_address;
			$disp_data['mb_mail_status'] = $db_data[0]->mb_mail_status;
		}

		if( !empty($db_data[0]->pc_mail_address) ){
			$disp_data['pc_mail_address'] =	$db_data[0]->pc_mail_address;
			$disp_data['pc_mail_status'] =	$db_data[0]->pc_mail_status;
		}

		$disp_data['dm_mail_status'] = 0;
		if( !empty($db_data[0]->mb_mail_address) && !empty($db_data[0]->pc_mail_address) ){
			$disp_data['dm_mail_status'] = 1;
		}

		return view('data_migration', $disp_data);
	}

	/*
	 * データ移行処理
	 */
	function sendDataMigration(Request $request){
		//トランザクション開始
		DB::beginTransaction();
		try {
			//PREMIUMのDB接続
			$premium_con = DB::connection('mysql_premium');

			//ユーザー情報取得
			$db_data = $premium_con->select("select * from user where access_key = '".$request->input('access_key')."';");

			//ログインID・パスワードでも認証前なので会員登録前のトップ画面へリダイレクト
			if( empty($db_data) ){
				return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : config('const.nonmember_top_path'));
			}

			//既にデータ移行済かチェック
			$db_jray_data = DB::select("select * from users where disable = 0 and status = 1 and login_id = '".$db_data[0]->login_id."';");

			//既にデータ移行済なら
			if( !empty($db_jray_data) ){
				$disp_data = [
					'title'					=> 'データ移行',
					'login_url'				=> config('const.base_url').config('const.login_exec_path'),
					'finished_flg'			=> 1,
					'login_id'				=> $db_jray_data[0]->login_id,
					'password'				=> $db_jray_data[0]->password_raw,
				];

				return view('data_migration', $disp_data);
			}

			//ここからユーザー情報移行

			//入金回数・合計入金額取得
			$pay_count = 0;
			$amount = 0;
			$db_pay_count = $premium_con->select("select user_id,count(user_id) as count,sum(receive_money) as amount from payment_log where user_id = ".$db_data[0]->login_id." group by user_id order by count;");
			if( !empty($db_pay_count) ){
				$pay_count = $db_pay_count[0]->count;
				$amount = $db_pay_count[0]->$amount;
			}

			//アクセスキー生成
			$remember_token = session_create_id();

			$now_date = Carbon::now();

			//DM配信
			$dm_status = 0;
			if( $db_data[0]->mb_mail_status == 1 || $db_data[0]->pc_mail_status == 1 ){
				$dm_status = 1;
			}

			$regist_status = config('const.migration_regist_status');

			//ユーザー情報
			$db_value = [
				'login_id'					 => $db_data[0]->login_id,
				'password'					 => bcrypt($db_data[0]->password),
				'password_raw'				 => $db_data[0]->password,
				'status'					 => $regist_status[$db_data[0]->regist_status],
				'remember_token'			 => $db_data[0]->access_key,
				'mail_status'				 => $dm_status,
				'point'						 => $db_data[0]->point,
				'ad_cd'						 => $db_data[0]->ad_cd,
//				'group_id'					 => $db_data[0]->group_id,
				'group_id'					 => 6,
				'pay_count'					 => $pay_count,
				'pay_amount'				 => $amount,
				'credit_certify_phone_no'	 => $db_data[0]->credit_certify_phone_no,
				'regist_date'				 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $db_data[0]->regist_datetime).'00',
				'temporary_datetime'		 => $db_data[0]->pre_regist_datetime,
				'sort_temporary_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $db_data[0]->pre_regist_datetime).'00',
				'last_access_datetime'		 => $now_date,
				'sort_last_access_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $db_data[0]->pre_regist_datetime).'00',
				'description'				 => $db_data[0]->description,
				'created_at'				 => $db_data[0]->regist_datetime,
				'updated_at'				 => $now_date,
				'disable'					 => 0
			];

			//既にPC/モバイルメールアドレスが登録され、どちらか選択した人
			if( !empty($request->input('dm_mail_address')) ){
				$db_value['mail_address'] = $request->input('dm_mail_address');

			//PCまたはモバイルどちらか登録のある人
			}else{
				if( !empty($db_data[0]->pc_mail_address) ){
					$db_value['mail_address'] = $db_data[0]->pc_mail_address;
				}
				if( !empty($db_data[0]->mb_mail_address) ){
					$db_value['mail_address'] = $db_data[0]->mb_mail_address;
				}
			}

			$client_id = DB::table('users')->insertGetId($db_value);

			//購入履歴取得
			$db_order_data = $premium_con->select("select * from ordering inner join ordering_detail on ordering.id = ordering_detail.ordering_id where ordering.user_id = ".$db_data[0]->id." ;");

			//注文IDごとに複数あるデータ格納
			$listOrderData = [];
			foreach($db_order_data as $lines){
				$listOrderData[$lines->ordering_id][] = $lines;
			}
//error_log(print_r($listOrderData,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

			$pay_count = 0;
			foreach($listOrderData as $order_id => $lines){
//error_log(print_r($lines,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

				//注文IDを生成
				DB::insert("insert ignore into create_order_ids(order_id) select MAX(order_id) + 1 from create_order_ids on duplicate key update order_id = order_id + 1;");

				//注文IDを取得
				$db_order_data = Create_order_id::first();

				$order_id = $db_order_data->order_id;

				$pay_count++;

				foreach($lines as $line){
//error_log(print_r($line,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

					//ポイント購入
					if( $line->buy_point_id > 0 ){
						$type = 1;
						$product_id = $line->buy_point_id;

					//商品購入
					}else{
						$type = 0;
						$product_id = $line->top_contents_id;
					}

					$created_at = $line->create_datetime;
					if( $created_at == '0000-00-00 00:00:00' ){
						$created_at = null;
					}

					$updated_at = $line->update_datetime;
					if( $updated_at == '0000-00-00 00:00:00' ){
						$updated_at = null;
					}
					$sendid = substr(sha1(uniqid(mt_rand(), true)), 0, config('const.sendid_length'));

					$payment_db = new Payment_log([
						'pay_type'		=> config('const.list_premium_convert_pay_type')[$line->pay_type],										//支払い方法
						'login_id'		=> $db_data[0]->login_id,
						'type'			=> $type,																								//商品タイプ
						'product_id'	=> $product_id,																							//商品ID
						'order_id'		=> $order_id,																							//注文ID
						'money'			=> $line->price,
						'point'			=> $line->add_point,
						'ad_cd'			=> $db_data[0]->ad_cd,
						'status'		=> $line->status,																						//支払い状況
						'sendid'		=> $sendid,
						'regist_date'	=> $line->create_datetime,
						'pay_count'		=> $pay_count,
						'sort_date'		=> preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s\d{2}:\d{2}:\d{2}/", "$1$2$3", $line->create_datetime),
						'created_at'	=> $created_at,
						'updated_at'	=> $updated_at
					]);

					//DB保存
					$payment_db->save();
				}
			}

			//すべてのデータ移行完了後にコミット
			DB::commit();

			//既にデータ移行済かチェック
			$db_jray_data = DB::select("select * from users where disable = 0 and status = 1 and login_id = '".$db_data[0]->login_id."';");

			//既にデータ移行済なら
			if( !empty($db_jray_data) ){
				$disp_data = [
					'title'					=> 'データ移行',
					'login_url'				=> config('const.base_url').config('const.login_exec_path'),
					'finished_flg'			=> 1,
					'login_id'				=> $db_jray_data[0]->login_id,
					'password'				=> $db_jray_data[0]->password_raw,
				];

				return view('data_migration', $disp_data);
			}

			//データ移行失敗
			//ロールバック
			DB::rollBack();

			$disp_data = [
				'title'					=> 'データ移行',
				'error'					=> 1,
				'access_key'			=> $request->input('access_key'),
				'data_migration_url'	=> config('const.base_url').'/data_migration/send',
			];

			if( !empty($db_data[0]->mb_mail_address) ){
				$disp_data['mb_mail_address'] = $db_data[0]->mb_mail_address;
				$disp_data['mb_mail_status'] = $db_data[0]->mb_mail_status;
			}

			if( !empty($db_data[0]->pc_mail_address) ){
				$disp_data['pc_mail_address'] =	$db_data[0]->pc_mail_address;
				$disp_data['pc_mail_status'] =	$db_data[0]->pc_mail_status;
			}

			$disp_data['dm_mail_status'] = 0;
			if( !empty($db_data[0]->mb_mail_address) && !empty($db_data[0]->pc_mail_address) ){
				$disp_data['dm_mail_status'] = 1;
			}

			return view('data_migration', $disp_data);

		//例外エラー
		}catch(\Exception $e){
			//ロールバック
			DB::rollBack();

			//PREMIUMのDB接続
			$premium_con = DB::connection('mysql_premium');

			//ユーザー情報取得
			$db_data = $premium_con->select("select * from user where is_quit = 0 and disable = 0 and regist_status = 1 and access_key = '".$request->input('access_key')."';");

			//ログインID・パスワードでも認証前なので会員登録前のトップ画面へリダイレクト
			if( empty($db_data) ){
				return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : config('const.nonmember_top_path'));
			}

			//既にデータ移行済かチェック
			$db_jray_data = DB::select("select * from users where disable = 0 and status = 1 and login_id = '".$db_data[0]->login_id."';");

			//既にデータ移行済なら
			if( !empty($db_jray_data) ){
				$disp_data = [
					'title'					=> 'データ移行',
					'login_url'				=> config('const.base_url').config('const.login_exec_path'),
					'finished_flg'			=> 1,
					'login_id'				=> $db_jray_data[0]->login_id,
					'password'				=> $db_jray_data[0]->password_raw
				];

				return view('data_migration', $disp_data);
			}

			$disp_data = [
				'title'					=> 'データ移行',
				'error'					=> 1,
				'access_key'			=> $request->input('access_key'),
				'data_migration_url'	=> config('const.base_url').'/data_migration/send',
			];

			if( !empty($db_data[0]->mb_mail_address) ){
				$disp_data['mb_mail_address'] = $db_data[0]->mb_mail_address;
				$disp_data['mb_mail_status'] = $db_data[0]->mb_mail_status;
			}

			if( !empty($db_data[0]->pc_mail_address) ){
				$disp_data['pc_mail_address'] =	$db_data[0]->pc_mail_address;
				$disp_data['pc_mail_status'] =	$db_data[0]->pc_mail_status;
			}

			$disp_data['dm_mail_status'] = 0;
			if( !empty($db_data[0]->mb_mail_address) && !empty($db_data[0]->pc_mail_address) ){
				$disp_data['dm_mail_status'] = 1;
			}

			return view('data_migration', $disp_data);
		}
	}
}
