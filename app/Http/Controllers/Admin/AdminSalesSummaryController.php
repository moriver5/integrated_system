<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Payment_log;
use DB;
use Utility;
use Carbon\Carbon;
use Session;

class AdminSalesSummaryController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-売上集計-年
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

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sales_summary_top']."{$toYear},{$user['login_id']}");

		$listData = [];
		$listDate = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listData[$i] = [
				'total'			=> 0,
				'bank_count'	=> 0,
				'bank_amount'	=> 0,
				'credit_count'	=> 0,
				'credit_amount'	=> 0,
				'netbank_count'	=> 0,
				'netbank_amount'=> 0,
				'hand_count'	=> 0,
				'hand_amount'	=> 0,
				'total_count'	=> 0,
				'total_amount'	=> 0,
			];
		}

		//注文件数・合計金額(購入)
		$buy_data = DB::connection(Session::get('operation_select_db'))->select("select pay_type, substring(sort_date, 1,6) as access_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "substring(sort_date, 1,6) >= {$toYear}01 and " 
						. "substring(sort_date, 1,6) <= {$toYear}12 "
						. "group by access_date, pay_type"
					);

		for($month=1;$month<=12;$month++){
			//購入数
			foreach($buy_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					//管理手動
					if( $lines->pay_type == 0 ){
						$listData[$month]['hand_count']++;
						$listData[$month]['hand_amount'] += $lines->amount;						

					//銀行振込
					}elseif( $lines->pay_type == 1 ){
						$listData[$month]['bank_count']++;
						$listData[$month]['bank_amount'] += $lines->amount;						

					//クレジット
					}elseif( $lines->pay_type == 2 ){
						$listData[$month]['credit_count']++;
						$listData[$month]['credit_amount'] += $lines->amount;						

					//ネットバンク
					}elseif( $lines->pay_type == 3 ){
						$listData[$month]['netbank_count']++;
						$listData[$month]['netbank_amount'] += $lines->amount;						
					}

					//売上件数・売上金額
					$listData[$month]['total_count']++;
					$listData[$month]['total_amount'] += $lines->amount;	
				}
			}
		}

		$disp_data = [
			'prev_year'	=> $toYear - 1,
			'next_year'	=> $toYear + 1,
			'year'		=> $toYear,
			'db_data'	=> $listData,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.sales.sales', $disp_data);
	}

	/*
	 * 集計-売上集計-月
	 */
	public function monthAnalysis($year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sales_summary_month']."{$month},{$user['login_id']}");
		
		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$listData = [];
		$listDate = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'total'			=> 0,
				'bank_count'	=> 0,
				'bank_amount'	=> 0,
				'credit_count'	=> 0,
				'credit_amount'	=> 0,
				'netbank_count'	=> 0,
				'netbank_amount'=> 0,
				'hand_count'	=> 0,
				'hand_amount'	=> 0,
				'total_count'	=> 0,
				'total_amount'	=> 0,
			];
		}

		//注文件数・合計金額(購入)
		$buy_data = DB::connection(Session::get('operation_select_db'))->select("select pay_type, sort_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, pay_type"
					);

		for($day=1;$day<=$last_day;$day++){
			//購入数
			foreach($buy_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					//管理手動
					if( $lines->pay_type == 0 ){
						$listData[$day]['hand_count']++;
						$listData[$day]['hand_amount'] += $lines->amount;						

					//銀行振込
					}elseif( $lines->pay_type == 1 ){
						$listData[$day]['bank_count']++;
						$listData[$day]['bank_amount'] += $lines->amount;						

					//クレジット
					}elseif( $lines->pay_type == 2 ){
						$listData[$day]['credit_count']++;
						$listData[$day]['credit_amount'] += $lines->amount;						

					//ネットバンク
					}elseif( $lines->pay_type == 3 ){
						$listData[$day]['netbank_count']++;
						$listData[$day]['netbank_amount'] += $lines->amount;						
					}

					//売上件数・売上金額
					$listData[$day]['total_count']++;
					$listData[$day]['total_amount'] += $lines->amount;
				}
			}
		}
		
		//PREV/NEXTリンク先パラメータ設定
		$next_year = $year;
		$prev_year = $year;
		$next_month = $month + 1;	
		$prev_month = $month - 1;	
		
		//当月が12月のときのパラメータ設定
		if( $month == 12 ){
			$next_year = $year + 1;
			$next_month = 1;

		//当月が1月のときのパラメータ設定
		}elseif( $month == 1 ){
			$prev_year = $year -1;
			$prev_month = 12;	
		}

		$disp_data = [
			'total_day'	=> $last_day,
			'next_year'	=> $next_year,
			'prev_year'	=> $prev_year,
			'next_month'=> $next_month,
			'prev_month'=> $prev_month,
			'year'		=> $year,
			'month'		=> $month,
			'db_data'	=> $listData,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.sales.sales_month', $disp_data);
	}
}
