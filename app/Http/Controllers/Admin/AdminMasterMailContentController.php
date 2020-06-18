<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Convert_table;
use App\Model\Mail_content;
use Utility;

class AdminMasterMailContentController extends Controller
{
	//
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 *  自動メール文設定画面表示
	 */
	public function index($id = '')
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Mail_content::get();
		
		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.mail_contents', $disp_data);
	}
	
	/*
	 *  自動メール文設定-更新処理
	 */
	public function store(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//エラーチェック
		$this->validate($request, [
			'from'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.from_name_length'),
			'from_mail'		=> 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'subject'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'body'			=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['auto_mail_update'].",{$user['login_id']}");

		//自動メール文更新処理
		$update = Mail_content::where('id', $request->input('tab'))
			->update([
				'from'			=> $request->input('from'),
				'from_mail'		=> $request->input('from_mail'),
				'subject'		=> $request->input('subject'),
				'body'			=> $request->input('body'),
			]);
					
		return null;
	}
	
	/*
	 *  自動メール文設定-%変換表画面表示
	 */
	public function convert($id)
	{		
		$db_data = Convert_table::get();

		$disp_data = [
			'db_data'	=> $db_data,
			'id'		=> $id,
			'ver'		=> time(),
		];
		
		return view('admin.master.convert_table', $disp_data);
	}
	
	
}
