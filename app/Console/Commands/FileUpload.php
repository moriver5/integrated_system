<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use Auth;
use App\Model\User;
use Session;
use Carbon\Carbon;
use Utility;
use Validator;

class FileUpload extends Command
{
	private $log_obj;
	private $log_mx_obj;
	private $log_failed_obj;
	private $log_sys_obj;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'file:upload {db_name} {filename} {ad_cd?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'ファイルアップロードをバックグラウンドで実行';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		//不正メールアドレス用のログ
		$this->log_bad_obj		 = new SysLog(config('const.import_error_log_name'), config('const.save_import_file_dir').config('const.import_error_email_file_name'));

		//重複エラー用のログ
		$this->log_obj			 = new SysLog(config('const.import_error_log_name'), config('const.save_import_file_dir').config('const.import_error_file_name'));

		//MXドメインエラー用のログ
		$this->log_mx_obj		 = new SysLog(config('const.import_mx_domain_error_log_name'), config('const.save_import_file_dir').config('const.import_mx_domain_error_file_name'));

		//ID生成に失敗用のログ
		$this->log_failed_obj	 = new SysLog(config('const.import_failed_log_name'), config('const.save_import_file_dir').config('const.import_failed_create_id_file_name'));

		//システム的なエラー用ログ(アップロードファイルが存在しないときにログ出力)
		$this->log_sys_obj		 = new SysLog(config('const.system_error_log_name'), config('const.system_log_dir_path').config('const.system_error_file_name'));
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		//操作を行うDB名取得
		$db_name		 = $this->argument('db_name');

		//{filename}の引数受取り
		$upload_file = $this->argument('filename');

		//{ad_cd?}の引数受取り
		$ad_cd		 = $this->argument('ad_cd');

		//アップロードファイルが存在していたら
		if( file_exists(config('const.save_import_file_dir').$upload_file) ) {
			//アップロードファイルをオープン
			$fp = fopen(config('const.save_import_file_dir').$upload_file, "r");

			//ID生成の最大ループ回数に達したときのフラグ
			$skip_flg = false;

			$now_date = Carbon::now();

			//アップロードファイルからデータ取り出し
			while( ($listData = fgetcsv($fp, 0, ",")) !== FALSE ){
				//メールアドレスがあるか確認
				foreach($listData as $line){
					//メールアドレスのチェックを@のみで簡易的にチェック
					if( preg_match("/@/",$line) > 0 ){
						$email = mb_strtolower(trim($line));

						//メールアドレスの妥当性チェック(妥当性/最大長さ)
						$validator = Validator::make(['email' => $email], ['email' => 'bail|required|email|max:'.config('const.email_length')]);

						//エラーがあればスキップ
						if ( $validator->fails() ) {
							$this->log_bad_obj->addLog($email);
							continue 2;
						}

						//エラーがなければ
						break;
/*
						$strlen = strlen($email);
						//メールアドレスの長さが255バイト以下なら
						if( $strlen <= config('const.email_length') ){
							break;
						}
 */
					}
				}

				//mail_addressの重複確認
				$db_obj = new User;
				$db_obj->setConnection($db_name);
				$exist_user = $db_obj->where('mail_address', $email)->count();

				//メールアドレスの重複あり、ログ出力、登録スキップして次へ
				if( !empty($exist_user) ){
					//ログ出力
					$this->log_obj->addLog($email);
					continue;
				}

				//MXドメインが存在するか確認
				$exist_flg = Utility::checkMxDomain($email);

				//MXドメインが存在しなかったらログ出力、スキップ
				if( !is_null($exist_flg) ){
					//ログ出力
					$this->log_mx_obj->addLog($email);
					continue;
				}

				//DBに存在しないlogin_idが生成されるまでループしながら生成
				$max_count = 0;
				do{
					//無限ループ回避のため最大回数まで繰り返す
					if( $max_count >= config('const.roop_limit') ){
						//ログ出力
						$this->log_failed_obj->addLog($email);
						
						//スキップフラグをtrue
						$skip_flg = true;
						
						break;
					}
					$max_count++;

					//login_id生成
					$login_id = random_int(config('const.min_login_id'), config('const.max_login_id'));

					//生成されたlogin_idを条件にusersテーブルを検索
					$user = new User;
					$db_obj->setConnection($db_name);
					$user = $db_obj->where('login_id', $login_id)->first();
				}while( !empty($user) );

				//ID生成の試行回数の失敗はスキップ
				if( $skip_flg ){
					continue;
				}
				
				//パスワードを生成
				$password	 = str_random(config('const.password_length'));

				$db_obj = new User([
					'login_id'					=> $login_id,
					'password'					=> bcrypt($password),
					'password_raw'				=> $password,
					'ad_cd'						=> $ad_cd,
					'status'					=> 1,
					'mail_status'				=> 1,
					'mail_address'				=> $email,
					'pay_count'					=> 0,
					'pay_amount'				=> 0,
					'remember_token'			=> session_create_id(),
					'temporary_datetime'		=> $now_date,
					'sort_temporary_datetime'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
					'regist_date'				=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
					'created_at'				=> $now_date,
					'updated_at'				=> $now_date,
				]);

				$db_obj->setConnection($db_name);

				//DB保存
				$db_obj->save();
 
				//0.1秒間スリープ
				usleep(100000);
			}
			fclose($fp);
		}else{
			//アップロードファイルが存在しないときにログ出力
			$this->log_sys_obj->addLog(__('messages.upload_file_not_exist'));
		}
	}
}
