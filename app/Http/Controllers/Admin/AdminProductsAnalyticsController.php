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

class AdminProductsAnalyticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-商品解析-トップ
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top'].",{$user['login_id']}");
	
		//データ取得
//		$pay_all_data = DB::connection(Session::get('operation_select_db'))->table("payment_logs")->select("login_id", "product_id", DB::raw("count(login_id) as count"), "money", DB::raw("sum(money) as amount"))->whereIn("status", [0, 3])->groupBy("product_id", "login_id", "money")->orderBy("count", "desc")->paginate(config('const.admin_client_list_limit'));
		$pay_all_data = DB::connection(Session::get('operation_select_db'))->table("payment_logs")->leftJoin("top_products", "top_products.id", "=", "payment_logs.product_id")->select("payment_logs.login_id", "payment_logs.product_id", DB::connection(Session::get('operation_select_db'))->raw("count(payment_logs.login_id) as count"), "payment_logs.money", DB::connection(Session::get('operation_select_db'))->raw("sum(payment_logs.money) as amount"), "top_products.open_flg", "top_products.start_date", "top_products.end_date")->whereIn("payment_logs.status", [0, 3])->groupBy("payment_logs.product_id", "payment_logs.login_id", "payment_logs.money")->orderBy("count", "desc")->paginate(config('const.admin_client_list_limit'));

		$listPay = [];
		foreach($pay_all_data as $lines){
			if( empty($listPay[$lines->product_id]) ){
				$listPay[$lines->product_id] = [
					'buy'		 => 0,
					'open_flg'	 => '--',
					'buy_total'	 => 0,
					'price'		 => 0,
					'amount'	 => 0,
					'sdate'		 => '--',
					'edate'		 => '--',
					'period_flg' => false
				];
			}
			$listPay[$lines->product_id]['costmer'][$lines->login_id] = 1;
			$listPay[$lines->product_id]['price'] = $lines->money;
			$listPay[$lines->product_id]['open_flg'] = $lines->open_flg;
			$listPay[$lines->product_id]['buy']++;
			$listPay[$lines->product_id]['amount'] += $lines->amount;
			$listPay[$lines->product_id]['start_date'] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1/$2/$3 $4:$5", $lines->start_date);
			$listPay[$lines->product_id]['end_date'] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1/$2/$3 $4:$5", $lines->end_date);
			$listPay[$lines->product_id]['period_flg'] = Utility::checkNowDateWithinPeriod($lines->start_date, $lines->end_date);
		}

		$disp_data = [
			'total'			=> $pay_all_data->total(),
			'currentPage'	=> $pay_all_data->currentPage(),
			'lastPage'		=> $pay_all_data->lastPage(),
			'links'			=> $pay_all_data->links(),
			'db_data'		=> $listPay,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.buy.analysis', $disp_data);
	}

	/*
	 * 集計-商品解析-購入者
	 */
	public function viewCustomer($product_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top'].",{$user['login_id']}");

		//購入者データ取得
		$customer_data = DB::connection(Session::get('operation_select_db'))->table("payment_logs")->join('users', 'users.login_id', '=', 'payment_logs.login_id')->select("users.id", "payment_logs.order_id", "payment_logs.login_id", "payment_logs.money")->whereIn("payment_logs.status", [0, 3])->where('payment_logs.product_id', $product_id)->orderBy("payment_logs.sort_date")->paginate(config('const.admin_client_list_limit'));

		$listCustomer = [];
		foreach($customer_data as $lines){
			$listCustomer[] = [
				'id'		=> $lines->id,
				'login_id'	=> $lines->login_id,
				'order_id'	=> $lines->order_id,
				'price'		=> $lines->money
			];
		}
		
		$disp_data = [
			'total'			=> $customer_data->total(),
			'currentPage'	=> $customer_data->currentPage(),
			'lastPage'		=> $customer_data->lastPage(),
			'links'			=> $customer_data->links(),
			'db_data'		=> $listCustomer,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.buy.customer', $disp_data);
	}
}
