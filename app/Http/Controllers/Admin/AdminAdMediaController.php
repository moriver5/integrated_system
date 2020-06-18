<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Group;
use App\Model\User;
use App\Model\Result_ad_log;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminAdMediaController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 媒体集計画面表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['media_top'].",{$user['login_id']}");

		//デフォルトの年度
		if( is_null(Session::get('media_start_date')) ){
			$start_date = date('Ymd');
		}else{
			$start_date = preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_start_date'));			
		}
		if( is_null(Session::get('media_end_date')) ){
			$end_date = date('Ymd');
		}else{
			$end_date = preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_end_date'));			
		}
		
		$db_data = Result_ad_log::query()
			->leftJoin('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
			->where('access_date', '>=', $start_date)
			->where('access_date', '<=', $end_date)
//			->select('result_ad_logs.ad_cd', 'ad_codes.name', 'result_ad_logs.pv as pv', 'result_ad_logs.reg as reg', 'result_ad_logs.temp_reg as temp_reg', 'result_ad_logs.quit as quit', 'result_ad_logs.active as active', 'result_ad_logs.order_num as order_num', 'result_ad_logs.pay as pay', 'result_ad_logs.amount as amount')
			->select('result_ad_logs.ad_cd', 'ad_codes.id', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
			->groupBy('result_ad_logs.ad_cd')
			->paginate(config('const.admin_client_list_limit'));

		//画面表示用配列
		$disp_data = [
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.ad.media.index', $disp_data);
	}

	/*
	 * 検索設定画面表示
	 */
	public function searchSetting()
	{
		//画面表示用配列
		$disp_data = [
			'session'						=> Session::all(),
			'ver'							=> time(),
			'ad_sort_list'					=> config('const.sort_ad_list'),
			'ad_search_item'				=> config('const.ad_media_search_item'),
			'search_like_type'				=> config('const.search_like_type'),
			'ad_search_disp_num'			=> config('const.search_disp_num'),
			'ad_category'					=> config('const.ad_category'),
		];

		return view('admin.ad.media.search_setting', $disp_data);
	}

	/*
	 * 検索結果のページャーのリンクから呼び出される
	 */
	public function search(Request $request)
	{
		//合計
		if( empty(Session::get('media_disp_type')) ){
			$query = Result_ad_log::query()
				->leftJoin('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.ad_cd', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.ad_cd','ad_codes.name');

		//日毎
		}else{
			$query = Result_ad_log::query()
				->leftJoin('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.access_date', 'result_ad_logs.ad_cd', 'ad_codes.id', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.access_date', 'result_ad_logs.ad_cd', 'ad_codes.id', 'ad_codes.name');
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$listData = [];
		$listDate = [];
		$listAd = [];
		$listId = [];

		//合計
		if( empty(Session::get('media_disp_type')) ){
			$listData = $db_data;

		//日毎
		}else{
			$dt1 = new Carbon(Session::get('media_start_date'));
			$dt2 = new Carbon(Session::get('media_end_date'));
			$period = $dt1->diffInDays($dt2);

			for($i=0;$i<=$period;$i++){
				$dt = new Carbon(Session::get('media_start_date'));
				$listDate[] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}:\d{2}:\d{2})/", "$2-$3", $dt->addDay($i));
			}

			foreach($db_data as $lines){
				if( empty($listData[$lines->ad_cd]) ){
					$listAd[$lines->ad_cd] = $lines->name;
					$listId[$lines->ad_cd] = $lines->id;

					$listData[$lines->ad_cd] = [];
					for($i=0;$i<=$period;$i++){
						//$i日後の日付
						$dt = new Carbon(Session::get('media_start_date'));
						$add_day = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}:\d{2}:\d{2})/", "$1$2$3", $dt->addDay($i));
						if( empty($listData[$lines->ad_cd][$add_day]) ){
							$listData[$lines->ad_cd][$add_day] = [];
						}
						$listData[$lines->ad_cd][$add_day] = [
							'id'		=> '',
							'name'		=> '',
							'ad_cd'		=> '',
							'pv'		=> 0,
							'reg'		=> 0,
							'temp_reg'	=> 0,
							'quit'		=> 0,
							'active'	=> 0,
							'order_num'	=> 0,
							'pay'		=> 0,
							'pay_rate'	=> 0,
							'amount'	=> 0	
						];
					}
					$listData[$lines->ad_cd]['total'] = [
						'id'		=> '',
						'name'		=> '',
						'ad_cd'		=> '',
						'pv'		=> 0,
						'reg'		=> 0,
						'temp_reg'	=> 0,
						'quit'		=> 0,
						'active'	=> 0,
						'order_num'	=> 0,
						'pay'		=> 0,
						'pay_rate'	=> 0,
						'amount'	=> 0	
					];
				}
				if( !empty($lines->access_date) ){
					$listData[$lines->ad_cd][$lines->access_date] = [
						'id'		=> $lines->id,
						'name'		=> $lines->name,
						'ad_cd'		=> $lines->ad_cd,
						'pv'		=> $lines->pv,
						'reg'		=> $lines->reg,
						'temp_reg'	=> $lines->temp_reg,
						'quit'		=> $lines->quit,
						'active'	=> $lines->active,
						'order_num'	=> $lines->order_num,
						'pay'		=> $lines->pay,
						'pay_rate'	=> $lines->active != 0 ? ($lines->pay / $lines->active) * 100:0,
						'amount'	=> $lines->amount
					];
					$listData[$lines->ad_cd]['total']['id'] = $lines->id;
					$listData[$lines->ad_cd]['total']['name'] = $lines->name;
					$listData[$lines->ad_cd]['total']['ad_cd'] = $lines->ad_cd;
					$listData[$lines->ad_cd]['total']['pv'] += $lines->pv;
					$listData[$lines->ad_cd]['total']['reg'] += $lines->reg;
					$listData[$lines->ad_cd]['total']['temp_reg'] += $lines->temp_reg;
					$listData[$lines->ad_cd]['total']['quit'] += $lines->quit;
					$listData[$lines->ad_cd]['total']['active'] += $lines->active;
					$listData[$lines->ad_cd]['total']['order_num'] += $lines->order_num;
					$listData[$lines->ad_cd]['total']['pay'] += $lines->pay;
					$listData[$lines->ad_cd]['total']['pay_rate'] += $listData[$lines->ad_cd]['total']['active'] != 0 ? ($listData[$lines->ad_cd]['total']['pay'] / $listData[$lines->ad_cd]['total']['active']) * 100:0;;
					$listData[$lines->ad_cd]['total']['amount'] += $lines->amount;
				}
			}
		}

		$disp_data = [
			'session'			=> Session::all(),
			'list_id'			=> $listId,
			'list_ad'			=> $listAd,
			'list_date'			=> $listDate,
			'db_data'			=> $listData,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		//合計
		if( empty(Session::get('media_disp_type')) ){
			return view('admin.ad.media.index', $disp_data);

		//日毎
		}else{
			return view('admin.ad.media.index_daily', $disp_data);			
		}

	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['media_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//合計
		if( empty(Session::get('media_disp_type')) ){
			$query = Result_ad_log::query()
				->leftJoin('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.ad_cd','ad_codes.name', 'ad_codes.id');

		//日毎
		}else{
			$query = Result_ad_log::query()
				->leftJoin('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.access_date', 'result_ad_logs.ad_cd', 'ad_codes.id', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.access_date', 'result_ad_logs.ad_cd', 'ad_codes.id', 'ad_codes.name');
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$listData = [];
		$listDate = [];
		$listAd = [];
		$listId = [];

		//合計
		if( empty(Session::get('media_disp_type')) ){
			$listData = $db_data;

		//日毎
		}else{
			$dt1 = new Carbon(Session::get('media_start_date'));
			$dt2 = new Carbon(Session::get('media_end_date'));
			$period = $dt1->diffInDays($dt2);

			for($i=0;$i<=$period;$i++){
				$dt = new Carbon(Session::get('media_start_date'));
				$listDate[] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}:\d{2}:\d{2})/", "$2-$3", $dt->addDay($i));
			}

			foreach($db_data as $lines){
				if( empty($listData[$lines->ad_cd]) ){
					$listAd[$lines->ad_cd] = $lines->name;
					$listId[$lines->ad_cd] = $lines->id;

					$listData[$lines->ad_cd] = [];
					for($i=0;$i<=$period;$i++){
						//$i日後の日付
						$dt = new Carbon(Session::get('media_start_date'));
						$add_day = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}:\d{2}:\d{2})/", "$1$2$3", $dt->addDay($i));
						if( empty($listData[$lines->ad_cd][$add_day]) ){
							$listData[$lines->ad_cd][$add_day] = [];
						}
						$listData[$lines->ad_cd][$add_day] = [
							'id'		=> '',
							'name'		=> '',
							'ad_cd'		=> '',
							'pv'		=> 0,
							'reg'		=> 0,
							'temp_reg'	=> 0,
							'quit'		=> 0,
							'active'	=> 0,
							'order_num'	=> 0,
							'pay'		=> 0,
							'pay_rate'	=> 0,
							'amount'	=> 0	
						];
					}
					$listData[$lines->ad_cd]['total'] = [
						'id'		=> '',
						'name'		=> '',
						'ad_cd'		=> '',
						'pv'		=> 0,
						'reg'		=> 0,
						'temp_reg'	=> 0,
						'quit'		=> 0,
						'active'	=> 0,
						'order_num'	=> 0,
						'pay'		=> 0,
						'pay_rate'	=> 0,
						'amount'	=> 0	
					];
				}
				if( !empty($lines->access_date) ){
					$listData[$lines->ad_cd][$lines->access_date] = [
						'id'		=> $lines->id,
						'name'		=> $lines->name,
						'ad_cd'		=> $lines->ad_cd,
						'pv'		=> $lines->pv,
						'reg'		=> $lines->reg,
						'temp_reg'	=> $lines->temp_reg,
						'quit'		=> $lines->quit,
						'active'	=> $lines->active,
						'order_num'	=> $lines->order_num,
						'pay'		=> $lines->pay,
						'pay_rate'	=> $lines->active != 0 ? ($lines->pay / $lines->active) * 100:0,
						'amount'	=> $lines->amount
					];
					$listData[$lines->ad_cd]['total']['id'] = $lines->id;
					$listData[$lines->ad_cd]['total']['name'] = $lines->name;
					$listData[$lines->ad_cd]['total']['ad_cd'] = $lines->ad_cd;
					$listData[$lines->ad_cd]['total']['pv'] += $lines->pv;
					$listData[$lines->ad_cd]['total']['reg'] += $lines->reg;
					$listData[$lines->ad_cd]['total']['temp_reg'] += $lines->temp_reg;
					$listData[$lines->ad_cd]['total']['quit'] += $lines->quit;
					$listData[$lines->ad_cd]['total']['active'] += $lines->active;
					$listData[$lines->ad_cd]['total']['order_num'] += $lines->order_num;
					$listData[$lines->ad_cd]['total']['pay'] += $lines->pay;
					$listData[$lines->ad_cd]['total']['pay_rate'] += $listData[$lines->ad_cd]['total']['active'] != 0 ? ($listData[$lines->ad_cd]['total']['pay'] / $listData[$lines->ad_cd]['total']['active']) * 100:0;
					$listData[$lines->ad_cd]['total']['amount'] += $lines->amount;
				}
			}
		}

		$disp_data = [
			'session'			=> Session::all(),
			'list_id'			=> $listId,
			'list_ad'			=> $listAd,
			'list_date'			=> $listDate,
			'db_data'			=> $listData,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		//合計
		if( empty(Session::get('media_disp_type')) ){
			return view('admin.ad.media.index', $disp_data);

		//日毎
		}else{
			return view('admin.ad.media.index_daily', $disp_data);			
		}
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		//広告コード
		if( !is_null(Session::get('media_search_item_value')) ){
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listItem = explode(",", Session::get('media_search_item_value'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('media_search_item'), $item);
				}
			});
		}

		//期間-開始
		if( !empty(Session::get('media_start_date')) ){
			$query->where('access_date', '>=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_start_date')));
		}else{
			$query->where('access_date', '>=', date('Ymd'));			
		}

		//期間-終了
		if( !empty(Session::get('media_end_date')) ){
			$query->where('access_date', '<=', date('Ymd'));
		}

		//媒体種別
		if( !empty(Session::get('media_category')) ){
			$query->whereIn('category', explode(",", Session::get('media_category')));
		}

		//表示順
		if( !empty(Session::get('media_sort')) ){
			list($column, $sort) = explode(",", config('const.sort_ad_list')[Session::get('media_sort')][0]);
			$query->orderBy($column, $sort);
		}

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->count();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$db_data = $query;

		//通常検索
		}else{
			$db_data = $query->paginate(config('const.admin_client_list_limit'));
//			$db_data = $query->toSql();
		}

		return $db_data;
	}

	/*
	 * SQL文の条件保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索項目
		Session::put('media_search_item', $request->input('search_item'));
		
		//LIKE検索
		Session::put('media_search_like_type', $request->input('search_like_type'));

		//検索の値
		Session::put('media_search_item_value', $request->input('search_item_value'));

		//期間-開始
		Session::put('media_start_date', $request->input('start_date'));

		//期間-終了
		Session::put('media_end_date', $request->input('end_date'));

		//媒体種別
		Session::put('media_category', $request->input('category'));

		//表示-合計/日毎
		Session::put('media_disp_type', $request->input('disp_type'));

		//アクション表示回数
		Session::put('media_action_flg', $request->input('action_flg'));

		//表示順
		Session::put('media_sort', $request->input('sort'));
	}
}
