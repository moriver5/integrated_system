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

class AdminPaymentStructureController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-入金構成-年
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
		$this->log_obj->addLog(config('const.admin_display_list')['payment_struct_top']."{$toYear},{$user['login_id']}");

		$listData = [];
		$listDate = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listData[$i] = [
				'total_amount'		=> 0,	//売上
				'user_total'		=> 0,	//者数
				'user_count'		=> [],	//ユーザーごとの集計変数
				'user_unit_price'	=> 0,	//者数単価
				'order_count'		=> 0,	//件数
				'order_unit_price'	=> 0,	//件数単価
				'pay_count1'		=> 0,	//入金1回
				'pay_count2'		=> 0,	//入金2回
				'pay_count3'		=> 0,	//入金3回
				'pay_count4'		=> 0,	//入金4回
				'pay_count5'		=> 0,	//入金5回
				'pay_count6'		=> 0,	//入金6回
				'pay_count7'		=> 0,	//入金7回
				'pay_count8'		=> 0,	//入金8回
				'pay_count9'		=> 0,	//入金9回
				'pay_count10'		=> 0,	//入金10回
				'amount_count1'		=> 0,	//入金1回の売上
				'amount_count2'		=> 0,	//入金2回の売上
				'amount_count3'		=> 0,	//入金3回の売上
				'amount_count4'		=> 0,	//入金4回の売上
				'amount_count5'		=> 0,	//入金5回の売上
				'amount_count6'		=> 0,	//入金6回の売上
				'amount_count7'		=> 0,	//入金7回の売上
				'amount_count8'		=> 0,	//入金8回の売上
				'amount_count9'		=> 0,	//入金9回の売上
				'amount_count10'	=> 0,	//入金10回の売上
			];
		}

		//入金構成データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->select("select order_id,payment_logs.login_id, substring(sort_date, 1,6) as access_date, sum(money) as amount "
						. "from payment_logs inner join users on payment_logs.login_id = users.login_id "
						. "where "
						. "payment_logs.status in('0','3') and "
						. "substring(sort_date, 1,6) >= {$toYear}01 and " 
						. "substring(sort_date, 1,6) <= {$toYear}12 "
						. "group by order_id,access_date,payment_logs.login_id"
					);

		//月ごとに集計
		for($month=1;$month<=12;$month++){
			foreach($db_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					//者数変数の初期化
					if( empty($listData[$month]['user_count'][$lines->login_id]) ){
						$listData[$month]['user_count'][$lines->login_id] = [
							'count'		=> 0,
							'amount'	=> 0
						];
					}

					//売上
					$listData[$month]['total_amount'] += $lines->amount;

					//ユーザーごとの件数
					$listData[$month]['user_count'][$lines->login_id]['count']++;					
					//ユーザーごとの売上
					$listData[$month]['user_count'][$lines->login_id]['amount'] += $lines->amount;

					//者数
					$listData[$month]['user_total'] = count($listData[$month]['user_count']);
					//者数単価
					$listData[$month]['user_unit_price'] = $listData[$month]['total_amount'] / count($listData[$month]['user_count']);

					//件数
					$listData[$month]['order_count']++;
					//件数単価
					$listData[$month]['order_unit_price'] = $listData[$month]['total_amount'] / $listData[$month]['order_count'];
				}
			}
		}

		//入金回数ごとの集計
		foreach($listData as $month => $lines){
			foreach($lines['user_count'] as $user_id){
				if( $user_id['count'] == 1 ){
					$listData[$month]['pay_count1']++;
					$listData[$month]['amount_count1'] += $user_id['amount'];
				}elseif( $user_id['count'] == 2 ){
					$listData[$month]['pay_count2']++;
					$listData[$month]['amount_count2'] += $user_id['amount'];
				}elseif( $user_id['count'] == 3 ){
					$listData[$month]['pay_count3']++;
					$listData[$month]['amount_count3'] += $user_id['amount'];
				}elseif( $user_id['count'] == 4 ){
					$listData[$month]['pay_count4']++;
					$listData[$month]['amount_count4'] += $user_id['amount'];
				}elseif( $user_id['count'] == 5 ){
					$listData[$month]['pay_count5']++;
					$listData[$month]['amount_count5'] += $user_id['amount'];
				}elseif( $user_id['count'] == 6 ){
					$listData[$month]['pay_count6']++;
					$listData[$month]['amount_count6'] += $user_id['amount'];
				}elseif( $user_id['count'] == 7 ){
					$listData[$month]['pay_count7']++;
					$listData[$month]['amount_count7'] += $user_id['amount'];
				}elseif( $user_id['count'] == 8 ){
					$listData[$month]['pay_count8']++;
					$listData[$month]['amount_count8'] += $user_id['amount'];
				}elseif( $user_id['count'] == 9 ){
					$listData[$month]['pay_count9']++;
					$listData[$month]['amount_count9'] += $user_id['amount'];
				}elseif( $user_id['count'] == 10 ){
					$listData[$month]['pay_count10']++;
					$listData[$month]['amount_count10'] += $user_id['amount'];
				}
			}
		}

		$disp_data = [
			'average_count'	=> $average_count,
			'prev_year'		=> $toYear - 1,
			'next_year'		=> $toYear + 1,
			'year'			=> $toYear,
			'db_data'		=> $listData,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.payment.payment', $disp_data);
	}

	/*
	 * 集計-入金構成-月
	 */
	public function monthAnalysis($year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['payment_struct_month']."{$month},{$user['login_id']}");
		
		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$nowYear = date('Y');
		$nowMonth = date('n');
		$average_count = 0;

		//今月なら今日までを平均を出す分母にする
		if( $nowYear == $year && $nowMonth == $month ){
			$average_count = date('j');

		//先月以前は月末までの日数を分母にする
		}elseif( $nowYear >= $year && $nowMonth > $month ){
			$average_count = $last_day;
		}

		$listData = [];
		$listDate = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'total_amount'		=> 0,	//売上
				'user_total'		=> 0,	//者数
				'user_count'		=> [],	//ユーザーごとの集計変数
				'user_unit_price'	=> 0,	//者数単価
				'order_count'		=> 0,	//件数
				'order_unit_price'	=> 0,	//件数単価
				'pay_count1'		=> 0,	//入金1回
				'pay_count2'		=> 0,	//入金2回
				'pay_count3'		=> 0,	//入金3回
				'pay_count4'		=> 0,	//入金4回
				'pay_count5'		=> 0,	//入金5回
				'pay_count6'		=> 0,	//入金6回
				'pay_count7'		=> 0,	//入金7回
				'pay_count8'		=> 0,	//入金8回
				'pay_count9'		=> 0,	//入金9回
				'pay_count10'		=> 0,	//入金10回
				'amount_count1'		=> 0,	//入金1回の売上
				'amount_count2'		=> 0,	//入金2回の売上
				'amount_count3'		=> 0,	//入金3回の売上
				'amount_count4'		=> 0,	//入金4回の売上
				'amount_count5'		=> 0,	//入金5回の売上
				'amount_count6'		=> 0,	//入金6回の売上
				'amount_count7'		=> 0,	//入金7回の売上
				'amount_count8'		=> 0,	//入金8回の売上
				'amount_count9'		=> 0,	//入金9回の売上
				'amount_count10'	=> 0,	//入金10回の売上
			];
		}

		//入金構成データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->select("select order_id,payment_logs.login_id, sort_date as access_date, sum(money) as amount "
						. "from payment_logs inner join users on payment_logs.login_id = users.login_id "
						. "where "
						. "payment_logs.status in('0','3') and "
						. "sort_date >= {$year}".sprintf("%02d", $month)."01 and " 
						. "sort_date <= {$year}".sprintf("%02d", $month).$last_day." "
						. "group by order_id,access_date,payment_logs.login_id"
					);

		for($day=1;$day<=$last_day;$day++){
			//購入数
			foreach($db_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					//者数変数の初期化
					if( empty($listData[$day]['user_count'][$lines->login_id]) ){
						$listData[$day]['user_count'][$lines->login_id] = [
							'count'		=> 0,
							'amount'	=> 0
						];
					}

					//売上
					$listData[$day]['total_amount'] += $lines->amount;

					//ユーザーごとの件数
					$listData[$day]['user_count'][$lines->login_id]['count']++;					
					//ユーザーごとの売上
					$listData[$day]['user_count'][$lines->login_id]['amount'] += $lines->amount;

					//者数
					$listData[$day]['user_total'] = count($listData[$day]['user_count']);
					//者数単価
					$listData[$day]['user_unit_price'] = $listData[$day]['total_amount'] / count($listData[$day]['user_count']);

					//件数
					$listData[$day]['order_count']++;
					//件数単価
					$listData[$day]['order_unit_price'] = $listData[$day]['total_amount'] / $listData[$day]['order_count'];
				}
			}
		}

		//入金回数ごとの集計
		foreach($listData as $day => $lines){
			foreach($lines['user_count'] as $user_id){
				if( $user_id['count'] == 1 ){
					$listData[$day]['pay_count1']++;
					$listData[$day]['amount_count1'] += $user_id['amount'];
				}elseif( $user_id['count'] == 2 ){
					$listData[$day]['pay_count2']++;
					$listData[$day]['amount_count2'] += $user_id['amount'];
				}elseif( $user_id['count'] == 3 ){
					$listData[$day]['pay_count3']++;
					$listData[$day]['amount_count3'] += $user_id['amount'];
				}elseif( $user_id['count'] == 4 ){
					$listData[$day]['pay_count4']++;
					$listData[$day]['amount_count4'] += $user_id['amount'];
				}elseif( $user_id['count'] == 5 ){
					$listData[$day]['pay_count5']++;
					$listData[$day]['amount_count5'] += $user_id['amount'];
				}elseif( $user_id['count'] == 6 ){
					$listData[$day]['pay_count6']++;
					$listData[$day]['amount_count6'] += $user_id['amount'];
				}elseif( $user_id['count'] == 7 ){
					$listData[$day]['pay_count7']++;
					$listData[$day]['amount_count7'] += $user_id['amount'];
				}elseif( $user_id['count'] == 8 ){
					$listData[$day]['pay_count8']++;
					$listData[$day]['amount_count8'] += $user_id['amount'];
				}elseif( $user_id['count'] == 9 ){
					$listData[$day]['pay_count9']++;
					$listData[$day]['amount_count9'] += $user_id['amount'];
				}elseif( $user_id['count'] == 10 ){
					$listData[$day]['pay_count10']++;
					$listData[$day]['amount_count10'] += $user_id['amount'];
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
			'average_count'	=> $average_count,
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
		
		return view('admin.analytics.payment.payment_month', $disp_data);
	}

	/*
	 * 集計-入金構成-日
	 */
	public function dayAnalysis($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['payment_struct_day']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$query = Payment_log::query();
		$listData = $query->join('users', 'users.login_id', '=', 'payment_logs.login_id')
				->select('users.regist_date as user_regist_date','users.mail_address as email','users.id as client_id','payment_logs.*')
				->whereIn('payment_logs.status', ['0', '3'])
				->where('payment_logs.sort_date', $year.sprintf("%02d", $month).sprintf("%02d", $day))
				->paginate(config('const.admin_client_list_limit'));
	
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

		//PREV/NEXTリンク先パラメータ設定
		//明日
		$dt = new Carbon($year.'-'.$month.'-'.$day);
		preg_match("/(\d{4})\-(\d{2})\-(\d{2}).+/", $dt->addDay(), $nextDate);

		$next_year	 = $nextDate[1];
		$next_month	 = $nextDate[2];
		$next_day	 = $nextDate[3];

		//昨日
		$dt = new Carbon($year.'-'.$month.'-'.$day);
		preg_match("/(\d{4})\-(\d{2})\-(\d{2}).+/", $dt->subDay(), $prevDate);
		$prev_year	 = $prevDate[1];
		$prev_month	 = $prevDate[2];
		$prev_day	 = $prevDate[3];

		$disp_data = [
			'total_day'	=> $last_day,
			'next_year'	=> $next_year,
			'prev_year'	=> $prev_year,
			'next_month'=> $next_month,
			'prev_month'=> $prev_month,
			'next_day'	=> $next_day,
			'prev_day'	=> $prev_day,
			'year'		=> $year,
			'month'		=> $month,
			'day'		=> $day,
			'db_data'	=> $listData,
			'total_pay'	=> count($listData),
			'ver'		=> time(),
		];
		
		return view('admin.analytics.payment.payment_day', $disp_data);
	}
}
