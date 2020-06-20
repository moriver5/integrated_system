<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use App\Model\User;
use App\Model\Create_order_id;
use App\Model\Payment_log;
use App\Model\Migration_failed_user;

use Session;
use Carbon\Carbon;
use DB;

class UserDataMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data_migration:start {origin_db_name} {dest_db_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'データベースのデータ移行を行う';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$origin_db	 = $this->argument("origin_db_name");
		$dest_db	 = $this->argument("dest_db_name");

		if( empty($origin_db) ){
			echo "移行元のデータベース名を指定してください\n";
			exit;
		}
		if( empty($dest_db) ){
			echo "移行先のデータベース名を指定してください\n";
			exit;
		}

		try {
			//移行元とDB接続
			$dbcon = DB::connection($origin_db);
echo "移行開始:{$origin_db}\n";
			//ユーザー情報取得
			$db_total = $dbcon->select("select count(*) as count from user;");
//echo print_r($db_data,true)."\n";
echo "元データ件数：".$db_total[0]->count."件\n";
			$stop_count = 3;
			$user_count = 0;
			$max_count = ceil($db_total[0]->count / 5000);
//			$max_count = ceil($db_total[0]->count / 20);
echo $max_count."\n";
			for($i=0;$i<=$max_count;$i++){
				$offset = 5000 * $i;
//				$offset = 20 * $i;
				//最終アクセス日時が最新データから取得
				$db_data = $dbcon->select("select * from user order by last_access_datetime desc limit {$offset},5000;");
				//ユーザー情報を取り出す
echo "取得件数：".count($db_data)."\n";
echo "処理件数：{$user_count}\n";

				foreach($db_data as $index => $user_lines){
					$now_date = Carbon::now();

					//トランザクション開始
					DB::connection($dest_db)->beginTransaction();

					try{
/*
						if( $stop_count == $user_count ){
							echo "処理を停止しました\n";
							exit;
						}
*/
						//仮登録・本登録以外はスキップ
//						if( !in_array($user_lines->regist_status, [0,1]) ){
//							continue;
//						}
						$validator	 = null;
						$email		 = [];
						$login_id	 = "";
						
						$login_id			 = $user_lines->login_id;
						$listValidateValue	 = ['login_id' => $user_lines->login_id];
						$listValidate		 = ['login_id' => 'bail|required|unique:'.$dest_db.'.users_copy,login_id'];
						
						if( !empty($user_lines->pc_mail_address) ){
							$email[]						 = $user_lines->pc_mail_address;
							$listValidateValue['pc_email']	 = $user_lines->pc_mail_address;
							$listValidate['pc_email']		 = 'bail|required|unique:'.$dest_db.'.users_copy,mail_address|unique:'.$dest_db.'.users_copy,mobile_mail_address|max:'.config('const.email_length');
						}
						
						if( !empty($user_lines->mb_mail_address) ){
							$email[]						 = $user_lines->mb_mail_address;
							$listValidateValue['email']		 = $user_lines->mb_mail_address;
							$listValidate['email']			 = 'bail|required|unique:'.$dest_db.'.users_copy,mail_address|unique:'.$dest_db.'.users_copy,mobile_mail_address|max:'.config('const.email_length');
						}
						
						if( !empty($listValidateValue['login_id']) ){
							$validator = Validator::make($listValidateValue, $listValidate);
						}

						//エラーがあればスキップ
						if ( empty($email) || empty($listValidateValue['login_id']) || (!empty($validator) && $validator->fails()) ) {
echo "skip:".$user_lines->login_id."\n";
							$listErr = [];
							$massage = [];
							if ( !empty($validator) && $validator->fails() ){
								foreach($validator->failed() as $column => $lines){
									foreach($lines as $err => $values ){
										$massage[] = $column.":".$err;
										break;
									}
								}
								$listErr[] = implode(",",$massage);
//								$listErr[] = $validator->messages();
							}
							if ( empty($email) ){
								$listErr[] = 'email:none';
							}
							if ( empty($listValidateValue['login_id']) ){
								$listErr[] = 'login_id:none';
							}
							$listValidateValue = [];
							$listValidate = [];
							$db_value = [];
							$failed_value = [
								'status' => $user_lines->regist_status,
								'is_quit' => $user_lines->is_quit,
								'disable' => $user_lines->disable,
								'client_id'	=> $user_lines->id,
								'created_at' => $now_date,
								'updated_at' => $now_date
							];
							if( !empty($listErr) ){
								$failed_value['memo'] = implode(",", $listErr);
							}
							if( !empty($user_lines->description) ){
								$failed_value['description'] = $user_lines->description;
							}
							if( !empty($user_lines->login_id) ){
								$failed_value['login_id'] = $user_lines->login_id;
							}
							if( count($email) > 0 ){
								$failed_value['email'] = implode(",", $email);
							}
							if( !empty($user_lines->last_access_datetime) ){
								$failed_value['last_access_date'] = $user_lines->last_access_datetime;
							}
							if( !empty($user_lines->regist_datetime) ){
								$failed_value['reg_date'] = $user_lines->regist_datetime;
							}
							DB::connection($dest_db)->table('migration_failed_users')->insert($failed_value);
							DB::connection($dest_db)->commit();
							continue;
						}

						//ユーザー情報
						$db_value = [];

						//ログインID
						if( !empty($user_lines->login_id) ){
							$db_value['login_id'] = $user_lines->login_id;
						}

						//パスワード
						if( !empty($user_lines->password) ){
							$db_value['password_raw'] = $user_lines->password;
							$db_value['password'] = bcrypt($db_value['password_raw']);
						}

						//会員ステータス
						if( !empty($user_lines->regist_status) ){
							$regist_status = config('const.migration_regist_status');
							$db_value['status'] = $regist_status[$user_lines->regist_status];
						}

						//DM配信
						$db_value['mail_status'] = 0;
						if( $user_lines->mb_mail_status == 1 || $user_lines->pc_mail_status == 1 ){
							$db_value['mail_status'] = 1;
						}

						//トークン
						//アクセスキー生成
	//					$remember_token = session_create_id();
						if( !empty($user_lines->access_key) ){
							$db_value['remember_token'] = $user_lines->access_key;						
						}

						//ポイント
						if( !empty($user_lines->point) ){
							$db_value['point'] = $user_lines->point;						
						}

						//広告ID
						if( !empty($user_lines->ad_cd) ){
							$db_value['ad_cd'] = $user_lines->ad_cd;						
						}

						//グループID(id=6は移行したデータのグループ)
	//					if( !empty($user_lines->group_id) ){
	//						$db_value['group_id'] = $user_lines->group_id;						
							$db_value['group_id'] = 6;
	//					}

						//入金回数・合計入金額取得
						$db_pay_count = $dbcon->select("select user_id,count(user_id) as count,sum(receive_money) as amount from payment_log where user_id = ".$user_lines->id." group by user_id order by count;");

						$db_value['pay_count'] = 0;
						$db_value['pay_amount'] = 0;
						if( !empty($db_pay_count) ){
							$db_value['pay_count'] = $db_pay_count[0]->count;
							$db_value['pay_amount'] = $db_pay_count[0]->amount;
						}

						if( !empty($user_lines->credit_certify_phone_no) ){
							$db_value['credit_certify_phone_no'] = $user_lines->credit_certify_phone_no;						
						}

						if( !empty($user_lines->regist_datetime) && $user_lines->regist_datetime != "0000-00-00 00:00:00" ){
							$db_value['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $user_lines->regist_datetime).'00';						
						}

						if( !empty($user_lines->pre_regist_datetime) && $user_lines->pre_regist_datetime != "0000-00-00 00:00:00" ){
							$db_value['temporary_datetime'] = $user_lines->pre_regist_datetime;						
							$db_value['sort_temporary_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $db_value['temporary_datetime']).'00';						
						}

						$db_value['last_access_datetime'] = $now_date;						
						$db_value['sort_last_access_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $db_value['last_access_datetime']).'00';						

						if( !empty($user_lines->description) ){
							$db_value['description'] = $user_lines->description;						
						}

						if( !empty($user_lines->regist_datetime) && $user_lines->regist_datetime != "0000-00-00 00:00:00" ){
							$db_value['created_at'] = $user_lines->regist_datetime;						
						}

						$db_value['updated_at'] = $now_date;						
						$db_value['disable'] = $user_lines->disable;						

						if( !empty($user_lines->pc_mail_address) ){
							$db_value['mail_address'] = $user_lines->pc_mail_address;
						}
						if( !empty($user_lines->mb_mail_address) ){
							$db_value['mail_address'] = $user_lines->mb_mail_address;
							if( !empty($user_lines->pc_mail_address) ){
								$db_value['mobile_mail_address'] = $user_lines->pc_mail_address;
							}
						}

						if( $user_lines->is_quit == 1 && !empty($user_lines->quit_datetime) && $user_lines->quit_datetime != '0000-00-00 00:00:00' ){
							$db_value['quit_datetime'] = $user_lines->quit_datetime;
							$db_value['sort_quit_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $db_value['quit_datetime']).'00';
						}

						if( $db_value['disable'] == 0 ){
							//ログインID、メールアドレスが既に登録されているか
//							$create_db = new User;
//							$create_db->setConnection($dest_db);
							$exist_count = DB::connection($dest_db)->table('users_copy')
											->where(function($query) use($db_value){
												$query->orWhere('login_id', $db_value['login_id']);
												$query->orWhere('mail_address', $db_value['mail_address']);
												if( isset($db_value['mobile_mail_address']) ){
													$query->orWhere('mobile_mail_address', $db_value['mobile_mail_address']);
												}
												return $query;
											})->count();

							if( $exist_count == 0 ){
								//DBへ登録
								$client_id = DB::connection($dest_db)->table('users_copy')->insertGetId($db_value);
								$db_value = [];
							}else{
								$failed_value = [
									'status' => $user_lines->regist_status,
									'is_quit' => $user_lines->is_quit,
									'disable' => $user_lines->disable,
									'client_id'	=> $user_lines->id,
									'created_at' => $now_date,
									'updated_at' => $now_date
								];
								$listErr = [];
								if ( empty($email) ){
									$listErr[] = 'email:none';
								}
								if ( empty($login_id) ){
									$listErr[] = 'login_id:none';
								}
								if( !empty($listErr) ){
									$failed_value['memo'] = implode(",", $listErr);
								}
								if( !empty($email) ){
									$failed_value['email'] = implode(",", $email);
								}
								if( !empty($user_lines->login_id) ){
									$failed_value['login_id'] = $user_lines->login_id;
								}
								if( !empty($user_lines->last_access_datetime) ){
									$failed_value['last_access_date'] = $user_lines->last_access_datetime;
								}
								if( !empty($user_lines->regist_datetime) ){
									$failed_value['reg_date'] = $user_lines->regist_datetime;
								}
								if( !empty($user_lines->description) ){
									$failed_value['description'] = $user_lines->description;
								}
								DB::connection($dest_db)->table('migration_failed_users')->insert($failed_value);
								DB::connection($dest_db)->commit();
								$db_value = [];
								$listValidateValue = [];
								$listValidate = [];
echo "skip2:".$user_lines->login_id."\n";
								continue;
							}
						}

						//購入履歴取得
						$db_order_data = $dbcon->select("select * from ordering inner join ordering_detail on ordering.id = ordering_detail.ordering_id where ordering.user_id = ".$user_lines->id." ;");
	//echo print_r($db_order_data);
						if( !empty($db_order_data) ){
							//注文IDごとに複数あるデータ格納
							$listOrderData = [];
							foreach($db_order_data as $order_lines){
								$listOrderData[$order_lines->ordering_id][] = $order_lines;
							}
	//error_log(print_r($listOrderData,true)."\n",3,"/data/www/tclb/storage/logs/nishi_log.txt");

							$pay_count = 0;
							foreach($listOrderData as $order_id => $order_lines){
	//error_log(print_r($lines,true)."\n",3,"/data/www/tclb/storage/logs/nishi_log.txt");

								//注文IDを生成
								DB::connection($dest_db)->insert("insert ignore into create_order_ids_copy(order_id) select MAX(order_id) + 1 from create_order_ids_copy on duplicate key update order_id = order_id + 1;");

								//注文IDを取得
								$db_order_data = DB::connection($dest_db)->select('select * from create_order_ids_copy');
	//echo print_r($db_order_data);
								$order_id = $db_order_data[0]->order_id;

								if( !empty($order_lines->status) && $order_lines->status == 3 ){
									$pay_count++;
								}

								foreach($order_lines as $order_items){
	//error_log(print_r($line,true)."\n",3,"/data/www/tclb/storage/logs/nishi_log.txt");

									//ポイント購入
									if( $order_items->buy_point_id > 0 ){
										$type = 1;
										$product_id = $order_items->buy_point_id;

									//商品購入
									}else{
										$type = 0;
										$product_id = $order_items->top_contents_id;
									}

									$created_at = $order_items->create_datetime;
									if( $created_at == '0000-00-00 00:00:00' || $created_at == '' ){
										$created_at = 'null';
									}else{
										$created_at = "'".$order_items->create_datetime."'";
									}

									$updated_at = $order_items->update_datetime;
									if( $updated_at == '0000-00-00 00:00:00' || $updated_at == '' ){
										$updated_at = 'null';
									}else{
										$updated_at = "'".$order_items->update_datetime."'";
									}
	//								$sendid = substr(sha1(uniqid(mt_rand(), true)), 0, config('const.sendid_length'));
									$sendid = $user_lines->login_id;

									DB::connection($dest_db)
										->insert("insert into payment_logs_copy("
										. "pay_type,"
										. "agency_id,"
										. "login_id,"
										. "type,"
										. "product_id,"
										. "order_id,"
										. "money,"
										. "point,"
										. "ad_cd,"
										. "status,"
										. "sendid,"
										. "regist_date,"
										. "pay_count,"
										. "sort_date,"
										. "created_at,"
										. "updated_at) values("
										. config('const.list_premium_convert_pay_type')[$order_items->pay_type].","
										. "2,'"
										. $user_lines->login_id."',"
										. $type.","
										. $product_id.","
										. $order_id.","
										. $order_items->price.","
										. $order_items->add_point.",'"
										. $user_lines->ad_cd."','"
										. $order_items->status."','"
										. $sendid."','"
										. $order_items->create_datetime."',"
										. $pay_count.","
										. preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s\d{2}:\d{2}:\d{2}/", "$1$2$3", $order_items->create_datetime).","
										. $created_at.","
										. $updated_at.");");
/*
									$payment_db = new Payment_log([
										'pay_type'		=> config('const.list_premium_convert_pay_type')[$order_items->pay_type],				//支払い方法
 *										'agency_id'		=> 2,																					//決済会社(1:Telecom　2:Axes　3:Credix)
										'login_id'		=> $user_lines->login_id,
										'type'			=> $type,																				//商品タイプ
										'product_id'	=> $product_id,																			//商品ID
										'order_id'		=> $order_id,																			//注文ID
										'money'			=> $order_items->price,
										'point'			=> $order_items->add_point,
										'ad_cd'			=> $user_lines->ad_cd,
										'status'		=> $order_items->status,																//支払い状況
										'sendid'		=> $sendid,
										'regist_date'	=> $order_items->create_datetime,
										'pay_count'		=> $pay_count,
										'sort_date'		=> preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s\d{2}:\d{2}:\d{2}/", "$1$2$3", $order_items->create_datetime),
										'created_at'	=> $created_at,
										'updated_at'	=> $updated_at
									]);

									//移行先DBへ接続
									$payment_db->setConnection($dest_db);

									//DB保存
									$payment_db->save();
 */
								}
							}

							DB::connection($dest_db)->update("update users_copy set pay_count = ".$pay_count." where id = ".$user_lines->id.";");
						}

						//すべてのデータ移行完了後にコミット
						DB::connection($dest_db)->commit();
	/*
						//既にデータ移行済かチェック
						$db_tclb_data = DB::select("select * from users where disable = 0 and status = 1 and login_id = '".$user_lines->login_id."';");

						//既にデータ移行済なら
						if( !empty($db_tclb_data) ){
							$disp_data = [
								'title'					=> 'データ移行',
								'login_url'				=> config('const.base_url').config('const.login_exec_path'),
								'finished_flg'			=> 1,
								'login_id'				=> $db_tclb_data[0]->login_id,
								'password'				=> $db_tclb_data[0]->password_raw,
							];

							return view('data_migration', $disp_data);
						}
	*/
						$user_count++;

					//例外エラー
					}catch(\Exception $e){
						//ロールバック
						DB::connection($dest_db)->rollBack();

						echo "データ移行に失敗しました：".$user_lines->login_id."\n";
						echo $e->getMessage()."\n";
						exit;
					}
				}
			}
			echo "処理件数：{$user_count}\n";

		//例外エラー
		}catch(\Exception $e){
			//ロールバック
			DB::connection($dest_db)->rollBack();
/*
			//PREMIUMのDB接続
			$premium_con = DB::connection('mysql_premium');

			//ユーザー情報取得
			$db_data = $premium_con->select("select * from user where is_quit = 0 and disable = 0 and regist_status = 1 and access_key = '".$request->input('access_key')."';");

			//ログインID・パスワードでも認証前なので会員登録前のトップ画面へリダイレクト
			if( empty($db_data) ){
				return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : config('const.nonmember_top_path'));
			}

			//既にデータ移行済かチェック
			$db_tclb_data = DB::select("select * from users where disable = 0 and status = 1 and login_id = '".$db_data[0]->login_id."';");

			//既にデータ移行済なら
			if( !empty($db_tclb_data) ){
				$disp_data = [
					'title'					=> 'データ移行',
					'login_url'				=> config('const.base_url').config('const.login_exec_path'),
					'finished_flg'			=> 1,
					'login_id'				=> $db_tclb_data[0]->login_id,
					'password'				=> $db_tclb_data[0]->password_raw
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
 */
		}
    }
}
