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

class AdminUserStatisticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-利用統計-年
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

		$prevYear = $toYear - 1;

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_top']."{$toYear},{$user['login_id']}");

		$listData = [];
		$listPrevData = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listData[$i] = [
				'total'			=> 0,
				'pv_total'		=> 0,
				'regist_total'	=> 0,
				'quite_total'	=> 0,
				'order_count'	=> 0,
				'order_amount'	=> 0,
				'buy_count'		=> 0,
				'buy_amount'	=> 0,
			];
			$listPrevData[$i] = [
				'total'			=> 0,
				'pv_total'		=> 0,
				'regist_total'	=> 0,
				'quite_total'	=> 0,
				'order_count'	=> 0,
				'order_amount'	=> 0,
				'buy_count'		=> 0,
				'buy_amount'	=> 0,
			];
		}

		//アクセスログ
		$access_data = DB::connection(Session::get('operation_select_db'))->table('year_result_access_logs')->whereRaw('access_date >= ? and access_date <= ?', [$toYear.'01', $toYear.'12'])->get();

		//PVログ
		$pv_data = DB::connection(Session::get('operation_select_db'))->table('year_pv_logs')->select('access_date', DB::connection(Session::get('operation_select_db'))->raw('sum(total) as total'))->whereRaw('access_date >= ? and access_date <= ?', [$toYear.'01', $toYear.'12'])->groupBy('access_date')->get();

		//登録人数
		$regist_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m') as access_date, count(id) as total from users where "
						. "status = 1 and "
						. "regist_date >= '{$toYear}0101000000' and " 
						. "regist_date <= '{$toYear}1231235959' "
						. "group by access_date"
					);

		//退会人数
		$quite_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m') as access_date, count(id) as total from users where "
						. "status = 2 and "
						. "regist_date >= '{$toYear}0101000000' and " 
						. "regist_date <= '{$toYear}1231235959' "
						. "group by access_date"
					);

		//注文件数・合計金額
		$order_data = DB::connection(Session::get('operation_select_db'))->select("select substring(sort_date, 1,6) as access_date, sum(money) amount from payment_logs where "
						. "substring(sort_date, 1,6) >= {$toYear}01 and " 
						. "substring(sort_date, 1,6) <= {$toYear}12 "
						. "group by access_date, order_id"
					);

		//注文件数・合計金額(購入)
		$buy_data = DB::connection(Session::get('operation_select_db'))->select("select substring(sort_date, 1,6) as access_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "substring(sort_date, 1,6) >= {$toYear}01 and " 
						. "substring(sort_date, 1,6) <= {$toYear}12 "
						. "group by access_date, order_id"
					);

		//去年のデータ
		//アクセスログ
		$access_prev_data = DB::connection(Session::get('operation_select_db'))->table('year_result_access_logs')->whereRaw('access_date >= ? and access_date <= ?', [$prevYear.'01', $prevYear.'12'])->get();

		//PVログ
		$pv_prev_data = DB::connection(Session::get('operation_select_db'))->table('year_pv_logs')->select('access_date', DB::connection(Session::get('operation_select_db'))->raw('sum(total) as total'))->whereRaw('access_date >= ? and access_date <= ?', [$prevYear.'01', $toYear.'12'])->groupBy('access_date')->get();

		//登録人数
		$regist_prev_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m') as access_date, count(id) as total from users where "
						. "status = 1 and "
						. "regist_date >= '{$prevYear}0101000000' and " 
						. "regist_date <= '{$prevYear}1231235959' "
						. "group by access_date"
					);

		//退会人数
		$quite_prev_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m') as access_date, count(id) as total from users where "
						. "status = 2 and "
						. "regist_date >= '{$prevYear}0101000000' and " 
						. "regist_date <= '{$prevYear}1231235959' "
						. "group by access_date"
					);

		//注文件数・合計金額
		$order_prev_data = DB::connection(Session::get('operation_select_db'))->select("select substring(sort_date, 1,6) as access_date, sum(money) amount from payment_logs where "
						. "substring(sort_date, 1,6) >= {$prevYear}01 and " 
						. "substring(sort_date, 1,6) <= {$prevYear}12 "
						. "group by access_date, order_id"
					);

		//注文件数・合計金額(購入)
		$buy_prev_data = DB::connection(Session::get('operation_select_db'))->select("select substring(sort_date, 1,6) as access_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "substring(sort_date, 1,6) >= {$prevYear}01 and " 
						. "substring(sort_date, 1,6) <= {$prevYear}12 "
						. "group by access_date, order_id"
					);

		for($month=1;$month<=12;$month++){
			//注文数
			foreach($order_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$month]['order_count']++;
					$listData[$month]['order_amount'] += $lines->amount;
				}
			}
			//購入数
			foreach($buy_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$month]['buy_count']++;
					$listData[$month]['buy_amount'] += $lines->amount;
				}
			}
			//登録数
			foreach($regist_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$month]['regist_total'] = $lines->total;
				}
			}
			//退会数
			foreach($quite_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$month]['quite_total'] = $lines->total;
				}
			}
			//アクセス数
			foreach($access_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$month]['total'] = $lines->total;
				}
			}
			//PV数
			foreach($pv_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$month]['pv_total'] = $lines->total;
				}
			}

			//去年のデータ
			//注文数
			foreach($order_prev_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listPrevData[$month]['order_count']++;
					$listPrevData[$month]['order_amount'] += $lines->amount;
				}
			}
			//購入数
			foreach($buy_prev_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listPrevData[$month]['buy_count']++;
					$listPrevData[$month]['buy_amount'] += $lines->amount;
				}
			}
			//登録数
			foreach($regist_prev_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listPrevData[$month]['regist_total'] = $lines->total;
				}
			}
			//退会数
			foreach($quite_prev_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listPrevData[$month]['quite_total'] = $lines->total;
				}
			}
			//アクセス数
			foreach($access_prev_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listPrevData[$month]['total'] = $lines->total;
				}
			}
			//PV数
			foreach($pv_prev_data as $lines){
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listPrevData[$month]['pv_total'] = $lines->total;
				}
			}
		}

		$disp_data = [
			'prev_year'		=> $toYear - 1,
			'next_year'		=> $toYear + 1,
			'year'			=> $toYear,
			'db_data'		=> $listData,
			'db_prev_data'	=> $listPrevData,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.statistics.analysis', $disp_data);
	}

	/*
	 * 集計-利用統計-月
	 */
	public function monthAnalysis($year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_month']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		//去年
		$prev_year = $year - 1;

		$listData = [];
		$listPrevData = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'total'			=> 0,
				'pv_total'		=> 0,
				'regist_total'	=> 0,
				'quite_total'	=> 0,
				'order_count'	=> 0,
				'order_amount'	=> 0,
				'buy_count'		=> 0,
				'buy_amount'	=> 0,
			];
			$listPrevData[$i] = [
				'total'			=> 0,
				'pv_total'		=> 0,
				'regist_total'	=> 0,
				'quite_total'	=> 0,
				'order_count'	=> 0,
				'order_amount'	=> 0,
				'buy_count'		=> 0,
				'buy_amount'	=> 0,
			];
		}

		//アクセスログ
		$access_data = DB::connection(Session::get('operation_select_db'))->table('month_result_access_logs')->whereRaw('access_date >= ? and access_date <= ?', [$year.sprintf("%02d", $month).'01', $year.sprintf("%02d", $month).$last_day])->get();

		//PVログ
		$pv_data = DB::connection(Session::get('operation_select_db'))->table('month_pv_logs')->select('access_date', DB::raw('sum(total) as total'))->whereRaw('access_date >= ? and access_date <= ?', [$year.sprintf("%02d", $month).'01', $year.sprintf("%02d", $month).$last_day])->groupBy('access_date')->get();

		//登録人数
		$regist_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m%d') as regist_date, count(id) as total from users where "
						. "status = 1 and "
						. "regist_date >= ".$year.sprintf("%02d", $month).'01000000'." and " 
						. "regist_date <= ".$year.sprintf("%02d", $month).$last_day."235959 "
						. "group by DATE_FORMAT(regist_date, '%Y%m%d')"
					);

		//退会人数
		$quite_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m%d') as regist_date, count(id) as total from users where "
						. "status = 2 and "
						. "regist_date >= ".$year.sprintf("%02d", $month).'01000000'." and " 
						. "regist_date <= ".$year.sprintf("%02d", $month).$last_day."235959 "
						. "group by DATE_FORMAT(regist_date, '%Y%m%d')"
					);

		//注文件数・合計金額
		$order_data = DB::connection(Session::get('operation_select_db'))->select("select sort_date, sum(money) amount from payment_logs where "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);

		//注文件数・合計金額(購入)
		$buy_data = DB::connection(Session::get('operation_select_db'))->select("select sort_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);

		for($day=1;$day<=$last_day;$day++){
			//注文数
			foreach($order_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listData[$day]['order_count']++;
					$listData[$day]['order_amount'] += $lines->amount;
				}
			}
			//購入数
			foreach($buy_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listData[$day]['buy_count']++;
					$listData[$day]['buy_amount'] += $lines->amount;
				}
			}
			//登録数
			foreach($regist_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->regist_date));
				if( $db_day == $day ){
					$listData[$day]['regist_total'] = $lines->total;
				}
			}
			//退会数
			foreach($quite_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->regist_date));
				if( $db_day == $day ){
					$listData[$day]['quite_total'] = $lines->total;
				}
			}
			//アクセス数
			foreach($access_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listData[$day]['total'] = $lines->total;
				}
			}
			//PV数
			foreach($pv_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listData[$day]['pv_total'] = $lines->total;
				}
			}
		}

		//去年のデータ
		//アクセスログ
		$access_prev_data = DB::connection(Session::get('operation_select_db'))->table('month_result_access_logs')->whereRaw('access_date >= ? and access_date <= ?', [$prev_year.sprintf("%02d", $month).'01', $prev_year.sprintf("%02d", $month).$last_day])->get();

		//PVログ
		$pv_prev_data = DB::connection(Session::get('operation_select_db'))->table('month_pv_logs')->select('access_date', DB::connection(Session::get('operation_select_db'))->raw('sum(total) as total'))->whereRaw('access_date >= ? and access_date <= ?', [$prev_year.sprintf("%02d", $month).'01', $prev_year.sprintf("%02d", $month).$last_day])->groupBy('access_date')->get();

		//登録人数
		$regist_prev_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m%d') as regist_date, count(id) as total from users where "
						. "status = 1 and "
						. "regist_date >= ".$year.sprintf("%02d", $month).'01000000'." and " 
						. "regist_date <= ".$year.sprintf("%02d", $month).$last_day."235959 "
						. "group by DATE_FORMAT(regist_date, '%Y%m%d')"
					);

		//退会人数
		$quite_prev_data = DB::connection(Session::get('operation_select_db'))->select(
						"select DATE_FORMAT(regist_date, '%Y%m%d') as regist_date, count(id) as total from users where "
						. "status = 2 and "
						. "regist_date >= ".$year.sprintf("%02d", $month).'01000000'." and " 
						. "regist_date <= ".$year.sprintf("%02d", $month).$last_day."235959 "
						. "group by DATE_FORMAT(regist_date, '%Y%m%d')"
					);

		//注文件数・合計金額
		$order_prev_data = DB::connection(Session::get('operation_select_db'))->select("select sort_date, sum(money) amount from payment_logs where "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);

		//注文件数・合計金額(購入)
		$buy_prev_data = DB::connection(Session::get('operation_select_db'))->select("select sort_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);

		$listDays = [];
		for($day=1;$day<=$last_day;$day++){
			$listDays[] = $day;

			//注文数
			foreach($order_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listPrevData[$day]['order_count']++;
					$listPrevData[$day]['order_amount'] += $lines->amount;
				}
			}
			//購入数
			foreach($buy_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listPrevData[$day]['buy_count']++;
					$listPrevData[$day]['buy_amount'] += $lines->amount;
				}
			}
			//登録数
			foreach($regist_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->regist_date));
				if( $db_day == $day ){
					$listPrevData[$day]['regist_total'] = $lines->total;
				}
			}
			//退会数
			foreach($quite_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->regist_date));
				if( $db_day == $day ){
					$listPrevData[$day]['quite_total'] = $lines->total;
				}
			}
			//アクセス数
			foreach($access_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listPrevData[$day]['total'] = $lines->total;
				}
			}
			//PV数
			foreach($pv_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listPrevData[$day]['pv_total'] = $lines->total;
				}
			}
		}

		$js_days = '['.implode(",", $listDays).']';

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
			'total_day'		=> $last_day,
			'next_year'		=> $next_year,
			'prev_year'		=> $prev_year,
			'next_month'	=> $next_month,
			'prev_month'	=> $prev_month,
			'year'			=> $year,
			'month'			=> $month,
			'list_days'		=> $js_days,
			'db_data'		=> $listData,
			'db_prev_data'	=> $listPrevData,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.statistics.analysis_month', $disp_data);
	}

	/*
	 * 集計-利用統計-日
	 */
	public function dayAnalysis($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_day']."{$month},{$user['login_id']}");

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
				->select('users.last_access_datetime','users.regist_date as user_regist_date','users.mail_address','users.pay_count','users.id as client_id','payment_logs.*')
				->whereIn('payment_logs.status', ['0', '3'])
				->where('payment_logs.sort_date', $year.sprintf("%02d", $month).sprintf("%02d", $day))
				->paginate(config('const.admin_client_list_limit'));

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
			'ver'		=> time(),
		];
		
		return view('admin.analytics.statistics.analysis_day', $disp_data);
	}

	/*
	 * 集計-利用統計-注文状況-月
	 */
	public function monthOrderStatus($year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_day']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$query = Payment_log::query();
		$listData = $query->join('users', 'users.login_id', '=', 'payment_logs.login_id')
				->select('users.last_access_datetime','users.regist_date as user_regist_date','users.mail_address','users.pay_count','users.id as client_id','payment_logs.*')
				->where('payment_logs.sort_date', '>=', $year.sprintf("%02d", $month).'01')
				->where('payment_logs.sort_date', '<=', $year.sprintf("%02d", $month).sprintf("%02d", $last_day))
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
		
		return view('admin.analytics.statistics.analysis_month_status', $disp_data);
	}

	/*
	 * 集計-利用統計-注文状況-日
	 */
	public function dayOrderStatus($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_day']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$query = Payment_log::query();
		$listData = $query->join('users', 'users.login_id', '=', 'payment_logs.login_id')
				->select('users.last_access_datetime','users.regist_date as user_regist_date','users.mail_address','users.pay_count','users.id as client_id','payment_logs.*')
				->where('payment_logs.sort_date', $year.sprintf("%02d", $month).sprintf("%02d", $day))
				->paginate(config('const.admin_client_list_limit'));

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
			'ver'		=> time(),
		];
		
		return view('admin.analytics.statistics.analysis_day_status', $disp_data);
	}

}
