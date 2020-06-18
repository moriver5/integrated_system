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

class AdminNewPaymentController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-新規入金-年
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
		$this->log_obj->addLog(config('const.admin_display_list')['newpayment_top']."{$toYear},{$user['login_id']}");

		$listData = [];
		$listDate = [];
		$listRegistTotal = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listData[$i] = [
				'total_regist'		=> 0,	//登録者数
				'total_first_pay'	=> 0,	//初回入金者
				'payment_rate'		=> 0,	//入金率
				'elapsed_day1'		=> 0,	//経過日数1-7日目
				'elapsed_day8'		=> 0,	//経過日数8-14日目
				'elapsed_day15'		=> 0,	//経過日数15-30日目
				'elapsed_day31'		=> 0,	//経過日数31-60日目
				'elapsed_day61'		=> 0,	//経過日数61日目以降
			];
			$listRegistTotal[$i] = 0;
		}

		//入金構成データ取得
		$regist_total = DB::connection(Session::get('operation_select_db'))->select("select substring(regist_date, 1,6) as regist_date, count(regist_date) as regist_total "
						. "from users "
						. "where "
						. "substring(regist_date, 1,6) >= {$toYear}01 and " 
						. "substring(regist_date, 1,6) <= {$toYear}12 "
						. "group by substring(regist_date, 1,6)"
					);

		foreach($regist_total as $lines){
			$month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->regist_date));
			$listRegistTotal[$month] = $lines->regist_total;
		}

		//入金構成データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->select("select substring(users.regist_date,1,8) as user_regist_date, payment_logs.login_id, substring(sort_date, 1,6) as first_pay_month,sort_date as first_pay_date "
						. "from payment_logs inner join users on payment_logs.login_id = users.login_id "
						. "where "
						. "payment_logs.pay_count = 1 and "
						. "payment_logs.status in('0','3') and "
						. "substring(users.regist_date, 1,6) >= {$toYear}01 and " 
						. "substring(users.regist_date, 1,6) <= {$toYear}12 "
						. "group by order_id,users.regist_date,payment_logs.login_id,sort_date"
					);

		//月ごとに集計
		for($month=1;$month<=12;$month++){
			//登録者数
			$listData[$month]['total_regist'] = $listRegistTotal[$month];
			foreach($db_data as $lines){
				$regist_month = sprintf("%d", preg_replace("/\d{4}(\d{2})\d{2}/", "$1", $lines->user_regist_date));
				//登録月に初回データがあるとき
				if( $regist_month == $month ){
					//初回入金者
					$listData[$month]['total_first_pay']++;

					//初回入金率
					if( $listData[$month]['total_regist'] > 0 ){
						$listData[$month]['payment_rate'] = ($listData[$month]['total_first_pay'] / $listData[$month]['total_regist']) * 100;
					}

					//経過日数の計算
					$regist_date = strtotime(preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1-$2-$3", $lines->user_regist_date));
					$first_pay_month = strtotime(preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1-$2-$3", $lines->first_pay_month));
					$elapsed_day = floor(($first_pay_month - $regist_date) / (60 * 60 * 24));

					//初日～7日目
					if( $elapsed_day >= 1 && $elapsed_day <= 7 ){
						$listData[$month]['elapsed_day1']++;

					//8日～14日目
					}elseif( $elapsed_day >= 8 && $elapsed_day <= 14 ){
						$listData[$month]['elapsed_day8']++;

					//15日～30日目
					}elseif( $elapsed_day >= 15 && $elapsed_day <= 30 ){
						$listData[$month]['elapsed_day15']++;

					//31日～60日目
					}elseif( $elapsed_day >= 31 && $elapsed_day <= 60 ){
						$listData[$month]['elapsed_day31']++;

					//61日目以降
					}elseif( $elapsed_day >= 61 ){
						$listData[$month]['elapsed_day61']++;
					}
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
		
		return view('admin.analytics.newpayment.newpayment', $disp_data);
	}

	/*
	 * 集計-新規入金-月
	 */
	public function monthAnalysis($year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['newpayment_month']."{$month},{$user['login_id']}");
		
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
		$listRegistTotal = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'total_regist'		=> 0,	//登録者数
				'total_first_pay'	=> 0,	//初回入金者
				'payment_rate'		=> 0,	//入金率
				'elapsed_day1'		=> 0,	//経過日数1-7日目
				'elapsed_day8'		=> 0,	//経過日数8-14日目
				'elapsed_day15'		=> 0,	//経過日数15-30日目
				'elapsed_day31'		=> 0,	//経過日数31-60日目
				'elapsed_day61'		=> 0,	//経過日数61日目以降
			];
			$listRegistTotal[$i] = 0;
		}

		//入金構成データ取得
		$regist_total = DB::connection(Session::get('operation_select_db'))->select("select substring(regist_date, 1,8) as regist_date, count(regist_date) as regist_total "
						. "from users "
						. "where "
						. "substring(regist_date, 1,8) >= {$year}".sprintf("%02d",$month)."01 and " 
						. "substring(regist_date, 1,8) <= {$year}".sprintf("%02d",$month)."{$last_day} "
						. "group by substring(regist_date, 1,8)"
					);

		foreach($regist_total as $lines){
			$day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->regist_date));
			$listRegistTotal[$day] = $lines->regist_total;
		}

		//入金構成データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->select("select substring(users.regist_date,1,8) as user_regist_date, payment_logs.login_id, substring(sort_date, 1,8) as first_pay_day,sort_date as first_pay_date "
						. "from payment_logs inner join users on payment_logs.login_id = users.login_id "
						. "where "
						. "payment_logs.pay_count = 1 and "
						. "payment_logs.status in('0','3') and "
						. "substring(users.regist_date,1,8) >= {$year}".sprintf("%02d",$month)."01 and " 
						. "substring(users.regist_date,1,8) <= {$year}".sprintf("%02d",$month)."{$last_day} "
						. "group by order_id,users.regist_date,payment_logs.login_id,sort_date"
					);

		//月ごとに集計
		for($day=1;$day<=$last_day;$day++){
			//登録者数
			$listData[$day]['total_regist'] = $listRegistTotal[$day];
			foreach($db_data as $lines){
				$regist_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->user_regist_date));
				//登録月に初回データがあるとき
				if( $regist_day == $day ){
					//初回入金者
					$listData[$day]['total_first_pay']++;

					//初回入金率
					if( $listData[$day]['total_regist'] > 0 ){
						$listData[$day]['payment_rate'] = ($listData[$day]['total_first_pay'] / $listData[$day]['total_regist']) * 100;
					}

					//経過日数の計算
					$regist_date = strtotime(preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1-$2-$3", $lines->user_regist_date));
					$first_pay_day = strtotime(preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1-$2-$3", $lines->first_pay_day));
					$elapsed_day = floor(($first_pay_day - $regist_date) / (60 * 60 * 24));

					//初日～7日目
					if( $elapsed_day >= 1 && $elapsed_day <= 7 ){
						$listData[$day]['elapsed_day1']++;

					//8日～14日目
					}elseif( $elapsed_day >= 8 && $elapsed_day <= 14 ){
						$listData[$day]['elapsed_day8']++;

					//15日～30日目
					}elseif( $elapsed_day >= 15 && $elapsed_day <= 30 ){
						$listData[$day]['elapsed_day15']++;

					//31日～60日目
					}elseif( $elapsed_day >= 31 && $elapsed_day <= 60 ){
						$listData[$day]['elapsed_day31']++;

					//61日目以降
					}elseif( $elapsed_day >= 61 ){
						$listData[$day]['elapsed_day61']++;
					}
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
		
		return view('admin.analytics.newpayment.newpayment_month', $disp_data);
	}

	/*
	 * 集計-新規入金-日
	 */
	public function dayAnalysis($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['newpayment_day']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

/*
		//注文件数・合計金額(購入)
		$buy_data = DB::connection(Session::get('operation_select_db'))->select("select * from payment_logs where "
						. "status in('0','3') and "
						. "sort_date = ".$year.sprintf("%02d", $month).sprintf("%02d", $day)
					);
 */

		$query = Payment_log::query();
		$listData = $query->join('users', 'users.login_id', '=', 'payment_logs.login_id')
				->select('users.regist_date as user_regist_date','users.mail_address as email','users.id as client_id','payment_logs.*')
				->whereIn('payment_logs.status', ['0', '3'])
				->where('payment_logs.pay_count', 1)
				->where(DB::connection(Session::get('operation_select_db'))->raw('substr(users.regist_date,1,8)'), $year.sprintf("%02d", $month).sprintf("%02d", $day))
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
		
		return view('admin.analytics.newpayment.newpayment_day', $disp_data);
	}

}
