<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Year_result_access_log;
use App\Model\Month_result_access_log;
use App\Model\Day_result_access_log;
use App\Model\Melmaga_history_log;
use App\Model\Payment_log;
use Utility;
use DB;
use Session;

class AdminRepeatAnalyticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-リピート解析-年
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top'].",{$user['login_id']}");
		
		//データ取得
		$pay_all_data = DB::connection(Session::get('operation_select_db'))->table("payment_logs")->select("login_id", DB::connection(Session::get('operation_select_db'))->raw("count(login_id) as count"), DB::connection(Session::get('operation_select_db'))->raw("sum(money) as amount"))->whereIn("status", [0, 3])->groupBy("login_id")->orderBy("count", "desc")->paginate(config('const.admin_client_list_limit'));

		$listPay = [];
		if( count($pay_all_data) > 0 ){
			foreach($pay_all_data as $lines){
				if( empty($listPay[$lines->login_id]) ){
					$user_data = DB::connection(Session::get('operation_select_db'))->select("select users.status as status, users.point, users.last_access_datetime, users.id as id, payment_logs.login_id as login_id, min(payment_logs.regist_date) as first_regist_date, max(payment_logs.regist_date) as end_regist_date, min(order_id) as first_order_id, max(order_id) as end_order_id from payment_logs inner join users on payment_logs.login_id = users.login_id where payment_logs.login_id = '{$lines->login_id}' and payment_logs.status in(0, 3) group by users.id");
					if( count($user_data) > 0 ){
						$listPay[$lines->login_id] = [
							'client_id'		 => $user_data[0]->id,
							'point'			 => $user_data[0]->point,
							'status'		 => config("const.disp_regist_status")[$user_data[0]->status],
							'last_access'	 => preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1/$2/$3 $4:$5", $user_data[0]->last_access_datetime),
							'pay_num'		 => $lines->count,
							'amount'		 => $lines->amount,
							'first_order_id' => $user_data[0]->first_order_id,
							'end_order_id'	 => $user_data[0]->end_order_id,
							'first_date'	 => preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1/$2/$3 $4:$5", $user_data[0]->first_regist_date),
							'end_date'		 => preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1/$2/$3 $4:$5", $user_data[0]->end_regist_date)
						];
					}
				}
			}
		}

		$disp_data = [
			'total'			=> $pay_all_data->total(),
			'currentPage'	=> $pay_all_data->currentPage(),
			'lastPage'		=> $pay_all_data->lastPage(),
			'links'			=> $pay_all_data->links(),
			'db_data'		=> $listPay,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.repeat.analysis', $disp_data);
	}

}
