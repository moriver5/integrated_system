<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Payment_log;
use App\Model\Group;
use DB;
use Utility;
use Carbon\Carbon;
use Session;

class AdminEventAnalyticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-イベント効果-年
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
		$listTotal = [
			'free_open_total' => 0,
			'free_view_total' => 0,
			'pt_open_total' => 0,
			'pt_view_total' => 0,
			'cap_open_total' => 0,
			'cap_view_total' => 0,
			'open_total' => 0,
			'view_total' => 0,
		];

		$forecasts_data = DB::connection(Session::get('operation_select_db'))->select("select category,count(id) as open_count,EXTRACT(YEAR_MONTH FROM open_sdate) as open_month from forecasts where EXTRACT(YEAR FROM open_sdate) = '{$toYear}' group by open_month,category");

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listData[$i] = [
				'open_total'		=> 0,
				'view_total'		=> 0,
				'free_open_count'	=> 0,
				'free_view_count'	=> 0,
				'pt_open_count'		=> 0,
				'pt_view_count'		=> 0,
				'camp_open_count'	=> 0,
				'camp_view_count'	=> 0,
			];

			foreach($forecasts_data as $lines){
				$open_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->open_month));
				if(  $open_month == $i ){
					if( $lines->category == 1 ){
						$listData[$i]['free_open_count'] = $lines->open_count;
						$listTotal['free_open_total'] += $lines->open_count;
					}elseif( $lines->category == 2 ){
						$listData[$i]['pt_open_count'] = $lines->open_count;
						$listTotal['pt_open_total'] += $lines->open_count;
					}
					$listData[$i]['open_total'] += $lines->open_count;
					$listTotal['open_total'] += $lines->open_count;
				}
			}
		}

		//
		$view_data = DB::connection(Session::get('operation_select_db'))->select("select category,count(client_id) as view_count,EXTRACT(YEAR_MONTH FROM created_at) as  view_month from visitor_logs where EXTRACT(YEAR FROM created_at) = '{$toYear}' group by category,view_month");

		for($month=1;$month<=12;$month++){
			//購入数
			foreach($view_data as $lines){
				$view_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->view_month));
				if( $view_month == $month ){
					//無料
					if( $lines->category == 1 ){
						$listData[$month]['free_view_count'] = $lines->view_count;						
						$listTotal['free_view_total'] += $lines->view_count;

					//ポイント
					}elseif( $lines->category == 2 ){
						$listData[$month]['pt_view_count'] = $lines->view_count;
						$listTotal['pt_view_total'] += $lines->view_count;
					}

					//合計
					$listData[$month]['view_total'] += $lines->view_count;	
					$listTotal['view_total'] += $lines->view_count;
				}
			}
		}

		$disp_data = [
			'prev_year'	=> $toYear - 1,
			'next_year'	=> $toYear + 1,
			'year'		=> $toYear,
			'db_data'	=> $listData,
			'total_data'=> $listTotal,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.event.event', $disp_data);
	}

	/*
	 * 集計-イベント効果-月
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
		$listTotal = [
			'free_open_total' => 0,
			'free_view_total' => 0,
			'pt_open_total' => 0,
			'pt_view_total' => 0,
			'cap_open_total' => 0,
			'cap_view_total' => 0,
			'open_total' => 0,
			'view_total' => 0,
		];

		$forecasts_data = DB::connection(Session::get('operation_select_db'))->select("select category,count(id) as open_count,EXTRACT(DAY FROM open_sdate) as open_day from forecasts where open_sdate >= '{$year}-".sprintf("%02d", $month)."-01' and open_sdate <= '{$year}-".sprintf("%02d", $month)."-{$last_day}' group by open_day,category");

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'open_total'		=> 0,
				'view_total'		=> 0,
				'free_open_count'	=> 0,
				'free_view_count'	=> 0,
				'pt_open_count'		=> 0,
				'pt_view_count'		=> 0,
				'camp_open_count'	=> 0,
				'camp_view_count'	=> 0,
			];

			foreach($forecasts_data as $lines){
				$open_day = sprintf("%d", preg_replace("/(\d{2})/", "$1", $lines->open_day));
				if(  $open_day == $i ){
					if( $lines->category == 1 ){
						$listData[$i]['free_open_count'] = $lines->open_count;
						$listTotal['free_open_total'] += $lines->open_count;
					}elseif( $lines->category == 2 ){
						$listData[$i]['pt_open_count'] = $lines->open_count;
						$listTotal['pt_open_total'] += $lines->open_count;
					}
					$listData[$i]['open_total'] += $lines->open_count;
					$listTotal['open_total'] += $lines->open_count;
				}
			}
		}

		//
		$view_data = DB::connection(Session::get('operation_select_db'))->select("select category,count(client_id) as view_count,EXTRACT(DAY FROM created_at) as view_day from visitor_logs where created_at >= '{$year}-".sprintf("%02d", $month)."-01' and created_at <= '{$year}-".sprintf("%02d", $month)."-{$last_day}' group by category,view_day");

		for($day=1;$day<=$last_day;$day++){
			//
			foreach($view_data as $lines){
				$view_day = sprintf("%d", preg_replace("/(\d{2})/", "$1", $lines->view_day));
				if( $view_day == $day ){
					//無料
					if( $lines->category == 1 ){
						$listData[$day]['free_view_count'] = $lines->view_count;						
						$listTotal['free_view_total'] += $lines->view_count;

					//ポイント
					}elseif( $lines->category == 2 ){
						$listData[$day]['pt_view_count'] = $lines->view_count;
						$listTotal['pt_view_total'] += $lines->view_count;
					}

					//合計
					$listData[$day]['view_total'] += $lines->view_count;	
					$listTotal['view_total'] += $lines->view_count;
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
			'total_data'=> $listTotal,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.event.event_month', $disp_data);
	}

	/*
	 * 集計-イベント効果-日
	 */
	public function dayAnalysis($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sales_summary_month']."{$month},{$user['login_id']}");
		
		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$listData = [];
		$listDate = [];

		$view_data = DB::connection(Session::get('operation_select_db'))->select("select forecast_id,count(client_id) as view_count,EXTRACT(DAY FROM created_at) as view_day from visitor_logs where visitor_logs.created_at >= '{$year}-".sprintf("%02d", $month)."-{$day} 00:00:00' and visitor_logs.created_at <= '{$year}-".sprintf("%02d", $month)."-{$day} 23:59:59' group by forecast_id,view_day");

		$detail_data = DB::connection(Session::get('operation_select_db'))->table("visitor_logs")
						->join('forecasts', 'forecasts.id', '=', 'visitor_logs.forecast_id')
						->select("visitor_logs.forecast_id", "visitor_logs.category", "forecasts.title", "forecasts.groups", "forecasts.open_sdate", "forecasts.open_edate")
						->where("visitor_logs.created_at", ">=", "{$year}-".sprintf("%02d", $month)."-{$day} 00:00:00")
						->where("visitor_logs.created_at", "<=", "{$year}-".sprintf("%02d", $month)."-{$day} 23:59:59")
						->paginate(config('const.admin_client_list_limit'));

		foreach($detail_data as $detail_lines){
			foreach($view_data as $view_lines){
				if( $detail_lines->forecast_id == $view_lines->forecast_id ){
					$listData[$detail_lines->forecast_id] = [
						'category'	 => config('const.forecast_disp_category')[$detail_lines->category],
						'title'		 => $detail_lines->title,
						'groups'	 => $detail_lines->groups,
						'access'	 => $view_lines->view_count,
						'sdate'		 => $detail_lines->open_sdate,
						'edate'		 => $detail_lines->open_edate,
					];
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

		//全件数取得
		$total = $detail_data->total();

		$disp_data = [
			'currentPage'	=> $detail_data->currentPage(),
			'lastPage'		=> $detail_data->lastPage(),
			'links'			=> $detail_data->links(),
			'total'			=> $total,
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
		
		return view('admin.analytics.event.event_day', $disp_data);
	}

	/*
	 * 集計-イベント効果-顧客情報
	 */
	public function dayAnalysisDetail($year, $mon, $day, $forecast_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['sales_summary_month']."{$month},{$user['login_id']}");

		$detail_data = DB::connection(Session::get('operation_select_db'))->table("visitor_logs")->join('users', 'users.id', '=', 'visitor_logs.client_id')->select("users.id", "users.ad_cd", "users.mail_address", "users.group_id", "users.status", "users.point", "users.regist_date", "users.last_access_datetime", "users.pay_count")->where("visitor_logs.forecast_id",$forecast_id)->paginate(config('const.admin_client_list_limit'));

		//全件数取得
		$total = $detail_data->total();

		$disp_data = [
			'forecast_id'	=> $forecast_id,
			'total'			=> $total,
			'currentPage'	=> $detail_data->currentPage(),
			'lastPage'		=> $detail_data->lastPage(),
			'links'			=> $detail_data->links(),
			'db_data'		=> $detail_data,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.event.event_day_detail', $disp_data);
	}
}
