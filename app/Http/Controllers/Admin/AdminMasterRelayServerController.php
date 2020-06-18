<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Relay_server;
use Utility;
use DB;
use Carbon\Carbon;
use Session;

class AdminMasterRelayServerController extends Controller
{
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 *  リレーサーバー設定画面表示
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = Relay_server::get();

		$list_data = [
			'melmaga'		 => ['',''],
			'setting'		 => ['',''],
			'personal'		 => ['','']
		];

		if( !empty($db_data) ){
			foreach($db_data as $lines){
				$list_data[$lines->type][0] = $lines->ip;
				$list_data[$lines->type][1] = $lines->port;
			}
		}

		$disp_data = [
			'db_data'	=> $list_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.relayserver.index', $disp_data);
	}

	/*
	 *  リレーサーバーの更新処理
	 */
	public function store(Request $request)
	{
		$listVal = [
			'melmaga_relayserver'	 => 'check_relayserver|max:'.config('const.relay_server_max_length'),
			'setting_relayserver'	 => 'check_relayserver|max:'.config('const.relay_server_max_length'),
			'personal_relayserver'	 => 'check_relayserver|max:'.config('const.relay_server_max_length'),
		];

		if( !empty($request->input('melmaga_port')) ){
			$listVal['melmaga_port'] = 'digital_num_check';
		}

		if( !empty($request->input('setting_port')) ){
			$listVal['setting_port'] = 'digital_num_check';
		}

		if( !empty($request->input('personal_port')) ){
			$listVal['personal_port'] = 'digital_num_check';
		}

		$this->validate($request, $listVal);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['relay_server_update'].",{$user['login_id']}");

		$melmaga_ip		 = trim($request->input('melmaga_relayserver'));
		$melmaga_port	 = trim($request->input('melmaga_port'));
		$setting_ip		 = trim($request->input('setting_relayserver'));
		$setting_port	 = trim($request->input('setting_port'));
		$personal_ip	 = trim($request->input('personal_relayserver'));
		$personal_port	 = trim($request->input('personal_port'));

		$now_date = Carbon::now();

		if( !empty($request->input('melmaga_port')) ){
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into relay_servers("
				. "type, "
				. "ip, "
				. "port, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'melmaga', "
				. "'".$melmaga_ip."', "
				. "".$melmaga_port.", "
				. "'".$now_date."', "
				. "'".$now_date."') "
				. "on duplicate key update "
				. "ip = '{$melmaga_ip}',"
				. "port = {$melmaga_port};");
		}else{
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into relay_servers("
				. "type, "
				. "ip, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'melmaga', "
				. "'".$melmaga_ip."', "
				. "'".$now_date."', "
				. "'".$now_date."') "
				. "on duplicate key update "
				. "ip = '{$melmaga_ip}';");
		}

		if( !empty($request->input('setting_port')) ){
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into relay_servers("
				. "type, "
				. "ip, "
				. "port, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'setting', "
				. "'".$setting_ip."', "
				. "".$setting_port.", "
				. "'".$now_date."', "
				. "'".$now_date."') "
				. "on duplicate key update "
				. "ip = '{$setting_ip}',"
				. "port = {$setting_port};");
		}else{
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into relay_servers("
				. "type, "
				. "ip, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'setting', "
				. "'".$setting_ip."', "
				. "'".$now_date."', "
				. "'".$now_date."') "
				. "on duplicate key update "
				. "ip = '{$setting_ip}';");
		}

		if( !empty($request->input('personal_port')) ){
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into relay_servers("
				. "type, "
				. "ip, "
				. "port, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'personal', "
				. "'".$personal_ip."', "
				. "".$personal_port.", "
				. "'".$now_date."', "
				. "'".$now_date."') "
				. "on duplicate key update "
				. "ip = '{$personal_ip}',"
				. "port = {$personal_port};");
		}else{
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into relay_servers("
				. "type, "
				. "ip, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'personal', "
				. "'".$personal_ip."', "
				. "'".$now_date."', "
				. "'".$now_date."') "
				. "on duplicate key update "
				. "ip = '{$personal_ip}';");
		}

		return null;
	}
}
