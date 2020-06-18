<?php

namespace App\Http\Controllers\Agency;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Result_ad_log;
use Utility;
use Session;
use DB;
use Carbon\Carbon;

class AgencyController extends Controller
{
	public function __construct()
	{

	}

	/*
	 * 代理店管理画面のログイン後に表示される画面
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//動的クエリを生成するため
		$query = Result_ad_log::query()
				->join('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.ad_cd', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.ad_cd','ad_codes.name')
				->where('ad_codes.agency_id', $agency['agency_id']);

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//画面表示用配列
		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * 検索ボタンを押下後の検索処理と検索結果を表示
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Result_ad_log::query()
				->join('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.ad_cd', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.ad_cd','ad_codes.name')
				->where('ad_codes.agency_id', $agency['agency_id']);

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * 検索処理後のページャーのリンクを押下たときに呼び出される
	 */
	public function search(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//動的クエリを生成するため
		$query = Result_ad_log::query()
				->join('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd');

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * 検索パラメータをsessionに保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索タイプ
		if( !is_null($request->input('search_type')) ){
			Session::put('ad_search_type', $request->input('search_type'));
		}

		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('ad_search_item', $request->input('search_item'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('ad_search_item');
		}
		
		//LIKE検索
		if( !is_null($request->input('search_like_type')) ){
			Session::put('ad_search_like_type', $request->input('search_like_type'));
		}

		//集計-開始
		if( !empty($request->input('start_date')) ){
			Session::put('ad_start_date', $request->input('start_date'));
		}else{
			Session::put('ad_start_date', date('Y').'/'.date('m'));
		}

		//集計-終了
		if( !empty($request->input('end_date')) ){
			Session::put('ad_end_date', $request->input('end_date'));
		}else{
			Session::put('ad_end_date', date('Y').'/'.date('m'));
		}

	}

	/*
	 * 検索処理のパラメータをSQLの条件に設定
	 */
	private function _getSearchOptionData($query)
	{
		//検索項目
		if( !is_null(Session::get('ad_search_item')) ){
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listSearchLikeType = config('const.search_like_type');
				$listItem = explode(",", Session::get('ad_search_item'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('ad_search_type'), $listSearchLikeType[Session::get('ad_search_like_type')][0], sprintf($listSearchLikeType[Session::get('ad_search_like_type')][1], $item ));
				}
			});
		}

		$dt = Carbon::now();

		//集計-開始
		if( !empty(Session::get('ad_start_date')) ){
			$query->where('access_date', '>=', preg_replace("/\//", "", Session::get('ad_start_date')).'01');
		}else{
			$query->where('access_date', '>=', $dt->year.$dt->month.'01');
		}

		//集計-終了
		if( !empty(Session::get('ad_end_date')) ){
			list($year, $month) = explode("/", Session::get('ad_end_date'));
			$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
			$query->where('access_date', '<=', preg_replace("/\//", "", Session::get('ad_end_date')).$last_day);
		}else{
			$query->where('access_date', '>=', date('t', mktime(0, 0, 0, $dt->month, 1, $dt->year)));
		}

		$db_data = $query->paginate(config('const.admin_client_list_limit'));
			
		return $db_data;
	}

	/*
	 * 検索結果のdetailリンクを押下後の結果表示
	 * detail画面での集計結果ボタン押下後の検索処理と結果表示
	 */
	public function aggregateMonth(Request $request, $ad_cd)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		list($year, $month) = explode("/", Session::get('ad_start_date'));
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		//動的クエリを生成するため
		$db_data = Result_ad_log::query()
				->join('ad_codes', 'result_ad_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->select('result_ad_logs.access_date', 'ad_codes.name', DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pv) as pv'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.reg) as reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.temp_reg) as temp_reg'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.quit) as quit'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.active) as active'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.order_num) as order_num'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.pay) as pay'), DB::connection(Session::get('operation_select_db'))->raw('sum(result_ad_logs.amount) as amount'))
				->groupBy('result_ad_logs.ad_cd','ad_codes.name','result_ad_logs.access_date')
				->where('ad_codes.agency_id', $agency['agency_id'])
				->where('result_ad_logs.ad_cd', $ad_cd)
				->where('access_date', '>=', preg_replace("/\//", "", Session::get('ad_start_date')).'01')
				->where('access_date', '<=', preg_replace("/\//", "", Session::get('ad_start_date')).$last_day)
				->paginate(config('const.admin_client_list_limit'));

		$list_data = [];
		if( count($db_data) > 0 ){
			setlocale(LC_ALL, 'ja_JP.UTF-8');
			foreach($db_data as $lines){
				$year = substr($lines->access_date,0,4);
				$month = substr($lines->access_date,4,2);
				$day = substr($lines->access_date,6,2);
				$lines->access_date = Carbon::create($year, $month, $day)->formatLocalized('%Y年%m月%d日(%a)');;			
				$list_data[] = $lines;
			}
		}

		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'ad_cd'				=> $ad_cd,
			'db_data'			=> $list_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('agency.index_daily', $disp_data);
	}

}
