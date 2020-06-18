<?php

namespace App\Http\Controllers\Auth;

use App\Model\User;
use App\Model\Mail_content;
use App\Convert_table;
use Illuminate\Http\Request;
use App\Http\Requests\KeibaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\SendMail;
use App\Libs\SysLog;
use App\Model\Grant_point;
use App\Model\Registered_mail;
use App\Model\Registered_mail_queue;
use App\Model\Ad_code;
use Utility;
use Mail;
use Cookie;
use Session;
use Carbon\Carbon;
use DB;
use Storage;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/member/home';
	protected $log_obj;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
		$this->log_obj = new SysLog(config('const.client_nonmember_log_name'), config('const.client_log_dir_path').config('const.client_nonmember_file_name'));
	}
	
	/**
	 * メールアドレス登録ボタン押下後、呼び出される
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function create(Request $request, $lpid = null)
	{
		$ad_cd = '';
//error_log($request->cookie('reg_afi_code').":ad_cd\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
//error_log(Cookie::get('reg_afi_code').":ad_cd\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
//error_log(print_r($_COOKIE,true).":ad_cd\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");

		//広告コードがクッキーに保存されていれば
		if( !empty($request->cookie('reg_afi_code')) ){
			$ad_cd = $request->cookie('reg_afi_code');
		}

		//LPのad_cdに値があれば
		if( !empty($request->input('ad_cd')) ){
			$ad_cd = $request->input('ad_cd');
		}

		//広告コードがあれば
		if( !empty($ad_cd) ){
			$ad_data = Ad_code::where('ad_cd', $ad_cd)->where('single_opt', 1)->first();

			//シングルオプト登録
			if( !empty($ad_data) ){
				return $this->singleoptCreate($request, $lpid, $ad_data);
			}
		}

		//POSTデータ取得(メールアドレス)
		if( $request->has('mail_address') ){
			$to_email = mb_strtolower(trim($request->input('mail_address')));			
		}else{
			$to_email = mb_strtolower(trim($request->input('email')));
		}

		$err_flg = Utility::checkNgWordEmail($to_email);

		//メールアドレスに禁止ワードが含まれていたらトップへリダイレクト
		if( !is_null($err_flg) ){
			return redirect(config('const.nonmember_top_path'));
		}

		//既に仮登録または本登録済かチェック
//		$db_data = User::where('mail_address', $to_email)->where('disable', 0)->first();
		$db_data = User::where('mail_address', $to_email)->first();

		//既に仮登録または本登録されていれば登録済メール送信
		//仮登録:0
		//本登録:1
		//退会:2
		//ブラック:3
		if( !empty($db_data) ){
			//仮登録済なら
			if( $db_data->status == config('const.regist_status')[1][0] ){
				$id = 1;
				$template = config('const.provision_regist');

			//本登録済なら
			}elseif( $db_data->status == config('const.regist_status')[0][0] ){
				$id = 3;
				$template = config('const.registered');

			//退会済
			}elseif( $db_data->status == config('const.regist_status')[2][0] ){
				//テンプレート画面表示で停止
				return view(config('const.quited_email'));

			//ブラック
			}elseif( $db_data->status == config('const.regist_status')[3][0] ){
				//テンプレート画面表示で停止
				return view(config('const.black_email'));
			}

			//自動メールのデータ取得
			$db_cnt = Mail_content::where('id', $id)->first();

			//データがあれば
			if( !empty($db_cnt) ){
				//パスワード生成
				$password = str_random(config('const.password_length'));

				//DBのパスワードは複合化できないので生成したパスワードをupdate
				$update		 = User::where('mail_address', $to_email)
					->where('disable', 0)
					->update([
						'password'		=> bcrypt($password),
						'password_raw'	=> $password,
					]);

				//%変換設定では変換できない文字列の処理
				$body = $db_cnt->body;
				$body = preg_replace("/\-%login_id\-/", $db_data->login_id, $body);
				$body = preg_replace("/\-%password\-/", $password, $body);
				$body = preg_replace("/\-%token\-/", $db_data->remember_token, $body);
				$body = preg_replace("/\-%accessKey\-/", $db_data->remember_token, $body);

				//変換後の文字列を取得
				list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

				list($host_ip, $port) = Utility::getSmtpHost('setting');

				//送信元情報設定
				$options = [
					'host_ip'	 => $host_ip,
					'port'		 => $port,
					'from'		 => $from_mail,
					'from_name'	 => $from_name,
					'subject'	 => $subject,
					'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.$template,
				];

				//送信データ設定
				$data = [
					'contents'		=> $body,
				];

				//仮登録メールアドレス先へメール送信
				Mail::to($to_email)->send( new SendMail($options, $data) );
			}
		}

		//仮登録メールアドレスのエラーチェック
		//メールアドレスの未入力/形式/長さ/重複チェック
		if( !is_null($lpid) ){
			$validator = Validator::make($request->all(), [
				'mail_address'	 => 'bail|required|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address|check_mx_domain',
			]);

			if( $validator->fails() ){
				$errors = $validator->errors();
				return redirect($_SERVER['HTTP_REFERER'])->withErrors(['err' => $errors->first('mail_address')]);
			}

		}else{
			$this->validate($request, [
				'email'	 => 'bail|required|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address|check_mx_domain',
			]);
		}

		//アクセスキー生成
		$remember_token = session_create_id();		

		$now_date = Carbon::now();

		$db_value = [
			'mail_address'				 => $to_email,
			'remember_token'			 => $remember_token,
			'last_access_datetime'		 => $now_date,
			'sort_last_access_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
			'temporary_datetime'		 => $now_date,
			'sort_temporary_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00'
		];

		//広告コードがあれば
		if( !empty($ad_cd) ){
			$ad_data = Ad_code::where('ad_cd', $ad_cd)->first();
			if( !empty($ad_data) ){
				$db_value = array_merge($db_value, ['group_id' => $ad_data->group_id]);			
			}
			//広告コードをupdateデータに追加
			$db_value = array_merge($db_value, ['ad_cd' => $ad_cd]);
		}
		
		//DBにメールアドレスを登録し仮登録する
		$client_id = DB::table('users')->insertGetId($db_value);

		//画面に表示するパラメータ
		$disp_data = [
			'title'		=> '-[仮登録]'
		];

		//仮登録完了メールデータを取得
		$db_cnt = Mail_content::where('id', 1)->first();

		//データがあれば
		if( !empty($db_cnt) ){
			//%変換設定では変換できない文字列の処理
			$body = preg_replace("/\-%token\-/", $remember_token, $db_cnt->body);
			$body = preg_replace("/\-%accessKey\-/", $remember_token, $body);

			//変換後の文字列を取得
			list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

			list($host_ip, $port) = Utility::getSmtpHost('setting');

			//仮登録メール送信
			//送信元情報設定
			$options = [
				'client_id'	 => $client_id,
				'host_ip'	 => $host_ip,
				'port'		 => $port,
				'from'		 => $from_mail,
				'from_name'	 => $from_name,
				'subject'	 => $subject,
				'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.'emails.provision_regist',
			];

			//送信データ設定
			$data = [
//				'complet_url'	=> config('const.base_url').config('const.comp_regist_path').'/'.$remember_token,
				'contents'		=> $body,
			];

			//仮登録メールアドレス先へメール送信
			Mail::to($to_email)->send( new SendMail($options, $data) );

			$disp_data = array_merge(['msg_flg' => 1 ],$disp_data);

		}else{
			//データがないときのメッセージ
			$disp_data = array_merge(['msg_flg' => 0 ],$disp_data);
		}

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['regist'].",{$to_email}");

		if( !is_null($lpid) ){
			return redirect("/".config("const.redirect_landing_url_path")."/{$lpid}/done")->withInput();
		}else{
			//仮登録メール送信完了画面表示
			return view('entry_send', $disp_data);
		}
	}

	/**
	 * 本登録用のURLへアクセスしたとき
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store($sid)
	{
		//アクセスキーがDBに登録されているか確認
		$db_data = User::where('remember_token', $sid)->where('disable', 0)->first();

		//アクセスキーがDBに登録されていない場合(仮登録前)
		if( empty($db_data) ){
			//トップページへリダイレクト
			return redirect(config('const.nonmember_top_path'));
		}else{
			//すでに本登録済だった場合
			if( !empty($db_data->login_id) ){
				//会員ページへリダイレクト
				return redirect(config('const.member_top_path').'/'.$sid);
			}
		}

		//DBに存在しないlogin_idが生成されるまでループしながら生成
		$max_count = 0;
		do{
			//無限ループ回避のため最大回数まで繰り返す
			if( $max_count > config('const.roop_limit') ){
				//トップページへリダイレクト
				return redirect(config('const.nonmember_top_path'));
			}
			$max_count++;

			//login_id生成
			$login_id = random_int(config('const.min_login_id'), config('const.max_login_id'));

			//生成されたlogin_idを条件にusersテーブルを検索
			$user = User::where('login_id', $login_id)->first();
		}while( !empty($user) );

		//パスワードを生成
		$password	 = str_random(config('const.password_length'));

		//付与ポイント取得
		$db_point = Grant_point::where('type', 'registed')->first();

		//デフォルトの付与ポイントが設定されていないとき
		if( empty($db_point) ){
			$point = 0;
		}else{
			$point = $db_point->point;
		}

		$now_date = Carbon::now();

		//updateデータ
		$update_data = [
			'login_id'					 => $login_id,
			'password'					 => bcrypt($password),
			'password_raw'				 => $password,
			'status'					 => 1,
			'mail_status'				 => 1,
			'point'						 => $point,
			'last_access_datetime'		 => $now_date,
			'sort_last_access_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
			'regist_date'				 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
			'created_at'				 => $now_date,
			'updated_at'				 => $now_date,
		];

		//デフォルトのデバイスを2(PC)
		$device = 2;

		//メールアドレスが携帯かPCか判定
		$device_flg = Utility::judgeMobileDevice($db_data->mail_address);

		//メールアドレスが携帯ならデバイスを1(携帯)
		if( is_null($device_flg) ){
			$device = 1;
		}

		//登録後送信メールのデータ
		$registered_data = [
			'client_id'	=> $db_data->id,
			'ad_cd'		=> $db_data->ad_cd,
			'mail'		=> $db_data->mail_address,
			'device'	=> $device							//PC：2 携帯：1
		];

		if( empty($db_data->group_id) ){
			//グループが登録されている場合、一番最初のIDがデフォルトになるらしい
			$group_id = DB::table('groups')->min('id');
			if( !empty($group_id) ){
				$update_data['group_id']		 = $group_id;
				$registered_data['group_id']	 = $group_id;
			}
		}else{
			$group_id = $db_data->group_id;
			$registered_data['group_id']	 = $group_id;
		}

		//remember_tokenを条件にuserテーブルのlogin_id/passwordを登録
		$update		 = User::where('remember_token', $sid)->where('disable', 0)->update($update_data);

		$regstered_db = Registered_mail::where('enable_flg', 1)->get();

		if( count($regstered_db) > 0 ){
			foreach($regstered_db as $lines){
				$registered_data['send_id'] = $lines->id;
				//登録後送信メール用にメールアドレスを登録
				$mail_queue = new Registered_mail_queue($registered_data);
				$mail_queue->save();
			}
		}

		//登録完了メールデータを取得
		$db_cnt = Mail_content::where('id', 2)->first();
		
		//変換設定では変換できない文字列の処理
		$body = preg_replace("/\-%login_id\-/", $login_id, $db_cnt->body);
		$body = preg_replace("/\-%password\-/", $password, $body);
		$body = preg_replace("/\-%token\-/", $db_data->remember_token, $body);
		$body = preg_replace("/\-%accessKey\-/", $db_data->remember_token, $body);

		//変換後の文字列を取得
		list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//本登録メール送信
		//送信元情報設定
		$options = [
			'client_id'	 => $db_data->id,
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'from'		 => $from_mail,
			'from_name'	 => $from_name,
			'subject'	 => $subject,
			'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.'emails.complet_regist',
		];
		
		//Viewと送信データの設定
		$data = [
			'top_url'				=> config('const.base_url'),
			'login_url'				=> config('const.base_url').config('const.login_exec_path'),
			'member_url'			=> config('const.base_url').config('const.member_top_path').'/'.$db_data->remember_token,
			'settlement_url'		=> config('const.base_url').config('const.member_settlement_path'),
			'expectation_free_url'	=> config('const.base_url').config('const.member_expectation_free_path'),
			'expectation_toll_url'	=> config('const.base_url').config('const.member_expectation_toll_path'),
			'hit_url'				=> config('const.base_url').config('const.member_hit_path'),
			'voice_url'				=> config('const.base_url').config('const.member_voice_path'),
			'login_id'				=> $login_id,
			'password'				=> $password,
			'contents'				=> $body,
		];

		//本登録メールアドレス先へメール送信
		Mail::to($db_data->mail_address)->send( new SendMail($options, $data) );

		//画面に表示するパラメータ
		$disp_data = array_merge($data,[
			'title'		=> '-[本登録]'
		]);

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['registend'].",{$db_data->mail_address}");

		//本登録完了画面表示
		return view('entry_end', $disp_data);
	}

	/**
	 * シングルオプトでの登録
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function singleoptCreate(KeibaRequest $request, $lpid = null, $ad_data = null)
	{
		//POSTデータ取得(メールアドレス)
		$to_email = mb_strtolower(trim($request->input('email')));

		//仮登録メールアドレスのエラーチェック
		//メールアドレスの未入力/形式/長さ/重複チェック
		if( !is_null($lpid) ){
			$validator = Validator::make($request->all(), [
				'mail_address'	 => 'bail|required|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address|check_mx_domain',
			]);

			if( $validator->fails() ){
				$errors = $validator->errors();
				return redirect($_SERVER['HTTP_REFERER'])->withErrors(['err' => $errors->first('mail_address')]);
			}

		}else{
			$this->validate($request, [
				'email'	 => 'bail|required|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address|check_mx_domain',
			]);
		}

		$err_flg = Utility::checkNgWordEmail($to_email);

		//メールアドレスに禁止ワードが含まれていたらトップへリダイレクト
		if( !is_null($err_flg) ){
			return redirect(config('const.nonmember_top_path'));
		}

		//メールアドレスがDBに登録されているか確認
		$db_data = User::where('mail_address', $to_email)->where('disable', 0)->first();

		//アクセスキーがDBに登録されていない場合(仮登録前)
		//既に仮登録または本登録されていれば登録済メール送信
		//仮登録:0
		//本登録:1
		//退会:2
		//ブラック:3
		if( !empty($db_data) ){
			//仮登録済なら
			if( $db_data->status == config('const.regist_status')[1][0] ){
				$id = 1;
				$template = config('const.provision_regist');

			//本登録済なら
			}elseif( $db_data->status == config('const.regist_status')[0][0] ){
				$id = 3;
				$template = config('const.registered');

			//退会済
			}elseif( $db_data->status == config('const.regist_status')[2][0] ){
				//テンプレート画面表示で停止
				return view(config('const.quited_email'));

			//ブラック
			}elseif( $db_data->status == config('const.regist_status')[3][0] ){
				//テンプレート画面表示で停止
				return view(config('const.black_email'));
			}

			//自動メールのデータ取得
			$db_cnt = Mail_content::where('id', $id)->first();

			//データがあれば
			if( !empty($db_cnt) ){
				//パスワード生成
				$password = str_random(config('const.password_length'));

				//DBのパスワードは複合化できないので生成したパスワードをupdate
				$update		 = User::where('mail_address', $to_email)
					->where('disable', 0)
					->update([
						'password'		=> bcrypt($password),
						'password_raw'	=> $password,
					]);

				//%変換設定では変換できない文字列の処理
				$body = $db_cnt->body;
				$body = preg_replace("/\-%login_id\-/", $db_data->login_id, $body);
				$body = preg_replace("/\-%password\-/", $password, $body);
				$body = preg_replace("/\-%token\-/", $db_data->remember_token, $body);
				$body = preg_replace("/\-%accessKey\-/", $db_data->remember_token, $body);

				//変換後の文字列を取得
				list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

				list($host_ip, $port) = Utility::getSmtpHost('setting');

				//送信元情報設定
				$options = [
					'host_ip'	 => $host_ip,
					'port'		 => $port,
					'from'		 => $from_mail,
					'from_name'	 => $from_name,
					'subject'	 => $subject,
					'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.$template,
				];

				//送信データ設定
				$data = [
					'contents'		=> $body,
				];

				//仮登録メールアドレス先へメール送信
				Mail::to($to_email)->send( new SendMail($options, $data) );
			}
		}

		//DBに存在しないlogin_idが生成されるまでループしながら生成
		$max_count = 0;
		do{
			//無限ループ回避のため最大回数まで繰り返す
			if( $max_count > config('const.roop_limit') ){
				//トップページへリダイレクト
				return redirect(config('const.nonmember_top_path'));
			}
			$max_count++;

			//login_id生成
			$login_id = random_int(config('const.min_login_id'), config('const.max_login_id'));

			//生成されたlogin_idを条件にusersテーブルを検索
			$user = User::where('login_id', $login_id)->first();
		}while( !empty($user) );

		//パスワードを生成
		$password	 = str_random(config('const.password_length'));

		//アクセスキー生成
		$remember_token = session_create_id();		

		//付与ポイント取得
		$db_point = Grant_point::where('type', 'registed')->first();

		//デフォルトの付与ポイントが設定されていないとき
		if( empty($db_point) ){
			$point = 0;
		}else{
			$point = $db_point->point;
		}

		$now_date = Carbon::now();

		//updateデータ
		$db_value = [
			'mail_address'				 => $to_email,
			'remember_token'			 => $remember_token,
			'login_id'					 => $login_id,
			'password'					 => bcrypt($password),
			'password_raw'				 => $password,
			'status'					 => 1,
			'mail_status'				 => 1,
			'point'						 => $point,
			'last_access_datetime'		 => $now_date,
			'temporary_datetime'		 => $now_date,
			'sort_temporary_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
			'sort_last_access_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
			'regist_date'				 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
			'created_at'				 => $now_date,
			'updated_at'				 => $now_date,
		];

		//デフォルトのデバイスを2(PC)
		$device = 2;

		//メールアドレスが携帯かPCか判定
		$device_flg = Utility::judgeMobileDevice($to_email);

		//メールアドレスが携帯ならデバイスを1(携帯)
		if( is_null($device_flg) ){
			$device = 1;
		}

		//広告コードがあれば
		$ad_cd = "";
		$group_id = "";
		if( !is_null($ad_data) ){
			$db_value = array_merge($db_value, [
				'group_id' => $ad_data->group_id,
				'ad_cd' => $ad_data->ad_cd
			]);	
			$ad_cd = $ad_data->ad_cd;
			$group_id = $ad_data->group_id;
		}

		//DBにメールアドレスを登録し仮登録する
		$client_id = DB::table('users')->insertGetId($db_value);

		//登録後送信メールのデータ
		$registered_data = [
			'client_id'	=> $client_id,
			'ad_cd'		=> $ad_cd,
			'mail'		=> $to_email,
			'device'	=> $device							//PC：2 携帯：1
		];

		//グループが登録されている場合、一番最初のIDがデフォルトになるらしい
		if( empty($group_id) ) {
			$group_id = DB::table('groups')->min('id');
		}

		if( !empty($group_id) ){
			$registered_data['group_id']	 = $group_id;
		}

		//登録後送信メール用にメールアドレスを登録
		$mail_queue = new Registered_mail_queue($registered_data);
		$mail_queue->save();

		//登録完了メールデータを取得
		$db_cnt = Mail_content::where('id', 2)->first();
		
		//変換設定では変換できない文字列の処理
		$body = preg_replace("/\-%login_id\-/", $login_id, $db_cnt->body);
		$body = preg_replace("/\-%password\-/", $password, $body);
		$body = preg_replace("/\-%token\-/", $remember_token, $body);
		$body = preg_replace("/\-%accessKey\-/", $remember_token, $body);
		
		//変換後の文字列を取得
		list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//本登録メール送信
		//送信元情報設定
		$options = [
			'client_id'	 => $client_id,
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'from'		 => $from_mail,
			'from_name'	 => $from_name,
			'subject'	 => $subject,
			'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.'emails.complet_regist',
		];
		
		//Viewと送信データの設定
		$data = [
			'top_url'				=> config('const.base_url'),
			'login_url'				=> config('const.base_url').config('const.login_exec_path'),
			'member_url'			=> config('const.base_url').config('const.member_top_path').'/'.$remember_token,
			'settlement_url'		=> config('const.base_url').config('const.member_settlement_path'),
			'expectation_free_url'	=> config('const.base_url').config('const.member_expectation_free_path'),
			'expectation_toll_url'	=> config('const.base_url').config('const.member_expectation_toll_path'),
			'hit_url'				=> config('const.base_url').config('const.member_hit_path'),
			'voice_url'				=> config('const.base_url').config('const.member_voice_path'),
			'login_id'				=> $login_id,
			'password'				=> $password,
			'contents'				=> $body,
		];

		//本登録メールアドレス先へメール送信
		Mail::to($to_email)->send( new SendMail($options, $data) );

		//画面に表示するパラメータ
		$disp_data = array_merge($data,[
			'title'		=> '-[本登録]'
		]);

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['registend'].",{$to_email}");

		$app = app();

		//本登録完了画面表示
		if( !empty($request->input('lpid')) ){
			//LPのdoneページまでのファイルパス
			$done_real_file_path = $app['config']['view.lp_paths'].'/'.$request->input('lpid').'/done';

			//doneファイルが存在すれば
			if( \File::exists($done_real_file_path) ){
				return redirect("/".config("const.redirect_landing_url_path")."/".$request->input('lpid')."/done")->withInput();

			//doneファイルがなければデフォルトを表示
			}else{
				return view('entry_end', $disp_data);
			}
		}else{
			return view('entry_end', $disp_data);
		}
	}
}
