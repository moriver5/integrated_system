<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Banner;
use Utility;

class AdminBannerController extends Controller
{
	//
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 *  バナー設定画面表示
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Banner::paginate(config('const.admin_key_list_limit'));
		if( !empty($db_data) ){
			foreach($db_data as $lines){
				$lines->banner = Utility::escapeJsTag($lines->banner);
			}
		}

		$disp_data = [
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver' => time(),
		];
		
		return view('admin.master.banner.index', $disp_data);
	}
	
	/*
	 *  バナー追加編集画面からの更新処理
	 */
	public function store(Request $request)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');
		
		//表示設定取得
		$listDispFlg = $request->input('disp_flg');

		//削除ID取得
		$listDelId = $request->input('del');

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'banner.*'		=> 'bail|required|surrogate_pair_check|emoji_check',
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//ログ出力
				$this->log_obj->addLog(config('const.admin_display_list')['banner_delete'].",{$user['login_id']}");

				//テーブルからデータ削除
				Banner::where('id', $id)->delete();

			}else{
				//ログ出力
				$this->log_obj->addLog(config('const.admin_display_list')['banner_update'].",{$user['login_id']}");

				$update_value = [
					'banner'	=> $request->input('banner')[$index],
					'disp_flg'	=> 0,
				];
				if( is_array($listDispFlg) && in_array($id, $listDispFlg) ){
					$update_value['disp_flg'] = 1;
				}
				//バナー設定の更新処理
				$update = Banner::where('id', $id)->update($update_value);
			}
		}
						
		return null;
	}
	
	/*
	 *  バナー追加画面
	 */
	public function create()
	{		

		$disp_data = [
			'ver'		=> time(),
		];
		
		return view('admin.master.banner.create', $disp_data);
	}
	
	/*
	 *  バナー追加処理
	 */
	public function createSend(Request $request)
	{		
		$this->validate($request, [
			'banner'	 => 'required'
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['banner_add'].",{$user['login_id']}");

		$db_value = [
			'banner'	 => $request->input('banner'),
			'disp_flg'	 => 0
		];

		if( !empty($request->input('disp_flg')) ){
			$db_value['disp_flg'] = $request->input('disp_flg');
		}
		
		//convert_tablesにキーを追加
		$db_obj = new Banner($db_value);
		
		//DB保存
		$db_obj->save();

		return null;
	}

}
