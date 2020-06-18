<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\User;
use App\Model\Group;
use App\Model\Point;
use App\Model\Point_log;
use App\Model\Point_setting;
use App\Model\Payment_log;
use App\Model\Magnification_setting;
use App\Model\Top_product;
use App\Model\Client_export_log;
use App\Model\Personal_mail_log;
use App\Model\Create_order_id;
use App\Mail\SendMail;
use Auth;
use Carbon\Carbon;
use Mail;
use Excel;
use Session;
use Utility;
use DB;
use Storage;
use Response;

class AdminPaymenyAnalyticsController extends Controller
{
	private $log_export_obj;
	private $log_history_obj;
	private $log_obj;
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_export_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_export_file_name'));
		$this->log_history_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
		$this->log_obj			 = new SysLog(config('const.operation_point_log_name'), config('const.system_log_dir_path').config('const.operation_point_history_file_name'));
	}
	
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = User::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//画面表示用配列
		$disp_data = [
			'db_data'			=> $db_data,
			'ver'				=> time()
		];
		
		return view('admin.analytics.pay_report.index', $disp_data);
	}

	//クライアント検索画面
	public function search(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//
		$disp_data = [
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'ver'		=> time()
		];
		
		return view('admin.analytics.pay_report.index', $disp_data);
	}
	
	public function searchSetting()
	{	
		//画面表示用配列
		$disp_data = [
			'session'				=> Session::all(),
			'search_like_type'		=> config('const.search_like_type'),
			'ver'					=> time(),
			'list_pay_type'			=> config('const.list_pay_type'),
		];
		
		return view('admin.analytics.pay_report.search_setting', $disp_data);
	}
	
	//クライアント検索処理
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_history_obj->addLog(config('const.admin_display_list')['client_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'db_data'			=> $db_data,
			'ver'				=> time()
		];
		
		return view('admin.analytics.pay_report.index', $disp_data);
	}

	/*
	 * 
	 */
	private function _saveSearchOption(Request $request)
	{
		//
		if( !is_null($request->input('pay_type')) ){
			Session::put('pay_type', $request->input('pay_type'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('pay_type');
		}

		//登録日時-開始
		if( !empty($request->input('start_regdate')) ){
			Session::put('start_regdate', $request->input('start_regdate'));
		}else{
			//未入力なら破棄
			Session::forget('start_regdate');
		}

		//登録日時-終了
		if( !empty($request->input('end_regdate')) ){
			Session::put('end_regdate', $request->input('end_regdate'));
		}else{
			//未入力なら破棄
			Session::forget('end_regdate');
		}

		//入金日時-開始
		if( !empty($request->input('start_paydate')) ){
			Session::put('start_paydate', $request->input('start_paydate'));
		}else{
			//未入力なら破棄
			Session::forget('start_paydate');
		}

		//入金日時-終了
		if( !empty($request->input('end_paydate')) ){
			Session::put('end_paydate', $request->input('end_paydate'));
		}else{
			//未入力なら破棄
			Session::forget('end_paydate');
		}

		//入金回数-開始
		if( !empty($request->input('start_paynum')) ){
			Session::put('start_paynum', $request->input('start_paynum'));
		}else{
			//未入力なら破棄
			Session::forget('start_paynum');
		}

		//入金回数-終了
		if( !empty($request->input('end_paynum')) ){
			Session::put('end_paynum', $request->input('end_paynum'));
		}else{
			//未入力なら破棄
			Session::forget('end_paynum');
		}

		//広告コード
		if( !empty($request->input('ad_cd')) ){
			Session::put('ad_cd', $request->input('ad_cd'));
		}else{
			//未入力なら破棄
			Session::forget('ad_cd');
		}

		//LIKE検索
		if( !is_null($request->input('search_like_type')) ){
			Session::put('search_like_type', $request->input('search_like_type'));
		}else{
			//未入力なら破棄
			Session::forget('search_like_type');
		}
	}
	
	/*
	 * usersテーブルの検索条件を保存されたSessionから設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		$query->join('payment_logs', 'payment_logs.login_id', '=', 'users.login_id');
		$query->select('payment_logs.pay_type', DB::connection(Session::get('operation_select_db'))->raw('count(distinct payment_logs.product_id) as pay_count'), DB::connection(Session::get('operation_select_db'))->raw('count(distinct users.login_id) as pay_user_count'), DB::connection(Session::get('operation_select_db'))->raw('sum(distinct payment_logs.point) as total_add_pt'), DB::connection(Session::get('operation_select_db'))->raw('sum(distinct payment_logs.money) as pay_amount'));
		$query->groupBy('payment_logs.pay_type');
		$query->where('users.status', 1);
		$query->whereIn('payment_logs.status', [0, 3]);

		//決済タイプ
		if( !empty(Session::get('pay_type')) ){
			$query->whereIn('payment_logs.pay_type', explode(",",Session::get('pay_type')));
		}

		//登録日時-開始
		if( !empty(Session::get('start_regdate')) ){
			//現在時刻をyyyymmddhhmmssにフォーマット
			$start_regdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('start_regdate')).'00';
			$query->where('users.regist_date', '>=', $start_regdate);
		}

		//登録日時-終了
		if( !empty(Session::get('end_regdate')) ){
			//現在時刻をyyyymmddhhmmssにフォーマット
			$end_regdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('end_regdate')).'00';
			$query->where('users.regist_date', '<=', $end_regdate);
		}

		//決済日時-開始
		if( !empty(Session::get('start_paydate')) ){
			$start_paydate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('start_paydate')).'00';
			$query->where('payment_logs.regist_date', '>=', $start_paydate);
		}

		//決済日時-終了
		if( !empty(Session::get('end_paydate')) ){
			$end_paydate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('end_paydate')).'00';
			$query->where('payment_logs.regist_date', '<=', $end_paydate);
		}

		//入金回数-開始
		if( !empty(Session::get('start_paynum')) ){		
			$query->where('users.pay_count', '>=', Session::get('start_paynum'));
		}

		//入金回数-終了
		if( !empty(Session::get('end_paynum')) ){
			$query->where('users.pay_count', '<=', Session::get('end_paynum'));
		}

		//広告コード
		if( !empty(Session::get('ad_cd')) ){
	//		$query->where('users.ad_cd', Session::get('ad_cd'));
			$query->where(function($query){
				$listSearchLikeType = config('const.search_like_type');
				$listAdCd = explode(",", Session::get('ad_cd'));
				foreach($listAdCd as $index => $ad_cd){
					$query->orWhere('users.ad_cd', $listSearchLikeType[Session::get('search_like_type')][0], sprintf($listSearchLikeType[Session::get('search_like_type')][1], $ad_cd ));
				}
			});
		}

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->count();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$db_data = $query;

		//通常検索
		}else{
			$db_data = $query->get();
		}
			
		return $db_data;
	}

}
