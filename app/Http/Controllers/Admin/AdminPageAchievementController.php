<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Achievement;
use App\Model\Top_product;
use App\Model\Top_content;
use App\Model\Forecast;
use Carbon\Carbon;
use Session;
use Utility;

class AdminPageAchievementController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 的中実績の一覧画面
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Achievement::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//画面表示用配列
		$disp_data = [
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.page.achievement.index', $disp_data);
	}

	/*
	 * 検索画面表示
	 */
	public function searchSetting()
	{

		//画面表示用配列
		$disp_data = [
			'session'				=> Session::all(),
			'ver'					=> time(),
			'page_race_search_item'	=> config('const.page_race_search_item'),
			'page_race_disp_type'	=> config('const.page_race_disp_type'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.page_race_sort_list'),
		];

		return view('admin.page.achievement.page_search', $disp_data);
	}

	/*
	 * 検索処理
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Achievement::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//
		$disp_data = [
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.page.achievement.index', $disp_data);
	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['achievement_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Achievement::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.page.achievement.index', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{

		//検索項目
		if( !is_null(Session::get('page_race_search_item_value')) ){
			if( Session::get('page_race_search_item') == config('const.page_race_search_item')[0][0] ){
				$query->where(Session::get('page_race_search_item'), '=', Session::get('page_race_search_item_value'));
			}else{
				$query->where(Session::get('page_race_search_item'), 'like', "%".Session::get('page_race_search_item_value')."%");
			}
		}

		//開催日-開始日
		if( !is_null(Session::get('start_race_date')) ){
			$query->where('race_date', '>=', Session::get('start_race_date'));
		}

		//開催日-終了日
		if( !is_null(Session::get('end_race_date')) ){
			$query->where('race_date', '<=', Session::get('end_race_date'));
		}

		//表示/非表示
		if( !is_null(Session::get('page_race_disp_type')) ){
			if( Session::get('page_race_disp_type') != '' ){
				$listPageType = config('const.page_race_disp_type');
				$query->where('open_flg', explode(",", $listPageType[Session::get('page_race_disp_type')][0]));
			}
		}

		//ソート
		$sort_item = "id";
		$sort_type = "desc";
		if( !is_null(Session::get('page_race_sort')) ){
			$listSortType = config('const.page_race_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('page_race_sort')][0]);
			$query->orderBy($sort_item, $sort_type);
		}else{
			$query->orderBy($sort_item, $sort_type);
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null(Session::get('page_search_disp_num')) ){
			$list_disp_limit = config('const.search_disp_num');
			$disp_limit = $list_disp_limit[Session::get('page_search_disp_num')];
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
			Session::put('page_race_search_item', $request->input('search_item'));
		}

		//検索項目の値
		Session::put('page_race_search_item_value', $request->input('search_item_value'));

		//開催日-開始日
		Session::put('start_race_date', $request->input('start_race_date'));

		//開催日-終了日
		Session::put('end_race_date', $request->input('end_race_date'));

		//表示/非表示
		Session::put('page_race_disp_type', $request->input('page_disp_type'));

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('page_race_sort', $request->input('sort'));
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('page_search_disp_num', $request->input('search_disp_num'));
		}
	}

	/*
	 * 的中実績の新規作成画面
	 */
	public function create($id = '')
	{
		$db_data = [];

		//バナーがtop_achievementテーブルに登録されていたら取得
		if( !empty($id) ){
			$db_data = Achievement::where('id',$id)->first();
		}

		$now_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Carbon::now())."00";

		$db_product_data = Top_product::where('open_flg', 1)->where('sort_start_date', '<=', $now_date)->where('sort_end_date', '>=', $now_date)->get();

		$listProduct = [];
		if( count($db_product_data) > 0 ){
			foreach($db_product_data as $lines){
				$listProduct[] = ['id' => $lines->id, 'title' => $lines->title];
			}
		}
/*
		$db_forecast_data = Forecast::query()
//			->leftJoin('top_contents', 'top_contents.id', '=', 'forecasts.campaigns')
//			->where('forecasts.open_flg', 1)
			->where('forecasts.product_id', 0)
			->whereNotNull('forecasts.campaigns')
			->select('forecasts.*')
//			->where('forecasts.disp_sdate', '<=', $now_date)
//			->where('forecasts.disp_edate', '>=', $now_date)
			->orderBy('forecasts.category', 'desc')
			->get();
 */

		$db_topcontent_data = Top_content::query()
//			->join('forecasts', 'top_contents.id', '=', 'forecasts.campaigns')
			->where('top_contents.open_flg', 1)
			->where(function ($query){
				return $query->orWhere('top_contents.title', 'like', "%無料情報%")
							->orWhere('top_contents.title', 'like', "%ポイント情報%");
			})
//			->where('forecasts.product_id', 0)
//			->whereNotNull('forecasts.campaigns')
			->select('top_contents.*')
			->where('top_contents.start_date', '<=', $now_date)
			->where('top_contents.end_date', '>=', $now_date)
			->orderBy('top_contents.id')
			->get();

		$listTopcontent = [];
		if( count($db_topcontent_data) > 0 ){
			foreach($db_topcontent_data as $lines){
				$listTopcontent[] = ['id' => $lines->id, 'title' => $lines->title];
			}
		}

		//
		$disp_data = [
			'list_product_data'		=> $listProduct,
			'list_topcontent_data'	=> $listTopcontent,
			'db_data'				=> $db_data,
			'list_open_flg'			=> config('const.admin_open_type'),
			'ver'					=> time()
		];

		return view('admin.page.achievement.create', $disp_data); 
	}

	/*
	 * 的中実績の新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'priority_id'	=> 'bail|required|numeric',
			'race_date'		=> 'bail|required|date_format_check',
			'race_name'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.achievement_race_max_length'),
			'msg1'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg1_max_length'),
			'msg2'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg2_max_length'),
			'msg3'			=> 'bail|digital_num_check|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg3_max_length'),
			'comment'		=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg3_max_length'),
		]);

		$now_date = Carbon::now();

		$achievement = new Achievement([
			'type'			=> $request->input('type'),
			'product_id'	=> $request->input('product_id'),
			'priority_id'	=> e($request->input('priority_id')),
			'open_flg'		=> $request->input('open_flg'),
			'race_date'		=> e($request->input('race_date')),
			'race_name'		=> e($request->input('race_name')),
			'msg1'			=> e($request->input('msg1')),
			'msg2'			=> e($request->input('msg2')),
			'msg3'			=> e($request->input('msg3')),
			'memo'			=> e($request->input('comment')),
			'sort_date'		=> $now_date,
			'created_at'	=> $now_date,
			'updated_at'	=> $now_date
		]);

		//DB保存
		$achievement->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['achievement_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 的中実績の編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Achievement::where('id',$id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_page_path').'?page='.$page);
		}

		$now_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Carbon::now())."00";

		$db_product_data = Top_product::where('open_flg', 1)->where('sort_start_date', '<=', $now_date)->where('sort_end_date', '>=', $now_date)->get();
		$listProduct = [];
		if( count($db_product_data) > 0 ){
			foreach($db_product_data as $lines){
				$listProduct[] = ['id' => $lines->id, 'title' => $lines->title];
			}
		}
/*
		$db_forecast_data = Forecast::query()
//			->leftJoin('top_contents', 'top_contents.id', '=', 'forecasts.campaigns')
			->where('forecasts.open_flg', 1)
			->where('forecasts.product_id', 0)
			->whereNotNull('forecasts.campaigns')
			->select('forecasts.*')
			->where('forecasts.disp_sdate', '<=', $now_date)
			->where('forecasts.disp_edate', '>=', $now_date)
			->orderBy('forecasts.category', 'desc')
			->get();
*/
		$db_topcontent_data = Top_content::query()
//			->join('forecasts', 'top_contents.id', '=', 'forecasts.campaigns')
			->where('top_contents.open_flg', 1)
			->where(function ($query){
				return $query->orWhere('top_contents.title', 'like', "%無料情報%")
							->orWhere('top_contents.title', 'like', "%ポイント情報%");
			})
//			->where('forecasts.product_id', 0)
//			->whereNotNull('forecasts.campaigns')
			->select('top_contents.*')
			->where('top_contents.start_date', '<=', $now_date)
			->where('top_contents.end_date', '>=', $now_date)
			->orderBy('top_contents.id')
			->get();

		$listTopcontent = [];
		if( count($db_topcontent_data) > 0 ){
			foreach($db_topcontent_data as $lines){
				$listTopcontent[] = ['id' => $lines->id, 'title' => $lines->title];
			}
		}

		//画面表示用配列
		$disp_data = [
			'edit_id'				=> $id,
			'list_product_data'		=> $listProduct,
			'list_topcontent_data'	=> $listTopcontent,
			'db_data'				=> $db_data,
			'list_open_flg'			=> config('const.admin_open_type'),
			'ver'					=> time()
		];

		return view('admin.page.achievement.edit', $disp_data);
	}

	/*
	 * 的中実績の編集処理
	 */
	public function store(Request $request)
	{
		$edit_id = $request->input('edit_id');

		$this->validate($request, [
			'priority_id'	=> 'bail|required|numeric',
			'race_date'		=> 'bail|required|date_format_check',
			'race_name'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.achievement_race_max_length'),
			'msg1'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg1_max_length'),
			'msg2'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg2_max_length'),
			'msg3'			=> 'bail|digital_num_check|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg3_max_length'),
			'comment'		=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.achievement_msg3_max_length'),
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//的中実績の編集
		if( empty($request->input('del')) ){
			$update = Achievement::where('id', $edit_id)
				->update([
					'type'			=> $request->input('type'),
					'product_id'	=> $request->input('product_id'),
					'priority_id'	=> $request->input('priority_id'),
					'open_flg'		=> $request->input('open_flg'),
					'race_date'		=> $request->input('race_date'),
					'race_name'		=> $request->input('race_name'),
					'msg1'			=> $request->input('msg1'),
					'msg2'			=> $request->input('msg2'),
					'msg3'			=> $request->input('msg3'),
					'memo'			=> $request->input('comment'),
					'updated_at'	=> Carbon::now()
				]);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['achievement_edit'].",{$user['login_id']}");

			return null;

		//的中実績の削除
		}else{
			//top_achievementsテーブルからデータ削除
			$delete = Achievement::where('id', $edit_id)->delete();

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['achievement_delete'].",{$user['login_id']}");

			return null;
		}
	}

	/*
	 * 一括更新処理
	 */
	public function bulkUpdate(Request $request)
	{

		//ID取得
		$listId = $request->input('id');
		
		//公開フラグ取得
		$listOpenFlg = $request->input('open_flg');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['achievement_update'].",{$user['login_id']}");

		foreach($listId as $index => $id){
			if( !empty($request->input('del_flg')[$index]) ){
				$delete = Achievement::where('id', $request->input('del_flg')[$index])->delete();
			}else{
				//更新データ設定
				//公開フラグのデフォルトはopen_flg:0
				$update_data = [
						'open_flg'		=> 0
				];

				//公開フラグがオンならopen_flg:1
				if( !empty($listOpenFlg) && in_array($id, $listOpenFlg) ){
					$update_data['open_flg'] = 1;
				}
				
				//ポイント設定画面の更新処理
				$update = Achievement::where('id', $id)->update($update_data);
			}
		}

		return null;
	}

}
