<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Libs\DbPdo;
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
use App\Model\Registered_mail_queue;
use App\Model\Result_ad_log;
use App\Model\Mail_content;
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
use PDO;
use App;
use Illuminate\Support\Facades\Validator;

class AdminClientController extends Controller
{
	private $log_export_obj;
	private $log_history_obj;
	private $log_obj;
	private $dbh;

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

		try {
			$this->dbh = DB::connection(Session::get('operation_select_db'))->getPdo();
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策
//			throw new \PDOException("テスト例外エラー");

		} catch (\PDOException $e) {
			abort('403', __("messages.pdo_connection_err_msg"));
		}
 
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

		$list_group_data = [];

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$listGroup = [];
		if( !empty($db_group_data) > 0 ){
			foreach($db_group_data as $lines){
				$listGroup[$lines->id] = $lines->name;
			}
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'	=> $listGroup,
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}

	/*
	 * USER LISTの一括削除処理
	 */
	public function bulkDeleteSend(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');

		//ブラックリスト削除ID取得(論理削除)
		$listSoftDelId	 = $request->input('soft_del');

		//物理削除ID取得
		$listDelId		 = $request->input('del');
		
		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_bulk_delete'].",{$user['login_id']}");	

		//削除処理
		foreach($listId as $index => $id){
			//$listDelIdが配列かつ削除IDがあれば
			//物理削除
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				User::where('id', $id)->delete();
				Registered_mail_queue::where('client_id', $id)->delete();
			}

			//ブラックリストは論理削除
			if( is_array($listSoftDelId) && in_array($id, $listSoftDelId) ){
				//usersテーブルのdisableを1に更新
				$delete = User::where('id', $id)->update(['disable' => 1]);
			}else{
				$delete = User::where('id', $id)->update(['disable' => 0]);				
			}
		}
						
		return null;
	}

	//クライアント検索画面
	public function search(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$listGroup = [];
		if( !empty($db_group_data) > 0 ){
			foreach($db_group_data as $lines){
				$listGroup[$lines->id] = $lines->name;
			}
		}

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//
		$disp_data = [
			'db_group_data'		=> $listGroup,
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'		=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}
	
	public function searchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$listGroup[] = config("const.not_set_group");
		if( !empty($db_group_data) ){
			foreach($db_group_data as $lines){
				$listGroup[] = [$lines->id, $lines->name];
			}
		}

		//画面表示用配列
		$disp_data = [
			'db_group_data'			=> $listGroup,
			'session'				=> Session::all(),
			'ver'					=> time(),
			'client_search_item'	=> config('const.client_search_item'),
			'search_like_type'		=> config('const.search_like_type'),
			'regist_status'			=> config('const.regist_status'),
			'dm_status'				=> config('const.dm_status'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.sort_list'),
		];
		
		return view('admin.client.client_search', $disp_data);
	}
	
	//クライアント検索処理
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_search'].",{$user['login_id']}");

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$listGroup = [];
		if( !empty($db_group_data) > 0 ){
			foreach($db_group_data as $lines){
				$listGroup[$lines->id] = $lines->name;
			}
		}

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'db_group_data'		=> $listGroup,
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}

	//クライアント検索処理
	public function searchAdPost(Request $request, $ad_cd)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_search'].",{$user['login_id']}");

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'db_group_data'		=> $db_group_data,
			'db_data'			=> $db_data,
			'ver'				=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}

	/*
	 * 
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索タイプ
		if( !is_null($request->input('search_type')) ){
			Session::put('search_type', $request->input('search_type'));
		}

		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('search_item', $request->input('search_item'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('search_item');
		}
		
		//LIKE検索
		if( !is_null($request->input('search_like_type')) ){
			Session::put('search_like_type', $request->input('search_like_type'));
		}

		//グループ
		if( !is_null($request->input('group_id')) ){
			Session::put('group_id', $request->input('group_id'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('group_id');
		}

		//登録状態
		if( !is_null($request->input('reg_status')) ){
			Session::put('reg_status', $request->input('reg_status'));
		}else{
			//チェックがなかったら破棄
			Session::forget('reg_status');
		}

		//DM購読
		if( !is_null($request->input('dm_status')) ){
			Session::put('dm_status', $request->input('dm_status'));
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

		//仮登録-開始
		if( !empty($request->input('start_provdate')) ){
			Session::put('start_provdate', $request->input('start_provdate'));
		}else{
			//未入力なら破棄
			Session::forget('start_provdate');
		}

		//仮登録-終了
		if( !empty($request->input('end_provdate')) ){
			Session::put('end_provdate', $request->input('end_provdate'));
		}else{
			//未入力なら破棄
			Session::forget('end_provdate');
		}

		//最終アクセス-開始
		if( !empty($request->input('start_lastdate')) ){
			Session::put('start_lastdate', $request->input('start_lastdate'));
		}else{
			//未入力なら破棄
			Session::forget('start_lastdate');
		}

		//最終アクセス-終了
		if( !empty($request->input('end_lastdate')) ){
			Session::put('end_lastdate', $request->input('end_lastdate'));
		}else{
			//未入力なら破棄
			Session::forget('end_lastdate');
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

		//入金金額-開始
		if( !empty($request->input('start_payamount')) ){
			Session::put('start_payamount', $request->input('start_payamount'));
		}else{
			//未入力なら破棄
			Session::forget('start_payamount');
		}

		//入金金額-終了
		if( !empty($request->input('end_payamount')) ){
			Session::put('end_payamount', $request->input('end_payamount'));
		}else{
			//未入力なら破棄
			Session::forget('end_payamount');
		}

		//アクション回数-開始
		if( $request->input('start_actnum') != "" ){
			Session::put('start_actnum', $request->input('start_actnum'));
		}else{
			//未入力なら破棄
			Session::forget('start_actnum');
		}

		//アクション回数-終了
		if( $request->input('end_actnum') != "" ){
			Session::put('end_actnum', $request->input('end_actnum'));
		}else{
			//未入力なら破棄
			Session::forget('end_actnum');
		}

		//POINT-開始
		if( $request->input('start_pt') != "" ){
			Session::put('start_pt', $request->input('start_pt'));
		}else{
			//未入力なら破棄
			Session::forget('start_pt');
		}

		//POINT-終了
		if( $request->input('end_pt') != "" ){
			Session::put('end_pt', $request->input('end_pt'));
		}else{
			//未入力なら破棄
			Session::forget('end_pt');
		}


		//ソート
		$sort_item = "id";
		$sort_type = "asc";
		if( !is_null($request->input('sort')) ){
			Session::put('sort', $request->input('sort'));
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('search_disp_num', $request->input('search_disp_num'));
		}
	}
	
	/*
	 * usersテーブルの検索条件を保存されたSessionから設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		//入金金額-開始
		if( !empty(Session::get('start_payamount')) || !empty(Session::get('end_payamount')) ){
			$query->join('payment_logs', 'payment_logs.login_id', '=', 'users.login_id');
			$query->select(DB::connection(Session::get('operation_select_db'))->raw('sum(payment_logs.money) as pay_amount'), 'users.id', 'users.login_id', 'users.ad_cd', 'users.mail_address', 'users.mobile_mail_address', 'users.group_id', 'users.status', 'users.point', 'users.mail_status', 'users.description', 'users.quit_datetime', 'users.updated_at', 'users.credit_certify_phone_no', 'users.pay_datetime', 'users.temporary_datetime', 'users.created_at', 'users.last_access_datetime', 'users.pay_count');
		}

		//削除を省く
//		$query->where('users.disable', 0);

		//検索項目
		if( !is_null(Session::get('search_item')) ){
/*
			$listSearchLikeType = config('const.search_like_type');
			$query->where(Session::get('search_type'), $listSearchLikeType[Session::get('search_like_type')][0], sprintf($listSearchLikeType[Session::get('search_like_type')][1], Session::get('search_item') ));
*/
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listSearchLikeType = config('const.search_like_type');
				$listItem = explode(",", Session::get('search_item'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('search_type'), $listSearchLikeType[Session::get('search_like_type')][0], sprintf($listSearchLikeType[Session::get('search_like_type')][1], $item ));
				}
			});
		}

		//グループ
		if( !empty(Session::get('group_id')) ){
			$query->whereIn('group_id', explode(",",Session::get('group_id')));
		}else{
			if( !is_null(Session::get('group_id')) ){
				$query->where('group_id', 0);
			}
		}
		
		//登録状態
		if( !is_null(Session::get('reg_status')) ){
			//チェックしたindexを配列で取得
			$listSltStatus = explode(",", Session::get('reg_status'));

			foreach($listSltStatus as $index){
				//チェックしたindexが登録状態リスト配列の添え字になってるので、
				//指定した配列内の１番目の値が登録状態の値となる
				$listStatus[] = config('const.regist_status')[$index][0];
			}
			$query->whereIn('users.status', $listStatus);
		}

		//DM購読
		if( !is_null(Session::get('dm_status')) ){
			$listDmStatus = config('const.dm_status');
			$query->whereIn('mail_status', explode(",", $listDmStatus[Session::get('dm_status')][0]));
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

		//仮登録-開始
		if( !empty(Session::get('start_provdate')) ){
			$start_provdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('start_provdate')).'00';
			$query->where('sort_temporary_datetime', '>=', $start_provdate);
		}

		//仮登録-終了
		if( !empty(Session::get('end_provdate')) ){
			$end_provdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('end_provdate')).'00';
			$query->where('sort_temporary_datetime', '<=', $end_provdate);
		}

		//最終アクセス-開始
		if( !empty(Session::get('start_lastdate')) ){
			$start_lastdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('start_lastdate')).'00';
			$query->where('sort_last_access_datetime', '>=', $start_lastdate);
		}

		//最終アクセス-終了
		if( !empty(Session::get('end_lastdate')) ){
			$end_lastdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('end_lastdate')).'00';
			$query->where('sort_last_access_datetime', '<=', $end_lastdate);
		}

		//入金日時-開始
		if( !empty(Session::get('start_paydate')) ){
			$start_paydate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('start_paydate')).'00';
			$query->where('sort_pay_datetime', '>=', $start_paydate);
		}

		//入金日時-終了
		if( !empty(Session::get('end_paydate')) ){
			$end_paydate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('end_paydate')).'00';
			$query->where('sort_pay_datetime', '<=', $end_paydate);
		}

		//入金金額-開始
		if( !empty(Session::get('start_payamount')) ){
			$query->havingRaw('sum(payment_logs.money) >= '.Session::get('start_payamount'));
		}

		//入金金額-終了
		if( !empty(Session::get('end_payamount')) ){
			$query->havingRaw('sum(payment_logs.money) <= '.Session::get('end_payamount'));
		}

		//入金回数-開始
		if( !empty(Session::get('start_paynum')) ){		
			$query->where('users.pay_count', '>=', Session::get('start_paynum'));
		}

		//入金回数-終了
		if( !empty(Session::get('end_paynum')) ){
			$query->where('users.pay_count', '<=', Session::get('end_paynum'));
		}

		//アクション回数-開始
		if( Session::get('start_actnum') != "" ){
			$query->where('users.action', '>=', Session::get('start_actnum'));			
		}

		//アクション回数-終了
		if( Session::get('end_actnum') != "" ){
			$query->where('users.action', '<=', Session::get('end_actnum'));			
		}

		//POINT-開始
		if( Session::get('start_pt') != "" ){
			$query->where('users.point', '>=', Session::get('start_pt'));
		}

		//POINT-終了
		if( Session::get('end_pt') != "" ){
			$query->where('users.point', '<=', Session::get('end_pt'));
		}

		//入金金額が指定されたとき
		if( !empty(Session::get('start_payamount')) || !empty(Session::get('end_payamount')) ){
			$query->groupBy('users.id');
		}

		//ソート
		$sort_item = "id";
		$sort_type = "desc";
		if( !is_null(Session::get('sort')) ){
			$listSortType = config('const.sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('sort')][0]);
			$query->orderBy($sort_item, $sort_type);
		}else{
			$query->orderBy($sort_item, $sort_type);			
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null(Session::get('search_disp_num')) ){
			$list_disp_limit = config('const.search_disp_num');
			$disp_limit = $list_disp_limit[Session::get('search_disp_num')];
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
			$db_data = $query->paginate($disp_limit);
		}
/*
		Session::forget('start_payamount');
		Session::forget('end_payamount');
		Session::forget('search_item');
		Session::forget('search_type');
		Session::forget('search_like_type');
		Session::forget('group_id');
		Session::forget('reg_status');
		Session::forget('dm_status');
		Session::forget('start_regdate');
		Session::forget('end_regdate');
		Session::forget('start_provdate');
		Session::forget('end_provdate');
		Session::forget('start_lastdate');
		Session::forget('end_lastdate');
		Session::forget('start_paydate');
		Session::forget('end_paydate');
		Session::forget('start_payamount');
		Session::forget('start_paynum');
		Session::forget('end_paynum');
		Session::forget('start_actnum');
		Session::forget('end_actnum');
		Session::forget('start_pt');
		Session::forget('end_pt');
		Session::forget('start_payamount');
		Session::forget('end_payamount');
		Session::forget('sort');
		Session::forget('search_disp_num');
*/
		return $db_data;
	}
	
	//クライアント検索エクスポート
	public function clientExport(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);
		
		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_export_key'));

		//DBから取得したデータを配列に格納
		$listData = [];
		foreach($db_data as $lines){
			$listData[] = [
				$lines->id,
				$lines->login_id,
				$lines->mail_address,
				$lines->status,
				$lines->mail_status,
				$lines->point,
				$lines->action,
				$lines->pay_count,
				$lines->pay_amount,
				$lines->group_id,
				$lines->ad_cd,
				$lines->credit_certify_phone_no,
				$lines->pay_datetime,
				$lines->temporary_datetime,
				$lines->created_at,
				$lines->last_access_datetime,
				$lines->updated_at,
				$lines->quit_datetime,
				$lines->description
			];
		}

		//エクスポートした操作ユーザーの情報をログ出力
		//引数：ログに書き込む内容
		$this->log_export_obj->addLog($user['login_id']);

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_export'].",{$user['login_id']}");

		//エクスポートファイル
		$save_export_file = config('const.excel_file_name').'_'.date('Ymd_His');

		//保存データ設定
		$export_log_db = new Client_export_log([
			'login_id'	=> $user['login_id'],
			'file'		=> $save_export_file,
		]);

		//DB保存
		$export_log_db->save();
		
		//Maatwebsite/Laravel-Excelを使用してExcelデータ出力
		Excel::create($save_export_file, function($excel) use($listData) {
			$excel->sheet(config('const.excel_seet_name'), function($sheet) use($listData) {
				//Excelデータの1行目を出力
				$sheet->row(1, config('const.export_file_header_column'));
				
				//DBデータ件数取得
				$listCount = count($listData);

				//2行目以降ループしながらデータ出力
				for($i=0; $i<$listCount; $i++){
					$sheet->appendRow($listData[$i]);
				}
			});
		})->export('xls');
		
		return null;
	}
	
	private function _saveExportSearchOption(Request $request)
	{
		//エクスポート開始日時
		if( !empty($request->input('start_export_date')) ){
			Session::put('start_export_date', $request->input('start_export_date'));
		}else{
			//未入力なら破棄
			Session::forget('start_export_date');
		}
		
		//エクスポート終了日時
		if( !empty($request->input('end_export_date')) ){
			Session::put('end_export_date', $request->input('end_export_date'));
		}else{
			//未入力なら破棄
			Session::forget('end_export_date');
		}
		
		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('sort', $request->input('sort'));
		}
	}
	
	private function _getExportSearchOptionData($query)
	{
		//
		if( !is_null(Session::get('start_export_date')) ){
			$query->where('created_at', '>=', Session::get('start_export_date'));
		}

		//
		if( !is_null(Session::get('end_export_date')) ){
			$query->where('created_at', '<=', Session::get('end_export_date'));
		}

		//ソート
		if( !is_null(Session::get('sort')) ){
			$query->orderBy('created_at', config('const.list_export_sort')[Session::get('sort')]);
		}
		
		$db_data = $query->paginate(config('const.admin_client_list_limit'));
		
		return $db_data;
	}
	
	public function clientExportOperationLog(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索ボタンを押下したときだけ検索条件をセッションに保存
		if( $request->input('submit') == 1 ){
			$this->_saveExportSearchOption($request);
		}
			
		//動的クエリを生成するため
		$query = Client_export_log::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getExportSearchOptionData($query);

		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'ver'				=> time(),
		];
		
		return view('admin.client.export_log', $disp_data); 
	}
	
	//クライアント編集画面
	public function edit(Request $request, $page, $id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//DBのusersテーブルからデータ取得
//		$db_data = User::where('id',$id)->where('disable', 0)->first();
		$db_data = User::where('id',$id)->first();

		//編集データがない場合、顧客データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_client_path'));
		}
		
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		$listGroup[] = config("const.not_set_group");
		if( !empty($db_group_data) ){
			foreach($db_group_data as $lines){
				$listGroup[] = [$lines->id, $lines->name];
			}
		}

		//戻るリンクのデフォルトを顧客管理一覧に設定
		$back_url = config('const.base_admin_url').'/'.config('const.client_url_path').'?page=';
		
		//閲覧者検索から来た場合の戻るリンク
		if( !empty($request->input('back')) ){
			$back_url = config('const.base_admin_url').'/'.config('const.visitor_url_path').'?page=';
		}

		$back_btn_flg = 1;
		//戻るボタンを表示するかどうか
		if( !is_null($request->input('back_btn')) ){
			$back_btn_flg = $request->input('back_btn');
		}

		//
		$disp_data = [
			'back_btn_flg'		=> $back_btn_flg,
			'back_url'			=> $back_url,
			'db_data'			=> $db_data,
			'db_group_data'		=> $listGroup,
			'dm_status'			=> config('const.edit_dm_status'),
			'page'				=> $page,
			'ver'				=> time(),
		];
		
		return view('admin.client.edit', $disp_data); 
	}
	
	/*
	 * 顧客編集画面からポイントの手動追加画面を表示
	 */
	public function addPoint($id)
	{	
		//pointsテーブルから手動ポイントのリストを取得
//		$list_point = Point::where('pay_type', 'bank')->get();

		$now_date = Carbon::now();

		//倍率設定済の購入ポイント取得
		$query = Magnification_setting::query();
		$query->join('point_settings', 'magnification_settings.category_id', '=', 'point_settings.category_id');
		$query->where('magnification_settings.start_date','<=', $now_date);
		$query->where('magnification_settings.end_date', '>=', $now_date);
		$db_data = $query->get();

		//倍率設定がされていなければ通常設定を取得
		if( count($db_data) == 0 ){
			//magnification_settingsテーブルの通常設定IDを取得
			$db_data = Magnification_setting::first();
			if( !empty($db_data) ){
				//通常設定の購入ポイントを取得
				$query = Point_setting::query();
				$db_data = $query->where('category_id', $db_data->default_id)->get();
			}
		}

		$disp_data = [
			'id'			=> $id,
			'list_point'	=> $db_data,	
			'ver'			=> time(),
		];
		
		return view('admin.client.add_point', $disp_data); 
	}
	
	/*
	 * 顧客編集画面からポイントの手動追加画面を表示→追加処理
	 */
	public function addPointSend(Request $request, $id)
	{
		//追加するポイントが選択されているかチェック
		$this->validate($request, [
			'point_id'	 => 'required',
		]);

		try{
			//ログイン管理者情報取得
			$user = Utility::getAdminDefaultDispParam();

			//選択したポイントをpoint_settingテーブルから取得
			$point = Point_setting::where('id', $request->input('point_id'))->first();

			//usersテーブルから現在のポイント取得
			$current_point = User::where('id',$id)->get(['point'])->first();

			DB::connection(Session::get('operation_select_db'))->transaction(function() use($id, $point){
				//usersテーブルへポイント追加更新
				$update = User::where('id',$id)->increment('point', $point->point);
			});

			//更新後のポイント取得用の動的クエリを生成するため
			$query = User::query();

			//更新後のポイントを取得
			$db_data = $query->where('id',$id)->get(['point','login_id'])->first();

			//ポイントログ履歴用の動的クエリを生成するため
			$log = new Point_log([
				'login_id'					=> $db_data->login_id,
				'add_point'					=> $point->point,
				'prev_point'				=> $current_point->point,
				'current_point'				=> $db_data->point,
				'operator'					=> $user['login_id']
			]);

			//データをinsert
			$log->save();

		}catch(\Exception $e){
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['client_point_add_err'].",{$user['login_id']}");

			//編集画面側の更新ボタン押下後の$.ajax通信結果をerrorのレスポンスで返す
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$id},{$point->point},{$current_point->point},{$db_data->point},{$user['login_id']}");

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_point_add'].",{$user['login_id']}");

		return null;
	}
	
	/*
	 * 顧客編集画面から個別メール画面表示
	 */
	public function editMail($id)
	{	
		//usersテーブルからデータ取得
		$user = User::where('id', $id)->first();
		
		$disp_data = [
			'id'		=> $id,
			'user'		=> $user,
			'ver'		=> time(),
		];
		
		return view('admin.client.edit_mail', $disp_data); 
	}
	
	/*
	 * 顧客編集画面→個別メール画面表示→メール送信処理
	 */
	public function editMailSend(Request $request)
	{
		$validate = [
			'from_name'	=> 'bail|required|surrogate_pair_check|emoji_check',
			'from_mail'	=> 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'to_mail'	=> 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'subject'	=> 'bail|required|max:'.config('const.subject_length').'|surrogate_pair_check|emoji_check',
			'body'		=> 'bail|surrogate_pair_check|emoji_check',
			'html_body'	=> 'bail|surrogate_pair_check|emoji_check',
		];
		
		//メール内容が空なら
		if( empty($request->input('body')) && empty($request->input('html_body')) ){
			$validate['body'] = 'bail|required|surrogate_pair_check|emoji_check';
		}
		
		//エラーチェック
		$this->validate($request, $validate);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//HTMLメールフラグ(デフォルトはテキストメール)
		$mail_html_flg	= false;
		$body			= $request->input('body');

		//HTMLメールなら
		if( !empty($request->input('html_body')) ){
			$mail_html_flg	= true;
			$body			= $request->input('html_body');
		}

		list($host_ip, $port) = Utility::getSmtpHost('personal');

		//送信元情報設定
		$options = [
			'client_id'	 => $request->input('id'),
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'html_flg'	 => $mail_html_flg,
			'from'		 => $request->input('from_mail'),
			'from_name'	 => $request->input('from_name'),
			'subject'	 => $request->input('subject'),
			'template'	 => $user['select_db'].'.'.config('const.admin_edit_mail'),
		];

		//送信データ設定
		$data = [
			'contents'		=> $body,
		];

		$err_flg = Utility::checkNgWordEmail($request->input('to_mail'), Session::get('operation_select_db'));

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}

		//%変換設定で設定した文字列が含まれていれば変換を行う
		$options['subject'] = Utility::getConvertData($options['subject']);
		$data['contents'] = Utility::getConvertData($data['contents']);

		$db_data = User::where('id', $request->input('id'))->first();
		if( !empty($db_data) ){
			$data['contents'] = preg_replace("/\-%login_id\-/", $db_data->login_id, $data['contents']);
			$data['contents'] = preg_replace("/\-%password\-/", $db_data->password_raw, $data['contents']);
			$data['contents'] = preg_replace("/\-%token\-/", $db_data->remember_token, $data['contents']);
		}

		//メールアドレス先へメール送信
		Mail::to($request->input('to_mail'))->send( new SendMail($options, $data) );

		//個別メール送信履歴テーブル(personal_mail_logs)に個別メールの送信情報をinsert
		$mail_log = new Personal_mail_log([
			'client_id'	=> $request->input('id'),
			'subject'	=> $options['subject'],
			'body'		=> $data['contents'],
		]);

		//データをinsert
		$mail_log->save();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_mail'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 個別メールの送信履歴画面を表示
	 */
	public function historyMailLog($id)
	{	
		//usersテーブルからデータ取得
		$db_data = Personal_mail_log::where('client_id', $id)->get();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_mail_history'].",{$user['login_id']}");
		
		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];

		return view('admin.client.history_mail_log', $disp_data); 
	}

	/*
	 * 個別メールの送信履歴の詳細画面を表示
	 */
	public function historyMailLogDetail($id, $detail_id)
	{
		//送信履歴のデータを取得
		$query = User::query();
		$db_data = $query->join('personal_mail_logs', 'users.id', '=', 'personal_mail_logs.client_id')
			->where([
				'personal_mail_logs.client_id'	=> $id,
				'personal_mail_logs.id'			=> $detail_id
			])->first();
	
		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.client.history_mail_log_detail', $disp_data); 
	}

	/*
	 * クライアント編集処理
	 */
	public function store(Request $request)
	{
		//編集しているusersテーブルのidを取得
		$edit_id = $request->input('id');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//アカウント編集
		if( empty($request->input('del')) ){
			$validate = [
	//Rule::uniqueでDB切換えがうまくいかないのでコメント
	//			'name'			=> ['bail', 'check_user_login_id', Rule::unique('users', 'login_id')->whereNot('id', $edit_id)->whereNot('login_id', '')],
				'email'			=> 'bail|required|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address,'.$edit_id.',id|check_mx_domain',
				'point'			=> 'bail|required|integer',
				'ad_cd'			=> 'check_alpha_dash',
			];

			if( !empty($request->input('name')) ){
				$validate['name'] = 'bail|required|check_user_login_id|check_exist_login_id:'.$edit_id;
			}

			if( !empty($request->input('new_password')) ){
				$validate['new_password'] = 'bail|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space';
			}

			//電話番号になにか入力されていたら
			if( !empty($request->input('tel')) ){
				$validate['tel'] = 'numeric';
			}

			//MEMOになにか入力されていたら
			if( !empty($request->input('description')) ){
				$validate['description'] = 'json';
			}

			//エラーチェック
			$this->validate($request, $validate);

			try{
				//トランザクション開始
				$this->dbh->beginTransaction();

				//usersテーブルから現在のポイント取得
				$stmt = $this->dbh->prepare("select point from users where id = :id for update");
				$stmt->bindValue(":id", $edit_id);
				$stmt->execute();

				$current_point = 0;
				while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
					$current_point = $row['point'];
				}

				$now_date = Carbon::now();

				$update_param = [
					'status = :status'									=> $request->input('status'),
					'login_id = :login_id'								=> $request->input('name'),
					'password = :password'								=> bcrypt($request->input('new_password')),
					'password_raw = :password_raw'						=> $request->input('new_password'),
					'ad_cd = :ad_cd'									=> $request->input('ad_cd'),
					'point = :point'									=> $request->input('point'),
					'mail_address = :mail_address'						=> mb_strtolower(trim($request->input('email'))),
					'mail_status = :mail_status'						=> $request->input('mail_status'),
					'credit_certify_phone_no = :credit_certify_phone_no'=> $request->input('tel'),
					'group_id = :group_id'								=> $request->input('group_id'),
					'description = :description'						=> $request->input('description')
					];

				//アカウント停止
				if( !empty($request->input('soft_del')) ){
					$update_param['disable = :disable'] = $request->input('soft_del');
				}else{
					$update_param['disable = :disable'] = 0;
				}

				try{

					//初めて本登録するとき
					if( $request->input('regist_date') == '' && $request->input('status') == config('const.db_regist_status')['1'] ){
						$update_param['regist_date = :regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
						$update_param['quit_datetime = :quit_datetime'] = null;
						$update_param['sort_quit_datetime = :sort_quit_datetime'] = null;

					//仮登録
					}else if( $request->input('temporary_date') == '' && $request->input('status') == config('const.db_regist_status')['0'] ){
						$update_param['temporary_datetime = :temporary_datetime'] = $now_date;
						$update_param['sort_temporary_datetime = :sort_temporary_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
						$update_param['quit_datetime = :quit_datetime'] = null;
						$update_param['sort_quit_datetime = :sort_quit_datetime'] = null;

					//退会
					}else if( $request->input('status') == config('const.db_regist_status')['2'] ){
						$update_param['quit_datetime = :quit_datetime'] = $now_date;
						$update_param['sort_quit_datetime = :sort_quit_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

					//ブラック
					}else if( $request->input('status') == config('const.db_regist_status')['3'] ){
						$update_value['status'] = config('const.db_regist_status')['3'];
						$update_param['status = :status'] = config('const.db_regist_status')['3'];
					}
//error_log(print_r($update_param,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
//error_log("update users set ".implode(", ", array_keys($update_param))." where id = :id\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
					//空のデータは更新省く
					$new_update_param = [];
					foreach($update_param as $column => $param){
						if( !isset($param) ){
							$param = null;
//							continue;
						}
						$new_update_param[$column] = $param;
					}

					$stmt = $this->dbh->prepare("update users set ".implode(", ", array_keys($new_update_param))." where id = :id");
					foreach($new_update_param as $column => $param){
						list($name, $name_value) = explode(" = ", $column);
						$stmt->bindValue($name_value, $param);
					}
					$stmt->bindValue(":id", $edit_id);
					$stmt->execute();

					//更新後のポイントを取得
					$stmt = $this->dbh->prepare("select point from users where id = :id for update");
					$stmt->bindValue(":id", $edit_id);
					$stmt->execute();

					$now_point = 0;
					while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
						$now_point = $row['point'];
					}

					if( $now_point != $current_point ){
						$add_point = abs($now_point - $current_point);

						//ポイントログ履歴用の動的クエリを生成するため
						$log = new Point_log([
							'login_id'					=> $request->input('name'),
							'add_point'					=> $add_point,
							'prev_point'				=> $current_point,
							'current_point'				=> $now_point,
							'operator'					=> $user['login_id']
						]);

						//データをinsert
						$log->save();

						//ポイント履歴ログ出力
						$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$edit_id},{$add_point},{$current_point},{$now_point},{$user['login_id']}");
					}

					$this->dbh->commit();

					//ログ出力
					$this->log_history_obj->addLog(config('const.admin_display_list')['client_edit_update'].",{$user['login_id']}");

					return null;

				}catch(\Exception $e){
					$this->dbh->rollback();

					//ログ出力
					$this->log_obj->addLog(config('const.admin_display_list')['client_edit_update_err'].",{$user['login_id']}");

					//編集画面側の更新ボタン押下後の$.ajax通信結果をerrorのレスポンスで返す
					return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
				}

			}catch(\Exception $e){
				$this->dbh->rollback();

				//編集画面側の更新ボタン押下後の$.ajax通信結果をerrorのレスポンスで返す
				return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
			}

		//アカウント削除
		}else{
			$delete = User::where('id', $edit_id)->delete();
//			$delete = User::where('id', $edit_id)->update(['disable' => 1]);		
			Registered_mail_queue::where('client_id', $edit_id)->delete();

			//ログ出力
			$this->log_history_obj->addLog(config('const.admin_display_list')['client_edit_delete'].",{$user['login_id']}");

			return null;
		}
	}
	
	//クライアント新規作成画面
	public function create()
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//DBのgroupテーブルからデータ取得
		$db_grpup_data = Group::get();
		
		//
		$disp_data = [
			'db_grpup_data'		=> $db_grpup_data,
			'dm_status'			=> config('const.edit_dm_status'),
			'ver'				=> time(),
		];
		
		return view('admin.client.create', $disp_data); 
	}
	
	public function createSend(Request $request)
	{
		$validate = [
			'name'			=> 'bail|required|digits:'.config('const.login_id_length').'|unique:'.Session::get('operation_select_db').'.users,login_id',
			'password'		=> 'bail|required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
			'email'			=> 'bail|required|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address|check_mx_domain',
			'point'			=> 'bail|required|integer',
			'ad_cd'			=> 'alpha_num_check',
		];

		//電話番号になにか入力されていたら
		if( !empty($request->input('tel')) ){
			$validate['tel'] = 'numeric';
		}

		//MEMOになにか入力されていたら
		if( !empty($request->input('description')) ){
			$validate['description'] = 'json';
		}

		//エラーチェック
		$this->validate($request, $validate);

		$now_date = Carbon::now();

		//アクセスキー生成
		$remember_token = session_create_id();		

		//登録データ
		$create_data = [
			'login_id'					=> $request->input('name'),
			'password'					=> bcrypt($request->input('password')),
			'password_raw'				=> $request->input('password'),
			'ad_cd'						=> $request->input('ad_cd'),
			'point'						=> $request->input('point'),
			'status'					=> $request->input('status'),
			'mail_address'				=> mb_strtolower(trim($request->input('email'))),
			'mail_status'				=> $request->input('mail_status'),
			'credit_certify_phone_no'	=> $request->input('tel'),
			'group_id'					=> $request->input('group_id'),
			'description'				=> $request->input('description'),
			'remember_token'			=> $remember_token,
			'created_at'				=> $now_date,
			'updated_at'				=> $now_date
		];

		//本登録
		if( $request->input('status') == config('const.db_regist_status')['1'] ){
			$create_data['temporary_datetime'] = $now_date;
			$create_data['sort_temporary_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			$create_data['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//仮登録
		}else if( $request->input('status') == config('const.db_regist_status')['0'] ){
			$create_data['temporary_datetime'] = $now_date;
			$create_data['sort_temporary_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//退会
		}else if( $request->input('status') == config('const.db_regist_status')['2'] ){
			$create_data['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			$create_data['quit_datetime'] = $now_date;
			$create_data['sort_quit_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//ブラック
		}else if( $request->input('status') == config('const.db_regist_status')['3'] ){
			$create_data['temporary_datetime'] = $now_date;
			$create_data['sort_temporary_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';		
			$create_data['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			$create_data['status'] = config('const.db_regist_status')['3'];
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		try{
			DB::connection(Session::get('operation_select_db'))->transaction(function() use($create_data){
				$user = new User($create_data);

				//DB保存
				$user->save();
			});
		}catch(\Exception $e){
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['client_create_err'].",{$user['login_id']}");

			//編集画面側の更新ボタン押下後の$.ajax通信結果をerrorのレスポンスで返す
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_create'].",{$user['login_id']}");

		return null;
	}
	
	//クライアント-ステータス変更画面
	public function group()
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));
		
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$list_groups = [];
		if( !empty($db_group_data) ){
			foreach($db_group_data as $lines){
				$list_groups[$lines->id] = ['name' => $lines->name, 'memo' => $lines->memo];
			}
		}

		//
		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_search_count'	=> $db_search_count,
			'db_group_data'		=> $list_groups,
			'ver'				=> time(),
		];
		
		return view('admin.client.group', $disp_data); 
	}
	
	/*
	 * 
	 */
	public function groupSearchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'			=> $db_group_data,
			'session'				=> Session::all(),
			'ver'					=> time(),
			'client_search_item'	=> config('const.client_search_item'),
			'search_like_type'		=> config('const.search_like_type'),
			'regist_status'			=> config('const.regist_status'),
			'dm_status'				=> config('const.dm_status'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.sort_list'),
		];
		
		return view('admin.client.group_search', $disp_data);
	}
	
	//クライアント-グループ移行画面
	public function groupSearchCount(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、結果件数を表示
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$list_groups = [];
		if( !empty($db_group_data) ){
			foreach($db_group_data as $lines){
				$list_groups[$lines->id] = ['name' => $lines->name, 'memo' => $lines->memo];
			}
		}

		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_group_data'		=> $list_groups,
			'db_search_count'	=> $db_search_count,
			'ver'				=> time(),
		];

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['group_search'].",{$user['login_id']}");

		return view('admin.client.group', $disp_data); 
	}
	
	public function groupSearchMove(Request $request)
	{
		$this->validate($request, [
			'group_id'	=> 'required'
		]);
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、結果件数を表示
		$db_search_where = $this->_getSearchOptionData($query, config('const.search_exec_type_unexecuted_key'));

		//検索条件でグループ更新
		try{
			DB::connection(Session::get('operation_select_db'))->transaction(function() use($db_search_where, $request){
				$update = $db_search_where->update([
					'group_id'	=> $request->input('group_id')
				]);
			});
		}catch(\Exception $e){
			//ログ出力
			$this->log_history_obj->addLog(config('const.admin_display_list')['group_move_err'].",{$user['login_id']}");

			//編集画面側の更新ボタン押下後の$.ajax通信結果をerrorのレスポンスで返す
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['group_move'].",{$user['login_id']}");

		return null;
	}
	
	//クライアント-ステータス変更画面
	public function status()
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、データ取得
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));
		
		//
		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_search_count'	=> $db_search_count,
			'ver'				=> time(),
		];
		
		return view('admin.client.status', $disp_data); 
	}
	
	/*
	 * 
	 */
	public function statusSearchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'			=> $db_group_data,
			'session'				=> Session::all(),
			'ver'					=> time(),
			'client_search_item'	=> config('const.client_search_item'),
			'search_like_type'		=> config('const.search_like_type'),
			'regist_status'			=> config('const.regist_status'),
			'dm_status'				=> config('const.dm_status'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.sort_list'),
		];
		
		return view('admin.client.status_search', $disp_data);
	}
	
	/*
	 * 顧客データインポート画面表示
	 */
	public function importClientData(Request $request)
	{
		$exist_bad_email_flg = Storage::disk('logs')->exists(config('const.import_error_email_file_name'));
		$exist_mx_domain_flg = Storage::disk('logs')->exists(config('const.import_mx_domain_error_file_name'));
		$exist_duplicate_flg = Storage::disk('logs')->exists(config('const.import_error_file_name'));

		$disp_data = [
			'bad_email_flg'		=> $exist_bad_email_flg,
			'mx_domain_flg'		=> $exist_mx_domain_flg,
			'duplicate_flg'		=> $exist_duplicate_flg,
			'ver'				=> time(),
		];
		
		return view('admin.client.import', $disp_data); 
	}
	
	/*
	 * 顧客データインポート処理
	 */
	public function importClientUpload(Request $request)
	{
		$this->validate($request, [
			'ad_cd'	 => 'alpha_num_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_import'].",{$user['login_id']}");
		
		//広告コード取得
		$ad_cd = $request->input('ad_cd');
		
		//アップロードファイルオブジェクト取得
		$file = $request->file('import_file');

		//アップロードファイル名取得
		$upload_file = $file->getClientOriginalName();
		
		//アップロードファイルのディレクトリ移動
		$file->move( config('const.save_import_file_dir'), $upload_file );

		//バックグラウンドでusersテーブルへアップロードデータをinsertするためのオブジェクト生成
		$process = new Process(config('const.artisan_command_path')." file:upload {$user['select_db']} {$upload_file} {$ad_cd} > /dev/null");

		//非同期実行(/data/www/jray/app/Console/Commands/FileUpload.php)
		$process->start();

		$disp_data = [
			'ver'				=> time(),
		];

		return null;
	}

	/*
	 * 
	 */
	public function downLoadBadEmail()
	{
		$stream = fopen('php://temp', 'w');

		//ログ読込み
		$contents = explode("\n",Storage::disk('logs')->get(config('const.import_error_email_file_name')));

		//1行取り出して日付・メールアドレスを抽出
		foreach($contents as $lines){
			//空をスキップ
			if( empty($lines) ){
				continue;
			}

			//日付・メールアドレス抽出
			list($date, $email) = explode(",", preg_replace("/^\[(.+?)\]\s.+?\.INFO:\s(.+?)\s\[\].+/", "$1,$2", $lines));

			//1行をCSV形式で書き込む
			fputcsv($stream, [$date,$email], ',');
		}
		rewind($stream);

		$csv = mb_convert_encoding(str_replace(PHP_EOL, "\r\n", stream_get_contents($stream)), 'SJIS', 'UTF-8');

		$headers = array(
		  'Content-Type' => 'text/csv',
		  'Content-Disposition' => 'attachment; filename="'.config('const.dl_unknown_mx_domain_file_name').'"'
		);

		return Response::make($csv, 200, $headers);
	}

	/*
	 * 
	 */
	public function downLoadUnknownMxDomain()
	{
		$stream = fopen('php://temp', 'w');

		//ログ読込み
		$contents = explode("\n",Storage::disk('logs')->get(config('const.import_mx_domain_error_file_name')));

		//1行取り出して日付・メールアドレスを抽出
		foreach($contents as $lines){
			//空をスキップ
			if( empty($lines) ){
				continue;
			}

			//日付・メールアドレス抽出
			list($date, $email) = explode(",", preg_replace("/^\[(.+?)\]\s.+?\.INFO:\s(.+?)\s\[\].+/", "$1,$2", $lines));

			//1行をCSV形式で書き込む
			fputcsv($stream, [$date,$email], ',');
		}
		rewind($stream);

		$csv = mb_convert_encoding(str_replace(PHP_EOL, "\r\n", stream_get_contents($stream)), 'SJIS', 'UTF-8');

		$headers = array(
		  'Content-Type' => 'text/csv',
		  'Content-Disposition' => 'attachment; filename="'.config('const.dl_unknown_mx_domain_file_name').'"'
		);

		return Response::make($csv, 200, $headers);
	}

	/*
	 * 
	 */
	public function downLoadDuplicateEmail()
	{
		$stream = fopen('php://temp', 'w');

		//ログ読込み
		$contents = explode("\n",Storage::disk('logs')->get(config('const.import_error_file_name')));

		//1行取り出して日付・メールアドレスを抽出
		foreach($contents as $lines){
			//空をスキップ
			if( empty($lines) ){
				continue;
			}

			//日付・メールアドレス抽出
			list($date, $email) = explode(",", preg_replace("/^\[(.+?)\]\s.+?\.INFO:\s(.+?)\s\[\].+/", "$1,$2", $lines));

			//1行をCSV形式で書き込む
			fputcsv($stream, [$date,$email], ',');
		}
		rewind($stream);

		$csv = mb_convert_encoding(str_replace(PHP_EOL, "\r\n", stream_get_contents($stream)), 'SJIS', 'UTF-8');

		$headers = array(
		  'Content-Type' => 'text/csv',
		  'Content-Disposition' => 'attachment; filename="'.config('const.dl_duplicate_file_name').'"'
		);

		return Response::make($csv, 200, $headers);
	}

	/*
	 * 
	 */
	public function deleteBadEmail()
	{
		//ログファイル名取得
		$file = config('const.import_error_email_file_name');

		//ファイル存在チェック
		$exist_bad_email_flg = Storage::disk('logs')->exists($file);

		//ファイル削除
		if( !empty($exist_bad_email_flg) ){
			Storage::disk('logs')->delete($file);
		}

		//顧客データインポート画面へリダイレクト
		return redirect(config('const.base_admin_url').config('const.admin_client_import_path'));
	}

	/*
	 * 
	 */
	public function deleteUnknownMxDomain()
	{
		//ログファイル名取得
		$file = config('const.import_mx_domain_error_file_name');

		//ファイル存在チェック
		$exist_mx_domain_flg = Storage::disk('logs')->exists($file);

		//ファイル削除
		if( !empty($exist_mx_domain_flg) ){
			Storage::disk('logs')->delete($file);
		}

		//顧客データインポート画面へリダイレクト
		return redirect(config('const.base_admin_url').config('const.admin_client_import_path'));
	}

	/*
	 * 
	 */
	public function deleteDuplicateEmail()
	{
		//ログファイル名取得
		$file = config('const.import_error_file_name');

		//ファイル存在チェック
		$exist_duplicate_flg = Storage::disk('logs')->exists($file);

		//ファイル削除
		if( !empty($exist_duplicate_flg) ){
			Storage::disk('logs')->delete($file);
		}

		//顧客データインポート画面へリダイレクト
		return redirect(config('const.base_admin_url').config('const.admin_client_import_path'));
	}

	//クライアント-ステータス変更画面
	public function statusSearchCount(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、結果件数を表示
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_group_data'		=> $db_group_data,
			'db_search_count'	=> $db_search_count,
			'ver'				=> time(),
		];

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_status_change'].",{$user['login_id']}");

		return view('admin.client.status', $disp_data); 
	}
	
	//クライアント-ステータス変更画面
	public function statusSearchList(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//動的クエリを生成するため
		$query = User::query();
		
		//検索条件を追加後、結果データを表示
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));		

		$disp_data = [
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];

		return view('admin.client.status_list', $disp_data); 
	}
	
	/*
	 * クライアント-ステータス変更画面-ポイント付与処理
	 */
	public function statusPointAdd(Request $request)
	{

		$this->validate($request, [
			'point'	 => 'required|check_add_point',
		]);
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		try{

			//トランザクション内でSQLを実行
			//自動でロールバックされる
			//デッドロック発生時にdeadrock_max_retry回数再試行
			//最大再試行回数を過ぎたら例外エラー
			DB::connection(Session::get('operation_select_db'))->transaction(function() use($user, $request){

				//動的クエリを生成するため
				$query = User::query();

				//SQL用のオブジェクトを生成し条件を取得
				$query = $this->_getSearchOptionData($query, config('const.search_exec_type_unexecuted_key'));

				//usersテーブルから更新対象のlogin_id,pointのリストを取得
				$listUpdateData = $query->get(['id','login_id', 'point']);

				foreach($listUpdateData as $lines){
					//リクエストのポイントを取得
					$add_point = $request->input('point');

					//ポイント計算
					$total_point = $lines->point + $add_point;

					//ポイント < 0ならpoint_logテーブル、usersテーブル、テキストログファイルのポイントを0にする
					if( $total_point <= 0 ){
						if( $lines->point > 0 ){
							$add_point = - $lines->point;
						}else{
							continue;
						}
					}

					//ポイント更新処理用の動的クエリを生成するため
					$query = User::query();

					//ポイント更新
					$query->where('id',$lines->id)->increment('point', $add_point);

					//更新後のポイント取得用の動的クエリを生成するため
					$query = User::query();

					//更新後のポイントを取得
					$db_data = $query->where('id',$lines->id)->get(['point'])->first();

					//ポイントログ履歴用の動的クエリを生成するため
					$log = new Point_log([
						'login_id'					=> $lines->login_id,
						'add_point'					=> $request->input('point'),
						'prev_point'				=> $lines->point,
						'current_point'				=> $db_data->point,
						'operator'					=> $user['login_id']
					]);

					//データをinsert
					$log->save();

					//ポイント加算
					if( preg_match("/^[0-9]+$/", $request->input('point')) > 0 ){			
						//ログ出力
						$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$lines->id},{$request->input('point')},{$lines->point},{$db_data->point},{$user['login_id']}");
						$this->log_history_obj->addLog(config('const.admin_display_list')['point_add'].",{$user['login_id']}");

					//ポイント減算
					}else{
						//ログ出力
						$this->log_obj->addLog(config('const.admin_display_list')['point_sub'].",{$lines->id},{$request->input('point')},{$lines->point},{$db_data->point},{$user['login_id']}");
						$this->log_history_obj->addLog(config('const.admin_display_list')['point_sub'].",{$user['login_id']}");
					}
				}

			});

		} catch (\Exception $e) {
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['point_except_err'].",{$user['login_id']}");
			
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}
	
		return null;
	}

	/*
	 * 注文追加画面表示
	 */
	public function addOrder($id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//現在時刻取得
		$now_date = Carbon::now();
		$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date."00");

		//商品データ取得
		$db_data = Top_product::where('open_flg', 1)
			->where('sort_start_date', '<=', $sort_date)
			->where('sort_end_date', '>=', $sort_date)
			->orderBy('order_num', 'asc')
			->get();

		//倍率設定済の購入ポイント取得
		$query = Magnification_setting::query();
		$query->join('point_settings', 'magnification_settings.category_id', '=', 'point_settings.category_id');
		$query->where('magnification_settings.start_date','<=', $now_date);
		$query->where('magnification_settings.end_date', '>=', $now_date);
		$db_pt_data = $query->get();

		//倍率設定がされていなければ通常設定を取得
		if( count($db_pt_data) == 0 ){
			//magnification_settingsテーブルの通常設定IDを取得
			$db_pt_data = Magnification_setting::first();
			if( !empty($db_pt_data) ){
				//通常設定の購入ポイントを取得
				$query = Point_setting::query();
				$db_pt_data = $query->where('category_id', $db_pt_data->default_id)->get();
			}
		}

		//
		$disp_data = [
			'id'			=> $id,
			'db_data'		=> $db_data,
			'db_pt_data'	=> $db_pt_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.add_order', $disp_data); 
	}

	/*
	 * 注文追加処理
	 */
	public function addOrderSend(Request $request)
	{
/*
		//配列のエラーチェック
		$this->validate($request, [
			'product_id'	=> 'required',
			'add_point'		=> 'add_point'
		]);
*/
		//現在時刻取得
		$now_date = Carbon::now();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//クライアントID取得
		$client_id = $request->input('id');

		//商品ID取得
		$listOrderId = $request->input('product_id');

		try{
			//注文IDを生成
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into create_order_ids(order_id) select MAX(order_id) + 1 from create_order_ids on duplicate key update order_id = order_id + 1;");

			//注文IDを取得
			$db_data = Create_order_id::first();

			$order_id = $db_data->order_id;

			//トランザクション内でSQLを実行
			//自動でロールバックされる
			//デッドロック発生時にdeadrock_max_retry回数再試行
			//最大再試行回数を過ぎたら例外エラー
//			DB::connection(Session::get('operation_select_db'))->transaction(function() use($client_id){
				//pay_count更新
//				User::query()->where('id', $client_id)->increment('pay_count', 1);
//			});

			//追加商品があれば
			if( count($listOrderId) > 0 ){

				//チェックした商品リストから商品IDを１つ取り出す
				foreach($listOrderId as $index => $line){
					DB::connection(Session::get('operation_select_db'))->transaction(function() use($user, $client_id, $line, $now_date, $order_id, $request){
						//商品ID、追加ポイント、金額
						list($product_id, $add_point, $money) = explode("_", $line);

						//usersテーブルから更新対象のlogin_id,pointを取得
						$db_data = User::query()->where('id', $client_id)->first(['login_id','point','pay_count']);
						$prev_point = $db_data->point;

						//購入履歴に追加
						$payment_log = new Payment_log([
							'pay_type'			=> 0,											//pay_type:0⇒管理手動
							'login_id'			=> $db_data->login_id,
							'type'				=> 0,											//キャンペーン:0 ポイント:1
							'product_id'		=> $product_id,
							'money'				=> $money,										//money:0⇒管理手動
							'point'				=> $add_point,
							'status'			=> config('const.settlement_result')[1],		//status:0⇒管理手動
							'pay_count'			=> $db_data->pay_count,
							'order_id'			=> $order_id,
							'regist_date'		=> $now_date,
							'sort_date'			=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $now_date)
						]);

						//データをinsert
						$payment_log->save();

						//ユーザーの入金合計を更新
//						User::query()->where('id', $client_id)->increment('pay_amount', $money);

						//追加ポイント>0のとき
						if( $add_point > 0 ){
/*
							//ポイント更新
							User::query()->where('id', $client_id)->increment('point', $add_point);

							//更新後のポイントを取得
							$db_data = User::query()->where('id', $client_id)->first(['login_id','point']);

							//ポイントログ履歴用の動的クエリを生成するため
							$log = new Point_log([
								'login_id'					=> $db_data->login_id,
								'add_point'					=> $add_point,
								'prev_point'				=> $prev_point,
								'current_point'				=> $db_data->point,
								'operator'					=> $user['login_id']
							]);

							//データをinsert
							$log->save();

							//ログ出力
							$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$client_id},{$add_point},{$prev_point},{$db_data->point},{$user['login_id']}");
 */
						}
					});
				}
/*
				//最終入金日付を更新
				$update	 = User::where('id', $client_id)->update([
					'pay_datetime' => $now_date, 
					'sort_pay_datetime' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6", $now_date).'00'
				]);
*/
				//ログ出力
				$this->log_history_obj->addLog(config('const.admin_display_list')['client_order_add'].",{$user['login_id']}");
			}

			if( !empty($request->input('add_point')) ){
				DB::connection(Session::get('operation_select_db'))->transaction(function() use($user, $now_date, $request, $order_id, $client_id){

					//商品ID、追加ポイント、金額
					list($product_id, $add_point, $money) = explode("_", $request->input('add_point'));

					//usersテーブルから更新対象のlogin_id,pointを取得
					$db_data = User::query()->where('id', $client_id)->first(['login_id','point','pay_count']);
					$prev_point = $db_data->point;

					//購入履歴に追加
					$payment_log = new Payment_log([
						'pay_type'			=> 0,											//pay_type:0⇒管理手動
						'login_id'			=> $db_data->login_id,
						'type'				=> 1,											//キャンペーン:0 ポイント:1
						'product_id'		=> $product_id,
						'money'				=> $money,										//money:0⇒管理手動
						'point'				=> $add_point,
						'status'			=> config('const.settlement_result')[1],		//status:0⇒管理手動
						'pay_count'			=> $db_data->pay_count,
						'order_id'			=> $order_id,
						'regist_date'		=> $now_date,
						'sort_date'			=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $now_date)
					]);

					//データをinsert
					$payment_log->save();

					//ユーザーの入金合計を更新
//					User::query()->where('id', $client_id)->increment('pay_amount', $money);

					//追加ポイント>0のとき
					if( $add_point > 0 ){
/*
						//ポイント更新
						User::query()->where('id', $client_id)->increment('point', $add_point);

						//更新後のポイントを取得
						$db_data = User::query()->where('id', $client_id)->first(['login_id','point']);

						//ポイントログ履歴用の動的クエリを生成するため
						$log = new Point_log([
							'login_id'					=> $db_data->login_id,
							'add_point'					=> $add_point,
							'prev_point'				=> $prev_point,
							'current_point'				=> $db_data->point,
							'operator'					=> $user['login_id']
						]);

						//データをinsert
						$log->save();

						//ログ出力
						$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$client_id},{$add_point},{$prev_point},{$db_data->point},{$user['login_id']}");
 */
					}
				});
/*
				//最終入金日付を更新
				$update	 = User::where('id', $client_id)->update([
					'pay_datetime' => $now_date, 
					'sort_pay_datetime' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6", $now_date).'00'
				]);
*/
				//ログ出力
				$this->log_history_obj->addLog(config('const.admin_display_list')['client_order_point_add'].",{$user['login_id']}");
			}

		} catch (\Exception $e) {
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['point_except_err'].",{$user['login_id']}");

			//ログ出力
			$this->log_history_obj->addLog(config('const.admin_display_list')['client_order_add_err'].",{$user['login_id']}");

			//編集画面側の更新ボタン押下後の$.ajax通信結果をerrorのレスポンスで返す
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		return null; 
	}

	/*
	 * 注文履歴画面表示
	 */
	public function historyOrder($id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//現在時刻取得
		$now_date = Carbon::now();

		//延べ決済回数・入金済合計金額を取得（管理手動、入金済のとき）
		$db_settlement_data = DB::connection(Session::get('operation_select_db'))->table('payment_logs')
				->join('users', 'payment_logs.login_id', '=', 'users.login_id')
				->leftJoin('top_products', 'top_products.id', '=', 'payment_logs.product_id')
				->select(DB::connection(Session::get('operation_select_db'))->raw('sum(payment_logs.money) as money,count(distinct payment_logs.order_id) as num'))
				->where('users.id', $id)->whereIn('payment_logs.status', [0,3])->first();

		//延べ予約回数・全予約合計金額を取得(管理手動、入金待ち、入金エラー、入金済み、返金、注文未完了のとき)
		$db_reserv_data = DB::connection(Session::get('operation_select_db'))->table('payment_logs')
				->join('users', 'payment_logs.login_id', '=', 'users.login_id')
				->leftJoin('top_products', 'top_products.id', '=', 'payment_logs.product_id')
				->select(DB::connection(Session::get('operation_select_db'))->raw('sum(payment_logs.money) as money,count(distinct payment_logs.order_id) as num'))
				->where('users.id', $id)->whereIn('payment_logs.status', [0,1,2,6,3,4,5])->first();

		//購入履歴データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->table('payment_logs')
				->join('users', 'payment_logs.login_id', '=', 'users.login_id')
				->leftJoin('top_products', 'top_products.id', '=', 'payment_logs.product_id')
				->select('top_products.title as title','payment_logs.money as product_money','payment_logs.*')
				->where('users.id', $id)->orderBy('payment_logs.order_id','desc')->paginate(config('const.admin_client_list_limit'));

		$list_data			 = [];
		$add_point			 = [];
		$total_amount		 = [];
		$settlement			 = ['num' => 0, 'total' => 0];
		$reserv				 = ['num' => 0, 'total' => 0];
		if( !empty($db_data) ){
			foreach($db_data as $lines){
				//同じ商品の合計用変数を初期化
				if( empty($total_amount[$lines->order_id]['total']) ){
					$total_amount[$lines->order_id]['total'] = 0;
				}

				if( empty($add_point[$lines->order_id]['add_point']) ){
					$add_point[$lines->order_id]['add_point'] = 0;
				}

				$add_point[$lines->order_id]['add_point'] += $lines->point;

				//合計を計算(手動追加、入金待ち、入金エラー、入金済み、返金、注文未完了のとき)
				if( in_array($lines->status,[0,1,2,6,3,4,5]) ){
					//同じ商品購入時の合計を計算
					$total_amount[$lines->order_id]['total'] += $lines->product_money;
				}

				//決済回数の計算(入金済のとき)
				if( !isset($settlement[$lines->order_id]) && in_array($lines->status, [0,3]) ){
					$settlement[$lines->order_id] = 1;
					$settlement['num'] += 1;
				}

				//予約回数の計算(手動追加、入金待ち、入金エラー、入金済み、返金、注文未完了のとき)
				if( !isset($reserv[$lines->order_id]) && in_array($lines->status, [0,1,2,6,3,4,5]) ){
					$reserv[$lines->order_id] = 1;
					$reserv['num'] += 1;
				}

				//入金済合計金額の計算(入金済のとき)
				if( in_array($lines->status, [0,3]) ){
					$settlement['total'] += $lines->product_money;
				}

				//全予約合計金額の計算(手動追加、入金待ち、入金エラー、入金済み、返金、注文未完了のとき)
				if( in_array($lines->status, [0,1,2,6,3,4,5]) ){
					$reserv['total'] += $lines->product_money;
				}

				//商品データ格納
				$list_data[$lines->order_id][] = $lines;
			}
		}

		//全件数取得
		$total = $db_data->total();

		//
		$disp_data = [
			'product_url'			=> config('const.base_admin_url').'/'.config('const.product_url_path').'/1/',
			'settlement'			=> $settlement,
			'reserv'				=> $reserv,
			'total_settlement_num'	=> $db_settlement_data->num,
			'total_settlement'		=> $db_settlement_data->money,
			'total_reserv_num'		=> $db_reserv_data->num,
			'total_reserv'			=> $db_reserv_data->money,
			'id'			=> $id,
			'total_amount'	=> $total_amount,
			'add_point'		=> $add_point,
			'db_data'		=> $list_data,
			'total'			=> $total,
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time(),
		];

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_order_history'].",{$user['login_id']}");

		return view('admin.client.history_order', $disp_data); 
	}

	/*
	 * 注文詳細画面表示
	 */
	public function historyOrderDetail($client_id, $order_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//注文データ取得
		$db_data = DB::connection(Session::get('operation_select_db'))->table('payment_logs')
				->join('users', 'payment_logs.login_id', '=', 'users.login_id')
				->leftJoin('top_products', 'top_products.id', '=', 'payment_logs.product_id')
				->leftJoin('point_settings', 'point_settings.id', '=', 'payment_logs.product_id')
				->select('users.mail_address as mail_address','users.credit_certify_phone_no as credit_certify_phone_no','users.description as description','top_products.id as product_id','top_products.title as title','payment_logs.point as point','payment_logs.money as product_money','payment_logs.*')
				->where('users.id', $client_id)->where('payment_logs.order_id', $order_id)->get();

		$total_amount	 = ['total' => 0];
		$list_data		 = [];
		foreach($db_data as $index => $lines){
			//合計を計算(入金待ち、入金エラー、入金済み、返金、注文未完了のとき)
			if( in_array($lines->status,[0,1,2,6,3,4,5]) ){
				//同じ商品購入時の合計を計算
				$total_amount['total'] += $lines->product_money;
			}

			//一度に複数の商品を購入したときの１番最初の商品
			if( $index == 0 ){
				$list_data = [
					'product_id'	=> $lines->product_id,
					'order_id'		=> $lines->order_id,
					'regist_date'	=> $lines->regist_date,
					'ad_cd'			=> $lines->ad_cd,
					'email'			=> $lines->mail_address,
					'pay_type'		=> $lines->pay_type,
					'tel'			=> $lines->credit_certify_phone_no,
					'status'		=> $lines->status,
					'point'			=> $lines->point,
					'money'			=> $lines->product_money,
					'description'	=> $lines->description
				];
				//複数ある商品のタイトルと商品金額
				$list_data['order_detail'][] = ['title' => $lines->title,'money' => $lines->product_money,'product_id'	=> $lines->product_id];

			//一度に複数の商品を購入したときの２番目以降の商品
			}else{
				//複数ある商品のタイトルと商品金額
				$list_data['order_detail'][] = ['title' => $lines->title,'money' => $lines->product_money,'product_id'	=> $lines->product_id];

				//複数ある商品の合計ポイント
				$list_data['point'] += $lines->point;

				//複数ある商品の合計金額
				$list_data['money'] += $lines->product_money;
			}
		}

		//
		$disp_data = [
			'product_url'	=> config('const.base_admin_url').'/'.config('const.product_url_path').'/1/',
			'client_id'		=> $client_id,
			'order_id'		=> $order_id,
			'total_amount'	=> $total_amount,
			'db_data'		=> $list_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.history_order_detail', $disp_data); 
	}

	/*
	 * 注文詳細画面の更新処理
	 */
	public function updateHistoryOrderDetail(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//現在時刻取得
		$now_date = Carbon::now();

		//更新データ(ステータス)
		$update_val = [
			'status' => $request->input('status'),
			'sort_date' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $now_date)
		];

		//変更前のステータスが入金済・入金済(管理手動)以外かつ変更後のステータスが入金済・入金済(管理手動)のとき
		if( !in_array($request->input('old_status'), [config('const.settlement_result')[0], config('const.settlement_result')[3]]) && 
			in_array($request->input('status'), [config('const.settlement_result')[0], config('const.settlement_result')[3]]) ){
			//usersテーブルの購入回数を１加算
			User::query()->where('id', $request->input('client_id'))->increment('pay_count', 1);

			//購入回数を取得
			$client_db = User::where('id', $request->input('client_id'))->first();

			//入金合計を更新
			$update = User::query()->where('id', $request->input('client_id'))->increment('pay_amount', $request->input('pay_amount'));

			//付加ポイント>0なら
			if( $request->input('add_point') > 0 ){
				//ポイント更新
				$update = User::query()->where('id', $request->input('client_id'))->increment('point', $request->input('add_point'));

				//更新後のポイントを取得
				$db_data = User::query()->where('id', $request->input('client_id'))->first(['login_id','point']);

				//ポイントログ履歴用の動的クエリを生成するため
				$log = new Point_log([
					'login_id'					=> $db_data->login_id,
					'add_point'					=> $request->input('add_point'),
					'prev_point'				=> $client_db->point,
					'current_point'				=> $db_data->point,
					'operator'					=> $user['login_id']
				]);

				//データをinsert
				$log->save();
			}

			$update_val['pay_count'] = $client_db->pay_count;

			//最終入金日付を更新
			$last_pay = User::where('id', $request->input('client_id'))->update([
				'pay_datetime' => $now_date, 
				'sort_pay_datetime' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6", $now_date).'00'
			]);

			//自動メールのデータ取得
			$db_cnt = Mail_content::where('id', 11)->first();

			//データがあれば
			if( !empty($db_cnt) ){
				//変換後の文字列を取得
				list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($db_cnt->body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

				//変換後の文字列を取得
				$body = Utility::getConvertData($body);
				$body = preg_replace("/\-%usermail\-/", $client_db->mail_address, $body);
				$body = preg_replace("/\-%transfer_amount\-/", $request->input('pay_amount'), $body);
				$body = preg_replace("/\-%order_date\-/", $now_date, $body);
				$body = preg_replace("/\-%order_id\-/", $request->input('order_id'), $body);
				$body = preg_replace("/\-%login_id\-/", $client_db->login_id, $body);
				$body = preg_replace("/\-%password\-/", $client_db->password_raw, $body);
				$body = preg_replace("/\-%token\-/", $client_db->remember_token, $body);
				$body = preg_replace("/\-%accessKey\-/", $client_db->remember_token, $body);

				list($host_ip, $port) = Utility::getSmtpHost('setting');

				//送信元情報設定
				$options = [
					'client_id'	 => $client_db->client_id,
					'host_ip'	 => $host_ip,
					'port'		 => $port,
					'from'		 => $from_mail,
					'from_name'	 => $from_name,
					'subject'	 => $subject,
					'template'	 => Session::get('operation_select_db').'.'.config('const.payment_comp'),
				];

				//送信データ設定
				$data = [
					'contents'		=> $body,
				];

				//メールアドレス変更先へメール送信
				Mail::to($client_db->mail_address)->send( new SendMail($options, $data) );
			}
		}

		//変更前のステータスが入金済・入金済(管理手動)かつ変更後のステータスが入金済・入金済(管理手動)以外のとき
		if( in_array($request->input('old_status'), [config('const.settlement_result')[0], config('const.settlement_result')[3]]) && 
			!in_array($request->input('status'), [config('const.settlement_result')[0], config('const.settlement_result')[3]]) ){

			//usersテーブルの購入回数を１加算
			User::query()->where('id', $request->input('client_id'))->increment('pay_count', -1);

			//購入回数を取得
			$client_db = User::where('id', $request->input('client_id'))->first();

			//入金合計を更新
			$update = User::query()->where('id', $request->input('client_id'))->increment('pay_amount', '-'.$request->input('pay_amount'));

			//付加ポイント>0なら
			if( $request->input('add_point') > 0 ){
				//ポイント更新
				$update = User::query()->where('id', $request->input('client_id'))->increment('point', '-'.$request->input('add_point'));

				//更新後のポイントを取得
				$db_data = User::query()->where('id', $request->input('client_id'))->first(['login_id','point']);

				//ポイントログ履歴用の動的クエリを生成するため
				$log = new Point_log([
					'login_id'					=> $db_data->login_id,
					'add_point'					=> $request->input('add_point'),
					'prev_point'				=> $client_db->point,
					'current_point'				=> $db_data->point,
					'operator'					=> $user['login_id']
				]);

				//データをinsert
				$log->save();
			}

			$update_val['pay_count'] = $client_db->pay_count;
		}

		$update = Payment_log::where('order_id', $request->input('order_id'))->update($update_val);

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['history_status_change'].":".config('const.list_pay_status')[$request->input('status')].",{$user['login_id']}");

		return null;
	}

	/*
	 * メルマガ履歴画面表示
	 */
	public function historyMelmaga($client_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//メルマガ履歴取得
		$db_data = DB::connection(Session::get('operation_select_db'))->table('melmaga_history_logs')
				->join('melmaga_logs', 'melmaga_history_logs.melmaga_id', '=', 'melmaga_logs.id')
				->select('melmaga_history_logs.melmaga_id','melmaga_history_logs.client_id','melmaga_history_logs.updated_at','melmaga_history_logs.first_view_datetime','melmaga_history_logs.created_at','melmaga_logs.subject')
				->where('melmaga_history_logs.client_id', $client_id)->orderBy('melmaga_history_logs.melmaga_id', 'desc')->paginate(config('const.admin_client_list_limit'));

		//
		$disp_data = [
			'id'			=> $client_id,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'db_data'		=> $db_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.history_melmaga', $disp_data); 
	}

	/*
	 * ポイント履歴画面表示
	 */
	public function historyPoint($login_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ポイント履歴取得
		$db_data = DB::connection(Session::get('operation_select_db'))->table('point_logs')->where('login_id', $login_id)->orderBy('id', 'desc')->paginate(config('const.admin_client_list_limit'));

		//
		$disp_data = [
			'id'			=> $login_id,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'db_data'		=> $db_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.history_point', $disp_data); 
	}

	/*
	 * アクセス履歴画面表示
	 */
	public function accessHistory($login_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//アクセス履歴取得
		$db_data = DB::connection(Session::get('operation_select_db'))->table('personal_access_logs')->where('login_id', $login_id)->orderBy('created_at', 'desc')->paginate(config('const.admin_client_list_limit'));

		//
		$disp_data = [
			'id'			=> $login_id,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'db_data'		=> $db_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.history_personal', $disp_data); 
	}
}
