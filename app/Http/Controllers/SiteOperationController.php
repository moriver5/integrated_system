<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Libs\SysLog;
use App\Libs\ClientLog;
use App\Http\Requests\KeibaRequest;
use App\Model\User;
use App\Model\Content;
use App\Mail\SendMail;
use App\Model\Mail_content;
use App\Model\Ad_code;
use App\Model\Tipster;
use App\Model\Landing_pages_content;
use Utility;
use Mail;
use Auth;
use Agent;
use Storage;
use Session;

class SiteOperationController extends Controller
{
	protected $log_obj;
	protected $pv_log_obj;

	public function __construct()
	{
		//ログ出力
		$this->log_obj = new SysLog(config('const.client_nonmember_log_name'), config('const.client_log_dir_path').config('const.client_nonmember_file_name'));

		//PV用ログ
		$this->pv_log_obj = new ClientLog();
	}

	/**
	 * 会員前トップページ
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, $id = '', $file = '')
	{
		try{

			//広告コード取得
			$ad_cd = $request->input('afl');

			//ログイン認証済の場合、会員トップへリダイレクト
			if ( Auth::check() ) {
				return redirect()->to(config('const.member_top_path'));
			}

			//ユーザーエージェント取得
			$ua = $request->server->get('HTTP_USER_AGENT');

			$app = app();

			if( !empty($id) ){
				//$file指定がないときデフォルトを設定
				if( empty($file) ){
					$file = 'index';			
				}

				//モバイルなら
				if ( Agent::isMobile($ua) ) {
					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['mobile_top']);

					//PV出力
					$this->pv_log_obj->addPvLogDb(config('const.display_list')['mobile_top']);

					$type = 'sp';

					$db_lp_data = Landing_pages_content::where([
						'lp_id'	=> $id,
						'type'	=> 2,
						'name'	=> $file
					])->first();

					if( empty($db_lp_data) || !$db_lp_data->url_open_flg ){
						$type = 'pc';
					}

	//print $app['config']['view.paths'][0].'/'.$app['config']['const']['list_site_const'][$_SERVER['SERVER_NAME']]."<br>";
					//ファイル名までのフルパス名生成
					$file_path		 = config('const.public_full_path').'/'.$_SERVER['SERVER_NAME'].'/'.config('const.landing_dir_path')."/{$id}/{$type}/$file";

					//Storage::disk('local')->get($file_path))でのランディングページまでのパス
					$storage_path	 = config("const.public_dir_path")."/".$_SERVER['SERVER_NAME'].'/'.config("const.landing_dir_path")."/{$id}/{$type}/{$file}";
	//echo $file_path."<br>";

				//PCなら
				} else {
					//ログ出力
					$this->log_obj->addLog(config('const.display_list')['pc_top']);

					//PV出力
					$this->pv_log_obj->addPvLogDb(config('const.display_list')['pc_top']);

					$type = 'pc';

					$db_lp_data = Landing_pages_content::where([
						'lp_id'	=> $id,
						'type'	=> 0,
						'name'	=> $file
					])->first();

					if( !$db_lp_data->url_open_flg ){
						$type = 'sp';
					}

					//ファイル名までのフルパス名生成
					$file_path		 = config('const.public_full_path').'/'.$_SERVER['SERVER_NAME'].'/'.config('const.landing_dir_path')."/{$id}/{$type}/{$file}";

					//Storage::disk('local')->get($file_path))でのランディングページまでのパス
					$storage_path	 = config("const.public_dir_path")."/".$_SERVER['SERVER_NAME'].'/'.config("const.landing_dir_path")."/{$id}/{$type}/{$file}";
				}

				//パスがディレクトリなら
				if( is_dir($file_path) ){
					$file_path .= 'index';
					$storage_path .= 'index';
				}
				//http://ドメイン/ディレクトリ名/テンプレートID
				//ディレクトリ名:$dir
				//テンプレートID:$id
				//ディレクトリ以外でパラメータ付きのファイルが実際に存在するなら
				if ( \File::exists($file_path) ) {
					if( preg_match("/(css|js)$/", $file_path) > 0 ){
						return redirect(config("const.landing_dir_path")."/{$id}/{$type}/{$file}");
					}else{
						$lp_contents = Storage::disk('local')->get($storage_path);
						$lp_contents = Utility::getConvertData($lp_contents);
						$lp_contents = Utility::getByTypeReplaceData($id, $lp_contents);

						if( $request->session()->has('_old_input') ){
							//顧客データ依存の置換文字を変換
							$lp_contents = preg_replace("/\-%regmail\-/", $request->session()->get('_old_input')['mail_address'], $lp_contents);
							$lp_contents = preg_replace("/\-%usermail\-/", $request->session()->get('_old_input')['mail_address'], $lp_contents);
						}

						//エラー取得
						$errmsg = $request->session()->get('errors');

						//エラーがあれば
						if( !empty($errmsg) ){
							//エラー置換文字を変換
							$lp_contents = preg_replace("/\-%errormsg\-/", $errmsg->first(), $lp_contents);
						}else{
							$lp_contents = preg_replace("/\-%errormsg\-/", '', $lp_contents);				
						}

						//広告コード付きのURLからアクセスしてきた場合
						if( !empty($ad_cd) ){
							$ad_log_obj	 = new ClientLog();

							//day_pv_logsテーブルへログ出力
							$ad_log_obj->addAdLogDb($ad_cd);

							//広告コードをクッキーに付与してデフォルトの会員前トップ画面表示
							return response($lp_contents)->cookie('reg_afi_code', $ad_cd, config('const.aff_cookie_expire_time'));
						}else{
							return response($lp_contents)->cookie('reg_afi_code');
						}
					}
				}
			}

			//3つ星予想師取得
			$tipster = Tipster::where('is_star', 1)->first();

			//的中データを取得
			[$db_data, $list_hit_data] = Utility::getHitAchievements($request);

			//会員の喜びの声を取得
			list($listVoice,$page_link) = Utility::getVoice();

			//%変換リスト取得
			$list_convert_data = Utility::getListConvertKeyValue();

			//今週のSPレースのコンテンツを取得
			$sp_race_data = '';
			$sp_db_race_data = Content::where('id', 5)->first();
			if( !empty($sp_db_race_data) ){
				$sp_race_data = Utility::getConvertData($sp_db_race_data->contents);
			}

			//画面に表示するパラメータ
			$disp_data = [
				'title'				 => '',
				'tipster'			 => $tipster,
				'list_convert_data'	 => $list_convert_data,
				'sp_race_data'		 => $sp_race_data,
				'list_hit_data'		 => $list_hit_data,
				'list_voice'		 => $listVoice,
			];

			//広告コード付きのURLからアクセスしてきた場合
			if( !empty($ad_cd) ){
				$ad_log_obj	 = new ClientLog();

				//day_pv_logsテーブルへログ出力
				$ad_log_obj->addAdLogDb($ad_cd);

				$disp_data['ad_cd'] = $ad_cd;

				//広告コードをクッキーに付与してデフォルトの会員前トップ画面表示
				return response(view('entry_top', $disp_data))->cookie('reg_afi_code', $ad_cd, config('const.aff_cookie_expire_time'));
			}else{
				//デフォルトの会員前トップ画面表示
				return response(view('entry_top', $disp_data))->cookie('reg_afi_code');			
			}

		}catch(\Exception $e){
			//例外エラー用
			return view('error');
		}
	}

	/**
	 * ログイン専用画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sp_login()
	{
		$disp_data = [
			'title'		=> config('const.outline'),
			'contents'	=> '',
		];

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['dedicated_login']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['dedicated_login']);

		//画面表示
		return view('sp_login', $disp_data);
	}

	/**
	 * ログインID・パスワード忘れの画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function forget(Request $request)
	{
		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['forget']);

		$data = [
			'title'		=> config('const.list_title')['forget'],
			'send_msg'	=> ''
		];

		//メール送信後のリダイレクトのメッセージを設定
		if( $request->input("status") == config("const.forget_end_status") ){
			$data['send_msg']	= __('messages.password_forget');
			return view('forget_end', $data);
		}

		//ログインID・パスワード忘れ画面表示
		return view('forget', $data);
	}

	/**
	 * ログインID・パスワード忘れお問い合わせ処理
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function forgetSend(KeibaRequest $request)
	{
		//メールアドレスの未入力/形式/
		$this->validate($request, [
			'email'	 => 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
		]);

		//POSTデータ取得(メールアドレス)
		$to_email = $request->input('email');

		$err_flg = Utility::checkNgWordEmail($to_email);

		//メールアドレスに禁止ワードが含まれていたらトップへリダイレクト
		if( !is_null($err_flg) ){
			return redirect(config('const.forget_path'));
		}

		//ログインID・パスワード取得
		$db_data = User::where('mail_address', $to_email)->first();
		if( empty($db_data) ){
			$disp_data = [
				'title'		 => config('const.list_title')['forget'],
				'send_msg'	 => ''
			];
			return view('forget', $disp_data);
		}

		//会員ステータス:無効
		if( $db_data->status == 3 ){
			$disp_data = [
				'title'		 => config('const.list_title')['forget'],
				'send_msg' => ''
			];
			return view('forget', $disp_data);
		}

		//パスワード忘れ用テンプレート変数
		$mail_template = config('const.forget');
		
		//仮登録
		if( $db_data->status == 0 ){
			$mail_template = config('const.forget_regist');

		//本登録済
		}elseif( $db_data->status == 1 ){
			$mail_template = config('const.forget');
			
		//退会
		}elseif( $db_data->status == 2 ){
			$mail_template = config('const.reregist');
		}
		
		//パスワードは符号化されusersテーブルに登録され、復元不可のため新規にパスワードを生成してusersテーブルのpasswordを更新
		$password	 = str_random(config('const.password_length'));
		$update		 = User::where('mail_address', $to_email)
			->update([
				'password'		=> bcrypt($password),
				'password_raw'	=> $password,
			]);

		//パス忘れメールデータを取得
		$db_cnt = Mail_content::where('id', 4)->first();
		
		//変換設定では変換できない文字列の処理
		$body = preg_replace("/\-%login_id\-/", $db_data->login_id, $db_cnt->body);
		$body = preg_replace("/\-%password\-/", $password, $body);
		$body = preg_replace("/\-%token\-/", $db_data->remember_token, $body);
		
		//変換後の文字列を取得
		list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//送信元情報設定
		$options = [
			'client_id'	 => $db_data->id,
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'from'		 => $from_mail,
			'from_name'	 => $from_name,
			'subject'	 => $subject,
			'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.$mail_template,
		];

		//送信データ設定
		$data = [
			'login_id'		=> $db_data->login_id,
			'password'		=> $password,
			'top_url'		=> config('const.base_url'),
			'complet_url'	=> config('const.base_url').config('const.comp_regist_path').'/'.$db_data->remember_token,
			'member_url'	=> config('const.base_url').config('const.member_top_path').'/'.$db_data->remember_token,
			'contents'		=> $body
		];

		//メール送信
		Mail::to($db_data->mail_address)->queue( new SendMail($options, $data) );

		$disp_data = [
			'title'		=> '',
			'send_msg'	=> __('messages.password_forget')
		];

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['forget_send'].",{$to_email}");

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['forget_send']);

		//完了後、元のページへリダイレクト
		return redirect(config('const.forget_path')."?status=".config("const.forget_end_status"));
	}

	/**
	 * よくある質問画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function qa()
	{
		//特定商取引に基づく表記のコンテンツを取得
		$db_data = Content::where('id', 4)->first();
	
		$disp_data = [
			'title'		=> Utility::getConvertData($db_data->title),
			'contents'	=> Utility::getConvertData($db_data->contents),
		];

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['qa']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['qa']);

		//画面表示
		return view('qa', $disp_data);
	}

	/**
	 * お問い合わせ画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function info()
	{
		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['info']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['info']);

		$disp_data = [
			'title'	 => config('const.list_title')['info'],		
		];

		//画面表示
		return view('info', $disp_data);
	}

	/**
	 * お問い合わせ画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sendInfo(Request $request)
	{
		$this->validate($request, [
			'subject'	 => 'required',
			'email'		 => 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'contents'	 => 'required',
		]);

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['info_send']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['info_send']);

		//送信元情報設定
		$options = [
			'html_flg'	 => false,
			'from'		 => $request->email,
			'from_name'	 => $request->email,
			'subject'	 => $request->subject,
			'template'	 => Session::get('operation_select_db').'.'.config('const.info_email'),
		];

		$data['contents'] = $request->contents;

		//メール送信
		Mail::to(config('const.mail_from'))->send( new SendMail($options, $data) );

		return redirect(config('const.info_comp_path'));
	}

	/**
	 * お問い合わせ画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function compInfo()
	{
		$disp_data = [
			'title'	 => config('const.list_title')['info'],		
		];

		//画面表示
		return view('info_comp', $disp_data);
	}

	/**
	 * プライバシーポリシー画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function privacy()
	{
		//プライバシーポリシーのコンテンツを取得
		$db_data = Content::where('id', 2)->first();

		$list_data = [
			'title'		=> '',
			'contents'	=> ''
		];

		if( !empty($db_data) ){
			$list_data = [
				'title'		=> Utility::getConvertData($db_data->title),
				'contents'	=> Utility::getConvertData($db_data->contents),
			];
		}

		$disp_data = [
			'title'		=> '-['.$list_data['title'].']',
			'contents'	=> $list_data['contents'],
		];
		
		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['privacy']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['privacy']);

		//画面表示
		return view('privacy', $disp_data);
	}

	/**
	 * 利用規約画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function rule()
	{
		//利用規約のコンテンツを取得
		$db_data = Content::where('id', 1)->first();

		$disp_data = [
			'title'		=> config('const.rule'),
			'contents'	=> '',
		];

		if( !empty($db_data) ){
			$disp_data = [
				'title'		=> '-['.Utility::getConvertData($db_data->title).']',
				'contents'	=> Utility::getConvertData($db_data->contents),
			];
		}
		
		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['rule']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['rule']);

		//画面表示
		return view('rule', $disp_data);
	}

	/**
	 * 特定商取引画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function outline()
	{
		//特定商取引に基づく表記のコンテンツを取得
		$db_data = Content::where('id', 3)->first();

		$disp_data = [
			'title'		=> config('const.outline'),
			'contents'	=> '',
		];

		if( !empty($db_data) ){
			$disp_data = [
				'title'		=> '-['.Utility::getConvertData($db_data->title).']',
				'contents'	=> Utility::getConvertData($db_data->contents),
			];
		}

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['outline']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['outline']);

		//画面表示
		return view('outline', $disp_data);
	}

	/**
	 * 競馬法に関する特記事項画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function legal()
	{
		//特定商取引に基づく表記のコンテンツを取得
		$db_data = Content::where('id', 17)->first();

		$disp_data = [
			'title'		=> config('const.outline'),
			'contents'	=> '',
		];

		if( !empty($db_data) ){
			$disp_data = [
				'title'		=> '-['.Utility::getConvertData($db_data->title).']',
				'contents'	=> Utility::getConvertData($db_data->contents),
			];
		}

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['legal']);

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['legal']);

		//画面表示
		return view('outline', $disp_data);
	}
}
