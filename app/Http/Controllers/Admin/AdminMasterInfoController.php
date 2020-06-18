<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\User_info;
use DB;
use Utility;
use Session;
use Carbon\Carbon;

class AdminMasterInfoController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	public function index()
	{
		$db_data = User_info::orderBy('order', 'asc')->paginate(config('const.admin_key_list_limit'));

		$disp_data = [
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.userinfo', $disp_data);
	}

	public function create($id = null)
	{
		$disp_data = [
			'userinfo'	=> '',
			'order'		=> 0,
			'disptime'	=> 0,
			'disp_flg'	=> 0,
		];

		if( !is_null($id) ){
			$db_data = User_info::first();
			$disp_data = [
				'userinfo'	=> $db_data->userinfo,
				'order'		=> $db_data->order,
				'dispmsg'	=> $db_data->dispmsg,
				'disptime'	=> $db_data->disptime,
				'disp_flg'	=> $db_data->disp_flg,
			];
		}

		$disp_data = array_merge($disp_data, [
			'ver' => time(),
		]);
		
		return view('admin.master.userinfo_create', $disp_data);
	}

	public function createInfoSend(Request $request)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$userinfo = $request->input('userinfo');

		if( !empty($request->input('order')) ){
			$order = $request->input('order');
		}else{
			$order = 0;
		}

		if( !empty($request->input('disptime')) ){
			$disptime = $request->input('disptime');
		}else{
			$disptime = 0;
		}

		if( !empty($request->input('disp_flg')) ){
			$disp_flg = $request->input('disp_flg');
		}else{
			$disp_flg = 0;
		}

		$user_info = new User_info([
			'userinfo'	 => $userinfo,
			'order'		 => $order,
			'disptime'	 => $disptime,
			'disp_flg'	 => $disp_flg,
		]);

		$user_info->save();
/*
		//登録されていなければinsert、登録されていればupdate
		DB::connection(Session::get('operation_select_db'))->insert("insert ignore into user_infos(userinfo,order,disptime,disp_flg,created_at,updated_at) values("
		. "'{$userinfo}',"
		. "{$order},"
		. "{$disptime},"
		. "{$disp_flg},"
		. "'".Carbon::now()."',"
		. "'".Carbon::now()."')");
*/
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_grant'].",{$user['login_id']}");
		
		return null;
	}

	public function edit($id)
	{
		$disp_data = [
			'userinfo' => '',
			'disptime' => 0,
			'disp_flg' => 0,
		];

		if( !is_null($id) ){
			$db_data = User_info::where('id', $id)->first();
			$disp_data = [
				'id'		=> $id,
				'userinfo'	=> $db_data->userinfo,
				'order'		=> $db_data->order,
				'dispmsg'	=> $db_data->dispmsg,
				'disptime'	=> $db_data->disptime,
				'disp_flg'	=> $db_data->disp_flg,
			];
		}

		$disp_data = array_merge($disp_data, [
			'ver' => time(),
		]);
		
		return view('admin.master.userinfo_edit', $disp_data);
	}

	public function store(Request $request, $id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$userinfo = $request->input('userinfo');

		if( !empty($request->input('order')) ){
			$order = $request->input('order');
		}else{
			$order = 0;
		}

		if( !empty($request->input('disptime')) ){
			$disptime = $request->input('disptime');
		}else{
			$disptime = 0;
		}

		if( !empty($request->input('disp_flg')) ){
			$disp_flg = $request->input('disp_flg');
		}else{
			$disp_flg = 0;
		}

		$update = User_info::where('id', $id)->update([
			'userinfo'	 => $userinfo,
			'order'		 => $order,
			'disptime'	 => $disptime,
			'disp_flg'	 => $disp_flg,
		]);

		return null;
	}

	public function bulkDelete(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['agency_del'].",{$user['login_id']}");	

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'del.*'		=> 'required',
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				User_info::where('id', $id)->delete();

			}
		}

		return null;
	}
}
