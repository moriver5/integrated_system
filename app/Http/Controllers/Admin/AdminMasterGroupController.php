<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\User;
use App\Model\Group;
use Utility;

class AdminMasterGroupController extends Controller
{
	//
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 *  グループ画面表示
	 */
	public function index()
	{
		//ログイン管理者情報取得
//		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Group::paginate(config('const.admin_key_list_limit'));
		
		$disp_data = [
			'db_data' => $db_data,
			'ver' => time(),
		];
		
		return view('admin.group.index', $disp_data);
	}
	
	/*
	 *  グループ画面-グループ追加画面追加
	 */
	public function create()
	{		

		$disp_data = [
			'ver'		=> time(),
		];
		
		return view('admin.group.create', $disp_data);
	}
	
	/*
	 *  グループ画面-グループ追加処理
	 */
	public function createSend(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$this->validate($request, [
			'group'	 => 'required|max:'.config('const.group_name_max_length'),
			'memo'	 => 'max:'.config('const.group_memo_max_length'),
		]);
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['group_add'].",{$user['login_id']}");	

		$db_value = [
			'name'	 => $request->input('group'),
			'memo'	 => $request->input('memo')
		];
		
		//groupテーブルにグループ名を追加
		$db_obj = new Group($db_value);
		
		//DB保存
		$db_obj->save();
		
		$disp_data = [
			'ver' => time(),
		];
		
		return null;
	}
	
	/*
	 *  グループ管理画面の更新処理
	 */
	public function store(Request $request)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');
		
		//更新ログ
		if( empty($listDelId) ){
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['group_update'].",{$user['login_id']}");	

		//削除ログ
		}else{
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['group_delete'].",{$user['login_id']}");	
		}	
			
		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'group.*'		=> 'required',
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Group::where('id', $id)->delete();

			}else{
				//グループ管理画面の更新処理
				$update = Group::where('id', $id)
					->update([
						'name'		=> $request->input('group')[$index],
						'memo'		=> $request->input('memo')[$index],
					]);
			}
		}
						
		return null;
	}
	
	/*
	 * グループ一括移行
	 */
	public function bulkMoveGroup()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$disp_data = [
			'db_group_data'		=> $db_group_data,
			'ver'				=> time(),
		];

		return view('admin.group.move', $disp_data); 
	}
	
	/*
	 * グループ一括移行の更新処理
	 */
	public function bulkMoveGroupSend(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//移行元のグループID取得
		$listGroupId = $request->input('move_group_id');

		$this->validate($request, [
			'move_group_id'	=> 'required',
			'group_id'		=> 'required',
		]);
		
		//動的クエリを生成するため
		$query = User::whereIn('group_id', $listGroupId)->update([
				'group_id'	=> $request->input('group_id')
			]);

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['group_bulk_move'].",{$user['login_id']}");	

		return null;
	}
}
