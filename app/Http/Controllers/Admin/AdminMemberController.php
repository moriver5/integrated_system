<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Operation_dbs;
use Carbon\Carbon;
use App\Mail\SendMail;
use Mail;
use Utility;
use Session;

class AdminMemberController extends Controller
{
	protected $log_obj;
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
//		$this->middleware('auth:admin');
//		$this->middleware('auth.admin.token');
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_history_file_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = [];
		
		//管理者一覧
//		$admin_obj = new Admin;

		$db_data = Admin::orderBy('id')->paginate(config('const.admin_member_list_limit'));
		
		//管理画面へログインリスト一覧
		$db_ua_data = Admin::where('last_login_date','>=',date('Y/m/d 0:0:0'))->orderby('last_login_date', 'desc')->paginate(config('const.admin_member_ua_list_limit'));

		$list_db = Operation_dbs::get();

		$disp_data = [
			'select_db'			=> $user['select_db'],
			'list_db'			=> $list_db,
			'db_data'			=> $db_data,
			'db_ua_data'		=> $db_ua_data,
			'admin_auth_list'	=> config('const.admin_auth_list')
		];

		if( empty($user['select_db']) ){
			return view('admin.member.db_select', $disp_data);			
		}else{
			return view('admin.member.index', $disp_data);
		}
	}

	public function selectSite()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$list_db = Operation_dbs::get();

		$disp_data = [
			'select_db'			=> $user['select_db'],
			'list_db'			=> $list_db,
			'admin_auth_list'	=> config('const.admin_auth_list')
		];

		return view('admin.member.db_select', $disp_data);			

	}

	//DB選択処理
	public function selectSiteSend(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//選択DBを更新
//		$admin_obj = new Admin;
		$update = Admin::where('id', $user['id'])
					->update([
						'select_db' => $request->input('select_db'),
					]);

		//選択DBをセッションに保存
		Session::put('operation_select_db', $request->input('select_db'));

		$db_data = Admin::leftJoin('operation_dbs', 'operation_dbs.db', 'admins.select_db')->where('email', $user['login_id'])->first();

		Session::put('operation_db_name', $db_data->name);

		return redirect(config('const.base_admin_url').config('const.admin_member_top_path'));

	}

	//アカウント新規作成画面
	public function create($page)
	{
		//画面表示配列に管理区分リストを追加
		$disp_data = [
			'page'				=> $page,
			'admin_auth_list'	=> config('const.admin_auth_list'),
			'ver'				=> time(),
		];
		
		return view('admin.member.create', $disp_data);
	}
	
	//アカウント新規作成処理
	public function createSend(Request $request)
	{
		//ログインIDのエラーチェック
		//ログインIDの未入力/長さ/重複チェック
		$this->validate($request, [
			'email'	 => 'bail|required|email|max:'.config('const.email_length').'|check_email_domain|unique:admins,email|check_mx_domain',
		]);
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['account_create'].",{$user['login_id']}");

		//アクセスキー生成
		$remember_token = session_create_id();		
		
		//メールアドレス取得
		$email = $request->input('email');

		$err_flg = Utility::checkNgWordEmail($email, Session::get('operation_select_db'));

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}
		
		//管理区分取得
		$type = $request->input('type');
		
		//adminsテーブルに登録するデータ
		$db_value = [
			'email'				=> $email,
			'remember_token'	=> $remember_token,
			'type'				=> $type
		];
		
		//DBにメールアドレスを登録し仮登録する
		$user = new Admin($db_value);

		//デフォルトのsiteoデータベース接続
		$user->setConnection('mysql');

		//DB保存
		$user->save();

		list($host_ip, $port) = Utility::getSmtpHost('personal');

		//送信元情報設定
		$options = [
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'from'		 => config('const.mail_from'),
			'from_name'	 => config('const.mail_admin_from_name'),
			'subject'	 => config('const.mail_admin_provision_subject'),
			'template'	 => config('const.admin_provision_regist'),
		];
		
		//送信データ設定
		$data = [
			'password_setting_url'	=> config('const.base_admin_url').config('const.admin_password_set_path').'/'.$remember_token,
		];

		//メールアドレス先へメール送信
		Mail::to($email)->queue( new SendMail($options, $data) );
		
		//画面表示配列に管理区分リストを追加
		$disp_data = ['admin_auth_list' => config('const.admin_auth_list')];
		
		return null;
	}
	
	//アカウント編集画面
	public function edit($page, $id)
	{
		//adminsテーブルからログイン情報を取得
		$db_data = Admin::where('id', $id)->first();
		
		//編集データがない場合、トップへリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_member_top_path'));
		}
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//画面表示配列に管理区分リストを追加
		$disp_data = [
			'admin_auth_list'	=> config('const.admin_auth_list'),
			'admin_login_id'	=> $db_data->name,
			'admin_email'		=> $db_data->email,
			'admin_auth_type'	=> $db_data->type,
			'admin_type'		=> $id,
			'page'				=> $page,
			'operate_type'		=> $user['auth_type'],
		];
		
		return view('admin.member.edit', $disp_data);
	}
	
	//アカウント編集処理
	public function store(Request $request)
	{
		//編集しているadminsテーブルのidを取得
		$edit_id = $request->input('id');
		
		$this->validate($request, [
			'email'	 => 'bail|required|email|max:'.config('const.email_length').'|unique:admins,email,'.$edit_id.',id|check_mx_domain'
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//アカウント編集
		if( empty($request->input('del')) ){
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['account_edit'].",{$user['login_id']}");

			$update_value = [
				'email'	=> $request->input('email'),
			];

			$update_value['type'] = $request->input('type');

			$update = Admin::where('id', $edit_id)
				->update($update_value);

			return null;
		
		//アカウント削除
		}else{
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['account_delete'].",{$user['login_id']}");

			$delete = Admin::where('id', $edit_id)->delete();		

			return null;
		}
	}
	
}
