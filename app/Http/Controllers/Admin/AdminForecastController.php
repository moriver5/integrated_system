<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Forecast;
use App\Model\Group;
use App\Model\User;
use App\Model\Top_content;
use App\Model\Top_product;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;

class AdminForecastController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 予想一覧画面表示
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Forecast::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();
		$list_data = [];
		foreach($db_data as $lines){
			//グループ
			if( !empty(Session::get('forecast_groups')) ){
				//検索するグループの中にDBデータのグループが含まれているか
				$listResult = array_intersect(explode(",", Session::get('forecast_groups')), explode(",", $lines->groups));
				if( empty($listResult) ){
					//含まれていなければ検索件数から１減らす
					$total--;
					continue;
				}
			}

			//キャンペーン
			if( !empty(Session::get('forecast_campaigns')) ){
				//検索するキャンペーンの中にDBデータのキャンペーンが含まれているか
				$listResult = array_intersect(explode(",", Session::get('forecast_campaigns')), explode(",", $lines->campaigns));
				if( empty($listResult) ){
					//含まれていなければ検索件数から１減らす
					$total--;
					continue;
				}
			}

			//秒を削除
			$lines->disp_sdate = preg_replace("/(:00)$/", "", $lines->disp_sdate);
			$lines->disp_edate = preg_replace("/(:00)$/", "", $lines->disp_edate);
			$lines->open_sdate = preg_replace("/(:00)$/", "", $lines->open_sdate);
			$lines->open_edate = preg_replace("/(:00)$/", "", $lines->open_edate);

			$list_data[] = $lines;
		}

		//画面表示用配列
		$disp_data = [
			'forecast_category'	=> config('const.forecast_category'),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.forecast.index', $disp_data);
	}

	/*
	 * 検索画面表示
	 */
	public function searchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//画面表示用配列
		$disp_data = [
			'db_group_data'				=> $db_group_data,
			'session'					=> Session::all(),
			'ver'						=> time(),
			'forecast_search_item'		=> config('const.forecast_search_item'),
			'forecast_search_category'	=> config('const.forecast_search_category'),
			'forecast_disp_type'		=> config('const.forecast_disp_type'),
			'search_disp_num'			=> config('const.search_disp_num'),
			'sort_list'					=> config('const.forecast_sort_list'),
		];

		return view('admin.forecast.forecast_search', $disp_data);
	}

	/*
	 * 検索処理
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Forecast::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();
		$list_data = [];
		foreach($db_data as $lines){
			//グループ
			if( !empty(Session::get('forecast_groups')) ){
				//検索するグループの中にDBデータのグループが含まれているか
				$listResult = array_intersect(explode(",", Session::get('forecast_groups')), explode(",", $lines->groups));
				if( empty($listResult) ){
					//含まれていなければ検索件数から１減らす
					$total--;
					continue;
				}
			}

			//キャンペーン
			if( !empty(Session::get('forecast_campaigns')) ){
				//検索するキャンペーンの中にDBデータのキャンペーンが含まれているか
				$listResult = array_intersect(explode(",", Session::get('forecast_campaigns')), explode(",", $lines->campaigns));
				if( empty($listResult) ){
					//含まれていなければ検索件数から１減らす
					$total--;
					continue;
				}
			}

			//秒を削除
			$lines->disp_sdate = preg_replace("/(:00)$/", "", $lines->disp_sdate);
			$lines->disp_edate = preg_replace("/(:00)$/", "", $lines->disp_edate);
			$lines->open_sdate = preg_replace("/(:00)$/", "", $lines->open_sdate);
			$lines->open_edate = preg_replace("/(:00)$/", "", $lines->open_edate);

			$list_data[] = $lines;
		}

		//
		$disp_data = [
			'forecast_category'	=> config('const.forecast_category'),
			'session'			=> Session::all(),
			'db_data'			=> $list_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.forecast.index', $disp_data);
	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['forecasts_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Forecast::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();
		$list_data = [];
		foreach($db_data as $lines){
			//グループ
			if( !empty(Session::get('forecast_groups')) ){
				$listResult = array_intersect(explode(",", Session::get('forecast_groups')), explode(",", $lines->groups));
				if( empty($listResult) ){
					$total--;
					continue;
				}
			}

			//キャンペーン
			if( !empty(Session::get('forecast_campaigns')) ){
				$listResult = array_intersect(explode(",", Session::get('forecast_campaigns')), explode(",", $lines->campaigns));
				if( empty($listResult) ){
					$total--;
					continue;
				}
			}

			//秒を削除
			$lines->disp_sdate = preg_replace("/(:00)$/", "", $lines->disp_sdate);
			$lines->disp_edate = preg_replace("/(:00)$/", "", $lines->disp_edate);
			$lines->open_sdate = preg_replace("/(:00)$/", "", $lines->open_sdate);
			$lines->open_edate = preg_replace("/(:00)$/", "", $lines->open_edate);

			$list_data[] = $lines;
		}

		$disp_data = [
			'forecast_category'	=> config('const.forecast_category'),
			'session'			=> Session::all(),
			'db_data'			=> $list_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.forecast.index', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{

		//検索項目
		if( !is_null(Session::get('forecast_search_item_value')) ){
			if( Session::get('forecast_search_item') == config('const.forecast_search_item')[0][0] ){
				$query->where(Session::get('forecast_search_item'), '=', Session::get('forecast_search_item_value'));
			}else{
				$query->where(Session::get('forecast_search_item'), 'like', "%".Session::get('forecast_search_item_value')."%");
			}
		}

		//カテゴリ
		if( !empty(Session::get('forecast_category')) ){
			$listCategoryType = config('const.forecast_search_category');
			$query->where('category', $listCategoryType[Session::get('forecast_category')][0]);
		}

		//表示日時-開始日
		if( !is_null(Session::get('forecast_disp_sdate')) ){
			$query->where('disp_sdate', '>=', Session::get('forecast_disp_sdate'));
		}

		//表示日時-終了日
		if( !is_null(Session::get('forecast_disp_edate')) ){
			$query->where('disp_edate', '<=', Session::get('forecast_disp_edate'));
		}

		//公開日時-開始日
		if( !is_null(Session::get('forecast_open_sdate')) ){
			$query->where('open_sdate', '>=', Session::get('forecast_open_sdate'));
		}

		//公開日時-終了日
		if( !is_null(Session::get('forecast_open_edate')) ){
			$query->where('open_edate', '<=', Session::get('forecast_open_edate'));
		}

		//公開
		if( !empty(Session::get('forecast_disp_type')) ){
			$listPageType = config('const.forecast_disp_type');
			$query->where('open_flg', $listPageType[Session::get('forecast_disp_type')][0]);
		}

		if( !empty(Session::get('forecast_title')) ){
			$query->where('title', Session::get('forecast_title'));
		}

		if( !empty(Session::get('forecast_comment')) ){
			$query->where('comment', Session::get('forecast_comment'));
		}

		if( !empty(Session::get('forecast_detail')) ){
			$query->where('detail', Session::get('forecast_detail'));
		}

		//ソート
		$sort_item = "id";
		$sort_type = "desc";
		if( !is_null(Session::get('forecast_sort')) ){
			$listSortType = config('const.forecast_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('forecast_sort')][0]);
			$query->orderBy($sort_item, $sort_type);
		}else{
			$query->orderBy($sort_item, $sort_type);
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null(Session::get('forecast_search_disp_num')) ){
			$list_disp_limit = config('const.search_disp_num');
			$disp_limit = $list_disp_limit[Session::get('forecast_search_disp_num')];
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
		if( !is_null($request->input('search_item')) ){
			Session::put('forecast_search_item', $request->input('search_item'));
		}

		//検索項目の値
		Session::put('forecast_search_item_value', $request->input('search_item_value'));

		//カテゴリ
		Session::put('forecast_category', $request->input('category'));

		//表示グループ
		Session::put('forecast_groups', $request->input('groups'));

		//キャンペーンID
		Session::put('forecast_campaigns', $request->input('campaigns'));

		//公開
		Session::put('forecast_disp_type', $request->input('disp_type'));

		//表示日時-開始日
		Session::put('forecast_disp_sdate', $request->input('disp_sdate'));

		//表示日時-終了日
		Session::put('forecast_disp_edate', $request->input('disp_edate'));

		//公開日時-開始日
		Session::put('forecast_open_sdate', $request->input('open_sdate'));

		//公開日時-終了日
		Session::put('forecast_open_edate', $request->input('open_edate'));

		//タイトル
		Session::put('forecast_title', $request->input('title'));

		//コメント
		Session::put('forecast_comment', $request->input('comment'));

		//内容
		Session::put('forecast_detail', $request->input('detail'));

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('forecast_sort', $request->input('sort'));
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('forecast_search_disp_num', $request->input('search_disp_num'));
		}
	}

	/*
	 * 予想の新規作成画面
	 */
	public function create()
	{
		$db_data = [];

		//商品取得
		$db_product_data = Top_product::where('open_flg', 1)->get();

		//TOPコンテンツのデータ取得
		$db_campaign_data = Top_content::where('type', 1)->where('open_flg', 1)->get();

		//
		$disp_data = [
			'list_open_flg'		=> config('const.admin_open_type'),
			'forecast_category'	=> config('const.forecast_category'),
			'list_product_data'	=> $db_product_data,
			'list_campaign_data'=> $db_campaign_data,
			'ver'				=> time()
		];

		return view('admin.forecast.create', $disp_data); 
	}

	/*
	 * 予想の新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'disp_sdate'	=> 'required|date_format_check',
			'disp_edate'	=> 'required|date_format_check',
			'open_sdate'	=> 'required|date_format_check',
			'open_edate'	=> 'required|date_format_check',
			'groups'		=> 'only_num_check|exist_group_id_check',
			'campaigns'		=> 'only_num_check|exist_campaign_id_check',
			'point'			=> 'required|integer',
			'headline'		=> 'required|surrogate_pair_check|emoji_check',
			'title'			=> 'required|surrogate_pair_check|emoji_check|max:'.config('const.forecast_title_max_length'),
			'comment'		=> 'required|surrogate_pair_check|emoji_check|max:'.config('const.forecast_comment_max_length'),
			'detail'		=> 'required|surrogate_pair_check|emoji_check',
		]);

		$now_date = Carbon::now();

		$forecast = new Forecast([
			'disp_sdate'		=> $request->input('disp_sdate'),
			'disp_edate'		=> $request->input('disp_edate'),
			'open_sdate'		=> $request->input('open_sdate'),
			'open_edate'		=> $request->input('open_edate'),
			'category'			=> $request->input('category'),
			'groups'			=> $request->input('groups'),
			'product_id'		=> $request->input('product_id'),
			'campaigns'			=> $request->input('campaigns'),
			'open_flg'			=> $request->input('open_flg'),
			'point'				=> $request->input('point'),
			'title'				=> $request->input('title'),
			'headline'			=> $request->input('headline'),
			'comment'			=> $request->input('comment'),
			'detail'			=> $request->input('detail'),
			'disp_sort_sdate'	=> $request->input('disp_sdate'),
			'disp_sort_edate'	=> $request->input('disp_edate'),
			'open_sort_sdate'	=> $request->input('open_sdate'),
			'open_sort_edate'	=> $request->input('open_edate'),
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		]);

		//DB保存
		$forecast->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['forecasts_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 予想新規作成画面でプレビュー画面表示
	 */
	public function createPreview()
	{
		//画面表示用配列
		$disp_data = [
			'ver'				=> time()
		];
		return view('admin.forecast.create_preview', $disp_data);
	}

	/*
	 * 予想新規作成画面でフォーメーション組合せ数計算画面表示
	 */
	public function createCalc()
	{
		//画面表示用配列
		$disp_data = [
			'ver'				=> time()
		];
		return view('admin.forecast.create_calc', $disp_data);
	}

	/*
	 * 予想の編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Forecast::where('id',$id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_forecast_path').'?page='.$page);
		}

		$listProduct[] = [
			'id'	 => 0,
			'title'	 => '未選択'
		];

		//商品取得
		$db_product_data = Top_product::where('open_flg', 1)->get();
		if( count($db_product_data) > 0 ){
			foreach($db_product_data as $lines){
				$listProduct[] = [
					'id'	=> $lines->id,
					'title'	=> $lines->title,
				];
			}
		}

		//TOPコンテンツのデータ取得
		$db_campaign_data = Top_content::where('type', 1)->where('open_flg', 1)->get();

		//画面表示用配列
		$disp_data = [
			'list_open_flg'		=> config('const.admin_open_type'),
			'forecast_category'	=> config('const.forecast_category'),
			'list_product_data'	=> $listProduct,
			'list_campaign_data'=> $db_campaign_data,
			'page'				=> $page,
			'edit_id'			=> $id,
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.forecast.edit', $disp_data);
	}

	/*
	 * 予想画面の編集処理
	 */
	public function store(Request $request)
	{
		$edit_id = $request->input('edit_id');

		$this->validate($request, [
			'disp_sdate'	=> 'required|date_format_check',
			'disp_edate'	=> 'required|date_format_check',
			'open_sdate'	=> 'required|date_format_check',
			'open_edate'	=> 'required|date_format_check',
			'groups'		=> 'only_num_check|exist_group_id_check',
			'campaigns'		=> 'only_num_check|exist_campaign_id_check',
			'point'			=> 'required|integer',
			'headline'		=> 'required|surrogate_pair_check|emoji_check',
			'title'			=> 'required|surrogate_pair_check|emoji_check|max:'.config('const.forecast_title_max_length'),
			'comment'		=> 'required|surrogate_pair_check|emoji_check|max:'.config('const.forecast_comment_max_length'),
			'detail'		=> 'required|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//予想画面の編集
		if( empty($request->input('del')) ){
			$update = Forecast::where('id', $edit_id)
				->update([
					'disp_sdate'		=> $request->input('disp_sdate'),
					'disp_edate'		=> $request->input('disp_edate'),
					'open_sdate'		=> $request->input('open_sdate'),
					'open_edate'		=> $request->input('open_edate'),
					'category'			=> $request->input('category'),
					'groups'			=> $request->input('groups'),
					'product_id'		=> $request->input('product_id'),
					'campaigns'			=> $request->input('campaigns'),
					'open_flg'			=> $request->input('open_flg'),
					'point'				=> $request->input('point'),
					'headline'			=> $request->input('headline'),
					'title'				=> $request->input('title'),
					'comment'			=> $request->input('comment'),
					'detail'			=> $request->input('detail'),
					'disp_sort_sdate'	=> $request->input('disp_sdate'),
					'disp_sort_edate'	=> $request->input('disp_edate'),
					'open_sort_sdate'	=> $request->input('open_sdate'),
					'open_sort_edate'	=> $request->input('open_edate'),
					'updated_at'		=> Carbon::now()
				]);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['forecasts_edit'].",{$user['login_id']}");

			return null;

		//予想画面の削除
		}else{
			//forecastテーブルからデータ削除
			$delete = Forecast::where('id', $edit_id)->delete();

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['forecasts_delete'].",{$user['login_id']}");

			return null;
		}
	}

	/*
	 * 予想編集画面でプレビュー表示
	 */
	public function editPreview($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Forecast::where('id',$id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_forecast_path').'?page='.$page);
		}

		//画面表示用配列
		$disp_data = [
			'detail'			=> preg_replace("/\n/", "<br />", $db_data->detail),
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.forecast.preview', $disp_data);
	}

	/*
	 * 予想一覧画面で一括削除処理
	 */
	public function bulkUpdate(Request $request)
	{

		//ID取得
		$listId = $request->input('id');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['forecasts_update'].",{$user['login_id']}");

		//削除にチェックが入っていれば
		if( !empty($listId) ){
			foreach($listId as $index => $id){
				if( !empty($request->input('del_flg')[$index]) ){
					$delete = Forecast::where('id', $request->input('del_flg')[$index])->delete();
				}
			}
		}

		return null;
	}

	/*
	 * アクセス画面表示
	 */
	public function access($page, $id)
	{
		//データ取得
		$query = User::query();
		$db_data = $query->join('visitor_logs', 'users.id', '=', 'visitor_logs.client_id')->where('forecast_id', $id)->paginate(config('const.admin_client_list_limit'));

		//画面表示用配列
		$disp_data = [
			'page'			=> $page,
			'id'			=> $id,
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.forecast.access', $disp_data);
	}

	/*
	 * アクセス画面からメール配信画面表示
	 */
	public function editMail($page, $id)
	{	
		//usersテーブルからデータ取得
		$user = User::where('id', $id)->first();
		
		$disp_data = [
			'id'		=> $id,
			'user'		=> $user,
			'ver'		=> time(),
		];
		
		return view('admin.forecast.edit_mail', $disp_data); 
	}
	
	/*
	 * 顧客編集画面→個別メール画面表示→メール送信処理
	 */
	public function editMailSend(Request $request)
	{
		$this->validate($request, [
			'from_name'	=> 'required',
			'from_mail'	=> 'required|email|check_mx_domain',
			'subject'	=> 'required',
			'body'		=> 'required'
		]);
		
		//送信元情報設定
		$options = [
			'html_flg'	 => false,
			'from'		 => $request->input('from_mail'),
			'from_name'	 => $request->input('from_name'),
			'subject'	 => $request->input('subject'),
			'template'	 => config('const.admin_edit_mail')
		];

		//送信データ設定
		$data = [
			'contents'		=> $request->input('body'),
		];

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//バックグラウンドでアクセス一覧のユーザーにメール配信
		$process = new Process(config('const.artisan_command_path')." mail:delivery {$user['select_db']} {$request->input('id')} > /dev/null");

		//非同期実行
		$process->start();

		//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
		//1秒待機
		usleep(1000000);

		return null;
	}


}
