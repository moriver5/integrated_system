<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Content;
use App\Model\Convert_table;
use Utility;

class AdminMasterSentenceController extends Controller
{
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 *  出力文言画面表示
	 */
	public function index($id = '')
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Content::get();
		
		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.disp_contents', $disp_data);
	}

	/*
	 *  出力文言設定の更新処理
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'title'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_title_max_length'),
			'sort'		=> 'bail|required|numeric|max:'.config('const.num_digits_max_length'),
			'sentence'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sentence_update'].",{$user['login_id']}");

		//出力文言更新処理
		$update = Content::where('id', $request->input('tab'))
			->update([
				'title'		=> $request->input('title'),
				'contents'	=> $request->input('sentence'),
				'sort'		=> $request->input('sort'),
			]);
					
		return null;
	}
	
	/*
	 *  出力文言画面-変換表画面表示
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
	
	/*
	 *  出力文言設定-プレビュー画面表示
	 */
	public function preview($id)
	{
		//今週のSPレースのコンテンツを取得
		$sp_race_data = '';
		$sp_db_race_data = Content::where('id', 5)->first();
		if( !empty($sp_db_race_data) ){
			$sp_race_data = Utility::getConvertData($sp_db_race_data->contents);
		}

		$disp_data = [
			'contents'	=> $sp_race_data,
			'id'		=> $id,
			'ver'		=> time(),
		];
		
		return view('admin.master.contents_preview', $disp_data);
	}
}
