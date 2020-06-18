<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use DB;
use Utility;
use Carbon\Carbon;
use Session;

class AdminSalesStructureController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-売上構成-年
	 */
	public function index($year = null)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//デフォルトの年度
		if( is_null($year) ){
			$toYear = date('Y');
			
		//リクエストからの年度
		}else{
			$toYear = $year;
		}

		$nowYear = date('Y');
		$average_count = date('n');
		if( $nowYear > $toYear ){
			$average_count = 12;
		}

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sales_struct']."{$toYear},{$user['login_id']}");

		$listData = [];
		$listLaterData = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			//当月～６ヵ月以降ごとの集計
			for($t=0;$t<=5;$t++){
				//全体の集計
				$listData[$i] = [
					'total_amount'		=> 0,	//売上
					'user_count'		=> [],	//ユーザーごとの集計
					'user_total'		=> 0,	//注文者数
					'user_unit_price'	=> 0,	//注文者数単価(売上/注文者数)
					'order_count'		=> 0,	//注文件数
					'order_unit_price'	=> 0,	//購入単価(売上/注文件数)
				];

				//当月～6ヵ月以降ごとの集計
				$listLaterData[$i][$t] = [
					'total_amount'		=> 0,	//売上
					'user_count'		=> [],	//ユーザーごとの集計
					'user_total'		=> 0,	//注文者数
					'user_unit_price'	=> 0,	//注文者数単価(売上/注文者数)
					'order_count'		=> 0,	//注文件数
					'order_unit_price'	=> 0,	//購入単価(売上/注文件数)
				];
			}
		}

		//売上構成データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->select("select "
						. "substring(users.regist_date,1,8) as user_regist_date, "
						. "substring(sort_date, 1,6) as pay_month, "
						. "order_id, "
						. "payment_logs.login_id, "
						. "sum(money) as amount "
						. "from payment_logs inner join users on payment_logs.login_id = users.login_id "
						. "where "
						. "payment_logs.status in('0','3') and "
						. "substring(payment_logs.sort_date, 1,6) >= {$toYear}01 and " 
						. "substring(payment_logs.sort_date, 1,6) <= {$toYear}12 "
						. "group by order_id,payment_logs.login_id,user_regist_date,pay_month"
					);

		//月ごとに集計
		for($month=1;$month<=12;$month++){
			foreach($db_data as $lines){
				//支払い月
				$regist_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->pay_month));

				$pay_year = substr($lines->pay_month,0,4);
				$pay_month = substr($lines->pay_month,4);

				//月末取得
				$last_day = date('t', mktime(0, 0, 0, $pay_month, 1, $pay_year));

				//月末
				$pay_date = "{$pay_year}/{$pay_month}/{$last_day} 00:00";

				if( $regist_month == $month ){
					//支払い月が登録から何か月後か算出
					$user_regist_date	= strtotime(preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $lines->user_regist_date));
					$user_pay_date		= strtotime($pay_date);
					$month1 = date("Y",$user_regist_date)*12 + date("m",$user_regist_date);
					$month2 = date("Y",$user_pay_date)*12 + date("m",$user_pay_date);
					$month_later = $month2 - $month1;

					if( empty($listLaterData[$month][$month_later]) ){
						$listLaterData[$month][$month_later] = [
							'user_total' => 0,
							'total_amount'	=> 0,
							'order_count'	=> 0,
							'user_unit_price' => 0,
							'order_unit_price' => 0,
						];
					}
					if( empty($listData[$month]['total_amount']) ){
						$listData[$month]['total_amount'] = 0;
					}

					//売上
					$listData[$month]['total_amount'] += $lines->amount;
					$listLaterData[$month][$month_later]['total_amount'] += $lines->amount;

					//者数変数の初期化
					if( empty($listData[$month]['user_count'][$lines->login_id]) ){
						$listData[$month]['user_count'][$lines->login_id] = [
							'count'		=> 0,
							'amount'	=> 0
						];
					}

					//支払い月が登録から何か月後ごとの者数変数の初期化
					if( empty($listLaterData[$month][$month_later]['user_count'][$lines->login_id]) ){
						$listLaterData[$month][$month_later]['user_count'][$lines->login_id] = [
							'count'		=> 0,
							'amount'	=> 0
						];
					}

					//ユーザーごとの件数
					$listData[$month]['user_count'][$lines->login_id]['count']++;
					$listLaterData[$month][$month_later]['user_count'][$lines->login_id]['count']++;					

					//ユーザーごとの売上
					$listData[$month]['user_count'][$lines->login_id]['amount'] += $lines->amount;
					$listLaterData[$month][$month_later]['user_count'][$lines->login_id]['amount'] += $lines->amount;

					//者数
					$listData[$month]['user_total'] = count($listData[$month]['user_count']);
					$listLaterData[$month][$month_later]['user_total'] = count($listLaterData[$month][$month_later]['user_count']);

					//者数単価
					$listData[$month]['user_unit_price'] = $listData[$month]['total_amount'] / count($listData[$month]['user_count']);
					$listLaterData[$month][$month_later]['user_unit_price'] = $listLaterData[$month][$month_later]['total_amount'] / count($listLaterData[$month][$month_later]['user_count']);

					//件数
					$listData[$month]['order_count']++;
					$listLaterData[$month][$month_later]['order_count']++;

					//件数単価
					$listData[$month]['order_unit_price'] = $listData[$month]['total_amount'] / $listData[$month]['order_count'];
					$listLaterData[$month][$month_later]['order_unit_price'] = $listLaterData[$month][$month_later]['total_amount'] / $listLaterData[$month][$month_later]['order_count'];
				}
			}
		}

		$disp_data = [
			'average_count'	=> $average_count,
			'prev_year'		=> $toYear - 1,
			'next_year'		=> $toYear + 1,
			'year'			=> $toYear,
			'db_data'		=> $listData,
			'db_later_data'	=> $listLaterData,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.salesstructure.sales', $disp_data);
	}

}
