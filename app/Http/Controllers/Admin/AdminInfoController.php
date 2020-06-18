<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\User;
use App\Model\Contact;
use App\Model\Group;
use App\Model\Personal_mail_log;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use Mail;
use App\Mail\SendMail;

class AdminInfoController extends Controller
{
	private $log_obj;
	private $log_history_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_history_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * infoのトップ画面表示
	 */
	public function index()
	{
		$db_data = Contact::orderBy('created_at', 'desc')->orderBy('reply_date', 'desc')->paginate(config('const.disp_achievements_limit'));

		$groups = Group::get();
		if( !empty($groups) ){
			$listGroups = [];
			foreach($groups as $lines){
				$listGroups[$lines->id] = $lines->name;
			}
		}

		$status = [];
		$listStatus = config('const.regist_status');
		foreach($listStatus as $lines){
			$status[$lines[0]] = $lines[1];
		}

		//画面表示用配列
		$disp_data = [
			'status'		=> $status,
			'groups'		=> $listGroups,
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];

		return view('admin.info.index', $disp_data);
	}

	/*
	 * info画面から削除処理
	 */
	public function delete(Request $request)
	{	
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		//削除IDがあれば
		if( !empty($listDelId) ){
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['info_mail_delete'].",{$user['login_id']}");	

			//削除処理
			foreach($listId as $index => $id){
				//$listDelIdが配列かつ削除IDがあれば
				if( is_array($listDelId) && in_array($id, $listDelId) ){
					//テーブルからデータ削除
					Contact::where('id', $id)->delete();
				}
			}
		}

		return null;
	}

	/*
	 * 個別メール画面
	 */
	public function replayMail($page, $id)
	{	
		//usersテーブルからデータ取得
		$db_data = Contact::where('id', $id)->first();

		if( !empty($db_data->msg) ){
			$db_data->msg = preg_replace("/\n/", "\n>", $db_data->msg);
			$db_data->msg = "\n\n\n\n>".$db_data->msg;
		}

		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.info.replay_mail', $disp_data); 
	}

	/*
	 * 個別メール画面から返信処理
	 */
	public function replayMailSend(Request $request)
	{
		//エラーチェック
		$this->validate($request, [
			'from_name'	=> 'bail|required|surrogate_pair_check|emoji_check',
//			'from_mail'	=> 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'to_mail'	=> 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'subject'	=> 'bail|required|max:'.config('const.subject_length').'|surrogate_pair_check|emoji_check',
			'body'		=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		list($host_ip, $port) = Utility::getSmtpHost('personal');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//送信元情報設定
		$options = [
			'client_id'	 => $request->input('client_id'),
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'from'		 => $request->input('from_mail'),
			'from_name'	 => $request->input('from_name'),
			'subject'	 => $request->input('subject'),
			'template'	 => $user['select_db'].'.'.config('const.admin_edit_mail'),
		];

		//送信データ設定
		$data = [
			'contents'		=> $request->input('body'),
		];

		$err_flg = Utility::checkNgWordEmail($request->input('to_mail'), Session::get('operation_select_db'));

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}

		//%変換設定で設定した文字列が含まれていれば変換を行う
		$options['subject'] = Utility::getConvertData($options['subject']);
		$data['contents'] = Utility::getConvertData($data['contents']);

		$db_data = User::where('id', $request->input('client_id'))->first();
		if( !empty($db_data) ){
			$data['contents'] = preg_replace("/\-%login_id\-/", $db_data->login_id, $data['contents']);
			$data['contents'] = preg_replace("/\-%password\-/", $db_data->password_raw, $data['contents']);
			$data['contents'] = preg_replace("/\-%token\-/", $db_data->remember_token, $data['contents']);
		}

		//メールアドレス先へメール送信
		Mail::to($request->input('to_mail'))->send( new SendMail($options, $data) );

		//個別メール送信履歴テーブル(personal_mail_logs)に個別メールの送信情報をinsert
		$mail_log = new Personal_mail_log([
			'client_id'	=> $request->input('client_id'),
			'subject'	=> $options['subject'],
			'body'		=> $data['contents'],
		]);

		//データをinsert
		$mail_log->save();

		//現在時刻
		$now_date = Carbon::now();
/*
		//返信ログを追加
		$contact = new Contact([
			'client_id'		=> $request->input('client_id'),
			'email'			=> $request->input('from_name'),
			'reply_date'	=> $now_date,
			'group_id'		=> $request->input('group_id'),
			'status'		=> $request->input('status'),
			'subject'		=> $request->input('subject'),
			'msg'			=> $request->input('body'),
			'memo'			=> $request->input('memo'),
		]);
		
		//DB保存
		$contact->save();
*/
		$save_data = [
			'client_id'		=> $request->input('client_id'),
			'email'			=> $request->input('to_mail'),
			'reply_date'	=> $now_date,
			'subject'		=> $request->input('subject'),
			'msg'			=> $request->input('body'),
			'memo'			=> $request->input('memo'),
			'created_at'	=> null,
		];

		if( !empty($request->input('group_id')) ){
			$save_data['group_id'] = $request->input('group_id');
		}

		if( !empty($request->input('status')) ){
			$save_data['status'] = $request->input('status');
		}

		$contact = new Contact($save_data);

		$contact->save();

		//返信日時を更新
		$update = Contact::where('id', $request->input('id'))->update(['reply_date' => $now_date]);

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['info_mail_reply'].",{$user['login_id']}");

		return null;
	}

	//クライアント検索画面
	public function search(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//動的クエリを生成するため
		$query = Contact::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		$groups = Group::get();
		if( !empty($groups) ){
			$listGroups = [];
			foreach($groups as $lines){
				$listGroups[$lines->id] = $lines->name;
			}
		}

		$status = [];
		$listStatus = config('const.regist_status');
		foreach($listStatus as $lines){
			$status[$lines[0]] = $lines[1];
		}

		$disp_data = [
			'status'		=> $status,
			'groups'		=> $listGroups,
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];
		
		return view('admin.info.index', $disp_data);
	}
	
	public function searchSetting()
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
		
		return view('admin.info.info_search', $disp_data);
	}
	
	//クライアント検索処理
	public function searchPost(Request $request)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['info_mail_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
//		$query = Contact::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData(config('const.search_exec_type_data_key'));

		$groups = Group::get();
		if( !empty($groups) ){
			$listGroups = [];
			foreach($groups as $lines){
				$listGroups[$lines->id] = $lines->name;
			}
		}

		$status = [];
		$listStatus = config('const.regist_status');
		foreach($listStatus as $lines){
			$status[$lines[0]] = $lines[1];
		}

		$disp_data = [
			'status'		=> $status,
			'groups'		=> $listGroups,
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];
		
		return view('admin.info.index', $disp_data);
	}
	
	/*
	 * 
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('info_search_item', $request->input('search_item'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('info_search_item');
		}
		
		//LIKE検索
		if( !is_null($request->input('search_like_type')) ){
			Session::put('info_search_like_type', $request->input('search_like_type'));
		}

		//受信日時-開始
		if( !empty($request->input('start_receive_date')) ){
			Session::put('info_start_receive_date', $request->input('start_receive_date'));
		}else{
			//未入力なら破棄
			Session::forget('info_start_receive_date');
		}

		//受信日時-終了
		if( !empty($request->input('end_receive_date')) ){
			Session::put('info_end_receive_date', $request->input('end_receive_date'));
		}else{
			//未入力なら破棄
			Session::forget('info_end_receive_date');
		}

		//返信日時-開始
		if( !empty($request->input('start_reply_date')) ){
			Session::put('info_start_reply_date', $request->input('start_reply_date'));
		}else{
			//未入力なら破棄
			Session::forget('info_start_reply_date');
		}

		//返信日時-終了
		if( !empty($request->input('end_reply_date')) ){
			Session::put('info_end_reply_date', $request->input('end_reply_date'));
		}else{
			//未入力なら破棄
			Session::forget('info_end_reply_date');
		}

		//返信ありなし
		if( !empty($request->input('reply_flg')) ){
			Session::put('info_reply_flg', $request->input('reply_flg'));
		}else{
			//未入力なら破棄
			Session::forget('info_reply_flg');
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('info_search_disp_num', $request->input('search_disp_num'));
		}
	}
	
	/*
	 * usersテーブルの検索条件を保存されたSessionから設定
	 */
	private function _getSearchOptionData($exec_type = '')
	{
		//動的クエリを生成するため
		$query = Contact::query();

		//顧客ID
		if( !is_null(Session::get('info_search_item')) ){
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listSearchLikeType = config('const.search_like_type');
				$listUserId = explode(",", Session::get('info_search_item'));
				foreach($listUserId as $index => $user_id){
					$query->orWhere('client_id', $listSearchLikeType[Session::get('info_search_like_type')][0], sprintf($listSearchLikeType[Session::get('info_search_like_type')][1], $user_id ));
				}
			});
		}

		//受信日時-開始
		if( !empty(Session::get('info_start_receive_date')) ){
			$query->where('created_at', '>=', Session::get('info_start_receive_date'));
		}

		//受信日時-終了
		if( !empty(Session::get('info_end_receive_date')) ){
			$query->where('created_at', '<=', Session::get('info_end_receive_date'));
		}

		//返信日時-開始
		if( !empty(Session::get('info_start_reply_date')) ){
			$query->where('reply_date', '>=', Session::get('info_start_reply_date'));
		}

		//返信日時-終了
		if( !empty(Session::get('info_end_reply_date')) ){
			$query->where('reply_date', '<=', Session::get('info_end_reply_date'));
		}

		//返信あり
		if( !empty(Session::get('info_reply_flg')) ){
			$query->whereNotNull('reply_date');
		}else{
			//返信なし
			if( empty(Session::get('info_start_reply_date')) && empty(Session::get('info_end_reply_date')) ){
				$query->whereNull('reply_date');			
			}
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null(Session::get('info_search_disp_num')) ){
			$list_disp_limit = config('const.search_disp_num');
			$disp_limit = $list_disp_limit[Session::get('info_search_disp_num')];
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
			
		return $db_data;
	}

}
