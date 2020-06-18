<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Payment_log;
use App\Model\Client_export_log;
use DB;
use Utility;
use Excel;
use Session;
use Carbon\Carbon;

class AdminPurchasingTrendsController extends Controller
{
	private $log_obj;
	private $log_export_obj;

	//
	public function __construct()
	{
		$this->log_obj			 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
		$this->log_export_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_trends_export_file_name'));
	}

	/*
	 * 集計-購買動向分析-トップ画面
	 */
	public function index(Request $request)
	{
		//画面表示用配列
		$disp_data = [
			'session'	=> Session::all(),
			'ver'		=> time(),
		];
		
		return view('admin.analytics.trends.index', $disp_data);
	}

	/*
	 * 集計-購買動向分析-抽出処理～購入人数表示
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['trends_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Payment_log::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, '', 'search');

		$disp_data = [
			'session'	=> Session::all(),
			'db_count'	=> count($db_data),
			'ver'		=> time()
		];
		
		return view('admin.analytics.trends.index', $disp_data);
	}

	/*
	 * 検索条件保存
	 */
	private function _saveSearchOption(Request $request)
	{

		//購入金額範囲-開始
		if( !empty($request->input('start_pay')) ){
			Session::put('trends_start_pay', $request->input('start_pay'));
		}else{
			//未入力なら破棄
			Session::forget('trends_start_pay');
		}

		//購入金額範囲-終了
		if( !empty($request->input('end_pay')) ){
			Session::put('trends_end_pay', $request->input('end_pay'));
		}else{
			//未入力なら破棄
			Session::forget('trends_end_pay');
		}

		//購入期間-開始
		if( !empty($request->input('start_purchase')) ){
			$start_purchase = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $request->input('start_purchase'));
			Session::put('trends_start_purchase', $start_purchase);
		}else{
			//未入力なら破棄
			Session::forget('trends_start_purchase');
		}

		//購入期間-終了
		if( !empty($request->input('end_purchase')) ){
			$end_purchase = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $request->input('end_purchase'));
			Session::put('trends_end_purchase', $end_purchase);
		}else{
			//未入力なら破棄
			Session::forget('trends_end_purchase');
		}
	}

	/*
	 * 検索条件取得
	 */
	private function _getSearchOptionData($query, $exec_type = '', $data_type = '')
	{
		$query->join('users', 'payment_logs.login_id', '=', 'users.login_id');

		//取得するカラム名を指定
		if( $data_type == 'search' ){
			$query->select(DB::connection(Session::get('operation_select_db'))->raw('count(payment_logs.login_id) as user_count'));
			$query->groupBy('payment_logs.login_id');
		}elseif( $data_type == '' ){
			$query->select('users.id','users.pay_count','users.pay_datetime','users.last_access_datetime','users.mail_address','payment_logs.sort_date','payment_logs.order_id','payment_logs.login_id',DB::connection(Session::get('operation_select_db'))->raw('sum(payment_logs.money) as amount'));
			$query->groupBy('payment_logs.order_id','payment_logs.login_id','users.mail_address','users.pay_datetime','users.last_access_datetime','users.id','users.pay_count','payment_logs.sort_date');
		}else{
			$query->select('users.id','payment_logs.order_id','payment_logs.pay_count', DB::connection(Session::get('operation_select_db'))->raw('sum(payment_logs.money) as amount'));
			$query->groupBy('users.id','payment_logs.order_id','payment_logs.pay_count');
		}

		$query->whereIn('payment_logs.status',['0','3']);

		//購入金額範囲-開始
		if( !empty(Session::get('trends_start_pay')) ){
			$query->where('money', '>=', Session::get('trends_start_pay'));
		}

		//購入金額範囲-終了
		if( !empty(Session::get('trends_end_pay')) ){
			$query->where('money', '<=', Session::get('trends_end_pay'));
		}

		//購入期間-開始
		if( !empty(Session::get('trends_start_purchase')) ){
			$query->where('payment_logs.sort_date', '>=', preg_replace("/\//", "", Session::get('trends_start_purchase')));
		}

		//購入期間-終了
		if( !empty(Session::get('trends_end_purchase')) ){
			$query->where('payment_logs.sort_date', '<=', preg_replace("/\//", "", Session::get('trends_end_purchase')));
		}

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->count();
		
		//顧客データのエクスポート
		}elseif( $exec_type == config('const.search_exec_type_export_key') ){
			$db_data = $query->get();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$db_data = $query;

		//通常検索
		}else{
			$db_data = $query->paginate(config('const.admin_key_list_limit'));
		}
			
		return $db_data;
	}

	/*
	 * 顧客別CSV出力
	 */
	public function clientExport(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);
		
		//動的クエリを生成するため
		$query = Payment_log::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_export_key'));

		//DBから取得したデータを配列に格納
		$listData = [];
		$listTmpDate = [];
		$listDate = [];
		$listExcelDate = [];

		foreach($db_data as $lines){
			if( empty($listTmpDate[$lines->sort_date]) ){
				$listTmpDate[$lines->sort_date] = 0;
			}
			$listTmpDate[$lines->sort_date]++;
		}
		foreach($listTmpDate as $date => $count){
			for($i=0;$i<$count;$i++){
				$listDate[] = $date.'_'.($i+1);
				$listExcelDate[] = preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $date);
			}
		}
		sort($listDate);
		sort($listExcelDate);

		foreach($db_data as $lines){
			$listData[$lines->id]['email']							 = $lines->mail_address;
			$listData[$lines->id]['pay_count']						 = $lines->pay_count;
			$listData[$lines->id]['last_pay_date']					 = $lines->pay_datetime;
			$listData[$lines->id]['last_access_date']				 = $lines->last_access_datetime;
			$listData[$lines->id]['pay_date'][$lines->sort_date][]	 = $lines->amount;
			$listData[$lines->id]['ave_pay_date']	 = $lines->amount;
			if( empty($listData[$lines->id]['total_amount']) ){
				$listData[$lines->id]['total_amount'] = 0;
			}
			$listData[$lines->id]['total_amount']	 += $lines->amount;
		}

		$listAmount = [];
		foreach($listDate as $date){
			foreach($listData as $client_id => $lines){
				ksort($lines['pay_date']);
				$exist_flg = 'ng';
				foreach($lines['pay_date'] as $pay_date => $list_amount){
					foreach($list_amount as $index => $amount){
						$index_pay_date = $pay_date.'_'.($index+1);
						if( $date == $index_pay_date ){
							$exist_flg = 'ok';
							$listAmount[$client_id][] = $amount;	
						}
					}
				}
				if( $exist_flg == 'ng' ){
					$listAmount[$client_id][] = '';
				}
			}
		}

		//EXCELの1行目
		$listConvertData[] = array_merge(['顧客ID','メールアドレス','入金回数','入金金額','最終入金日時','最終アクセス','平均入金間隔'],$listExcelDate);

		//EXCEL2行目以降 顧客IDごとに日別の売上を出力
		foreach($listAmount as $client_id => $list_amount){
			$listConvertData[] = array_merge([$client_id,$listData[$client_id]['email'],$listData[$client_id]['pay_count'],$listData[$client_id]['total_amount'],$listData[$client_id]['last_pay_date'],$listData[$client_id]['last_access_date'],$listData[$client_id]['ave_pay_date']],$list_amount);
		}

		//$listConvertDataの行と列を入れ替える処理
		$swaps = array();
		for ($i = 0; $i < count($listConvertData[0]); $i++) {
		  foreach ($listConvertData as $value) {
			$swaps[$i][] = $value[$i];
		  }
		}
		$listExcelData = $swaps;

		//EXCELヘッダ部分に追加
		array_unshift($listExcelData,['購入期間',Session::get('trends_start_purchase'),Session::get('trends_end_purchase')]);
		array_unshift($listExcelData,['購入金額範囲',Session::get('trends_start_pay'),Session::get('trends_end_pay')]);
		array_unshift($listExcelData,['サイト名',config('const.html_title')]);
	
		//エクスポートした操作ユーザーの情報をログ出力
		//引数：ログに書き込む内容
		$this->log_export_obj->addLog($user['login_id']);

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['trends_client_export'].",{$user['login_id']}");

		//エクスポートファイル
		$save_export_file = config('const.trends_client_excel_file_name').'_'.date('Ymd_His');

		//保存データ設定
		$export_log_db = new Client_export_log([
			'login_id'	=> $user['login_id'],
			'file'		=> $save_export_file,
		]);

		//DB保存
		$export_log_db->save();


		//Maatwebsite/Laravel-Excelを使用してExcelデータ出力
		Excel::create($save_export_file, function($excel) use($listExcelData) {
			$excel->sheet(config('const.trends_client_excel_seet_name'), function($sheet) use($listExcelData) {
				foreach($listExcelData as $lines){
					$sheet->appendRow($lines);					
				}
			});
		})->export('xls');
		
		return null;
	}

	/*
	 * 購入回数別CSV出力
	 */
	public function paycountExport(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);
		
		//動的クエリを生成するため
		$query = Payment_log::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_export_key'), 'pay_count');

		//DBから取得したデータを配列に格納
		$listData = [];
		$listId = [];

		foreach($db_data as $lines){
			$listId[$lines->id] = $lines->id;
			$listData[$lines->pay_count][$lines->id] = $lines->amount;
		}
		ksort($listData);

		$listExcelData = [];
		foreach($listId as $id){
			foreach($listData as $pay_count => $lines){
				$listExcelData[$pay_count][$id] = '';
				foreach($lines as $client_id => $amount){
					if( $id == $client_id ){
						$listExcelData[$pay_count][$id] = $amount;
					}
				}
			}
		}

		//エクスポートした操作ユーザーの情報をログ出力
		//引数：ログに書き込む内容
		$this->log_export_obj->addLog($user['login_id']);

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['trends_pay_export'].",{$user['login_id']}");

		//エクスポートファイル
		$save_export_file = config('const.trends_pay_excel_file_name').'_'.date('Ymd_His');

		//保存データ設定
		$export_log_db = new Client_export_log([
			'login_id'	=> $user['login_id'],
			'file'		=> $save_export_file,
		]);

		//DB保存
		$export_log_db->save();

		//Maatwebsite/Laravel-Excelを使用してExcelデータ出力
		Excel::create($save_export_file, function($excel) use($listExcelData,$listId) {
			$excel->sheet(config('const.trends_pay_excel_seet_name'), function($sheet) use($listExcelData,$listId) {
				$sheet->appendRow(['サイト名',config('const.html_title')]);
				$sheet->appendRow(['購入金額範囲',Session::get('trends_start_pay'),Session::get('trends_end_pay')]);
				$sheet->appendRow(array_merge(['顧客ID',],$listId));
				foreach($listExcelData as $pay_count => $lines){
					$sheet->appendRow(array_merge([$pay_count.'回目'],$lines));					
				}
			});
		})->export('xls');
		
		return null;
	}

}
