<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Confirm_email;
use Utility;

class AdminMasterConfirmEmailSettingController extends Controller
{
    //
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 *  確認アドレス設定画面表示
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Confirm_email::paginate(config('const.admin_key_list_limit'));
		
		$disp_data = [
			'db_data' => $db_data,
			'ver' => time(),
		];
		
		return view('admin.master.confirm_email_setting', $disp_data);
	}

	/*
	 *  確認アドレス追加画面表示
	 */
	public function create()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$disp_data = [
			'ver' => time(),
		];
		
		return view('admin.master.confirm_email_create', $disp_data);
	}

	/*
	 *  確認アドレス追加処理
	 */
	public function createSend(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$this->validate($request, [
			'user_name'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.from_name_length'),
			'email'		 => 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
		]);

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['confirm_add'].",{$user['login_id']}");

		$db_value = [
			'name'	 => $request->input('user_name'),
			'email'	 => $request->input('email')
		];
		
		//confirm_emailsテーブルにグループ名を追加
		$db_obj = new Confirm_email($db_value);

		//DB保存
		$db_obj->save();
		
		return null;
	}

	/*
	 *  確認アドレス削除処理
	 */
	public function updateSend(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'user_name.*'	=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.from_name_length'),
				'email.*'		=> 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain'
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Confirm_email::where('id', $id)->delete();

			}else{
				//確認アドレス設定画面の更新処理
				$update = Confirm_email::where('id', $id)
					->update([
						'name'		=> $request->input('user_name')[$index],
						'email'		=> $request->input('email')[$index],
					]);
			}
		}

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['confirm_delete'].",{$user['login_id']}");

		return null;
	}



}
