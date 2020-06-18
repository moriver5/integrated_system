<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Settlement_type;
use Utility;

class AdminMasterSettlementAgencyController extends Controller
{
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 *  
	 */
	public function index()
	{
		//ログイン管理者情報取得
//		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Settlement_type::get();
		
		$disp_data = [
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.settlement_agency_setting', $disp_data);
	}

	public function sendAgencyUpdate(Request $request)
	{
		$listId = $request->input('id');
		$listName = $request->input('user_name');

		//すべてのデータのactiveを0で更新
		Settlement_type::query()->update([
			'active' => 0
		]);

		//
		foreach($listId as $index => $id){
			$active = 0;
			if( $id == $request->input('active_id') ){
				$active = 1;
			}
			$update = Settlement_type::where('id', $id)->update([
				'name'	 => $listName[$index],
				'active' => $active
			]);
		}

		return null;
	}
}
