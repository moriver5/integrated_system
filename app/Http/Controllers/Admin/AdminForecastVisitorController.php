<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\User;
use App\Model\Forecast;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminForecastVisitorController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 予想管理-閲覧者一覧
	 */
	public function index(Request $request)
	{
		$db_data	 = [];
		$forecast_id ="";

		//予想ID検索が保存されていればデータ取得
		if( !empty(Session::get('visitor_forecast_id')) ){
			$forecast_id = Session::get('visitor_forecast_id');
			$query = User::query();
			$db_data = $query->join('visitor_logs', 'users.id', '=', 'visitor_logs.client_id')->whereIn('forecast_id', explode(",", Session::get('visitor_forecast_id')))->paginate(config('const.admin_client_list_limit'));
		}

		//画面表示用配列
		$disp_data = [
			'list_status'	=> config('const.regist_status'),
			'forecast_id'	=> $forecast_id,
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.forecast.visitor.index', $disp_data);
	}

	/*
	 * 予想管理-閲覧者検索
	 */
	public function searchPost(Request $request)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['forecasts_visitor'].",{$user['login_id']}");

		Session::put('visitor_forecast_id', $request->input('forecast_id'));

		//データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->table('users')
			->join('visitor_logs', 'users.id', '=', 'visitor_logs.client_id')
			->whereIn('forecast_id', explode(",", $request->input('forecast_id')))->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'list_status'	=> config('const.regist_status'),
			'forecast_id'	=> Session::get('visitor_forecast_id'),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.forecast.visitor.index', $disp_data);
	}
}
