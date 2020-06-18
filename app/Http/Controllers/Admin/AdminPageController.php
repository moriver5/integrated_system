<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Top_content;
use App\Model\Top_product;
use App\Model\Convert_table;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use Storage;
use File;

class AdminPageController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * ページ管理-TOPコンテンツ
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Top_content::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//画面表示用配列
		$disp_data = [
			'preview_url'	=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview').'/',
			'db_data'		=> $db_data,
			'list_type'		=> config('const.page_create_type'),
			'view_num'		=> config('const.view_num'),
			'list_open_flg'	=> config('const.admin_open_type'),
			'page_order'	=> config('const.page_order_num'),
			'ver'			=> time()
		];

		return view('admin.page.content.index', $disp_data);
	}

	/*
	 * 検索設定画面
	 */
	public function searchSetting()
	{

		//画面表示用配列
		$disp_data = [
			'view_num'			=> config('const.view_num'),
			'session'			=> Session::all(),
			'ver'				=> time(),
			'page_search_item'	=> config('const.page_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'regist_status'		=> config('const.regist_status'),
			'page_type'			=> config('const.page_type'),
			'search_disp_num'	=> config('const.search_disp_num'),
			'sort_list'			=> config('const.page_sort_list'),
		];

		return view('admin.page.content.page_search', $disp_data);
	}

	/*
	 * 検索結果のページャーから呼び出し
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Top_content::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//top_productsテーブル、top_bannersテーブルからデータ取得
		list($listProducts, $listBanners) = $this->_getProductsBannerData($db_data);
		
		//
		$disp_data = [
			'preview_url'	=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview').'/',
			'products'		=> $listProducts,
			'banners'		=> $listBanners,
			'db_data'		=> $db_data,
			'list_type'		=> config('const.page_create_type'),
			'view_num'		=> config('const.view_num'),
			'list_open_flg'	=> config('const.admin_open_type'),
			'page_order'	=> config('const.page_order_num'),
			'ver'			=> time()
		];

		return view('admin.page.content.index', $disp_data);
	}

	/*
	 * 検索設定画面から検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['top_content_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Top_content::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//top_productsテーブル、top_bannersテーブルからデータ取得
		list($listProducts, $listBanners) = $this->_getProductsBannerData($db_data);

		$disp_data = [
			'preview_url'	=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview').'/',
			'products'		=> $listProducts,
			'banners'		=> $listBanners,
			'db_data'		=> $db_data,
			'list_type'		=> config('const.page_create_type'),
			'view_num'		=> config('const.view_num'),
			'list_open_flg'	=> config('const.admin_open_type'),
			'page_order'	=> config('const.page_order_num'),
			'ver'			=> time()
		];

		return view('admin.page.content.index', $disp_data);
	}

	/*
	 * top_productsテーブルからデータを取得
	 */
	private function _getProductsBannerData($db_data)
	{
		//DBデータがあるとき
		if( !empty($db_data) ){
			//IDのリストを生成
			$listId = [];
			foreach($db_data as $lines){
				$listId[] = $lines->id;
			}

			$listProducts = [];
			$listBanners = [];

			//設定済のIDリストを取得
			$products = Top_product::whereIn('id',$listId)->get();
			foreach($products as $lines){
				$listProducts[$lines->id] = 1;		
			}
		}

		return [$listProducts, $listBanners];
	}

	/*
	 * 検索条件取得
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{

		//検索項目
		if( !is_null(Session::get('page_search_item_value')) ){
			if( Session::get('page_search_item') == config('const.page_search_item')[1][0] ){
				$query->where(Session::get('page_search_item'), '=', Session::get('page_search_item_value'));
			}else{
				$query->where(Session::get('page_search_item'), 'like', "%".Session::get('page_search_item_value')."%");
			}
		}

		//TYPE
		if( !is_null(Session::get('page_type')) ){
			$listPageType = config('const.page_type');
			$query->whereIn('type', explode(",", $listPageType[Session::get('page_type')][0]));
		}
/*
		//閲覧人数表示
		if( !is_null(Session::get('view_num')) ){
			$listViewNum = config('const.view_num');
			$query->whereIn('type', explode(",", $listViewNum[Session::get('view_num')][0]));
		}
*/
		//ソート
		$sort_item = "id";
		$sort_type = "desc";
		if( !is_null(Session::get('page_sort')) ){
			$listSortType = config('const.page_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('page_sort')][0]);
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
	 * 検索条件保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('page_search_item', $request->input('search_item'));
		}

		//検索項目の値
		Session::put('page_search_item_value', $request->input('search_item_value'));

		//TYPE
		if( !is_null($request->input('page_type')) ){
			Session::put('page_type', $request->input('page_type'));
		}

		//閲覧人数表示
		if( !is_null($request->input('view_num')) ){
			Session::put('view_num', $request->input('view_num'));
		}

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('page_sort', $request->input('sort'));
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('page_search_disp_num', $request->input('search_disp_num'));
		}
	}

	/*
	 * TOPコンテンツの新規作成画面
	 */
	public function create($id = '')
	{
		$db_data = [];

		//バナーがtop_contentsテーブルに登録されていたら取得
		if( !empty($id) ){
			$db_data = Top_content::where('id',$id)->first();
		}

		//現在時刻取得
		$now_date = Carbon::now();

		$db_product_data = Top_product::where('open_flg', 1)
			->where('sort_start_date', '<=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->where('sort_end_date', '>=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->get();

		$list_product = [0 => '選択なし'];
		if( count($db_product_data) > 0 ){
			foreach($db_product_data as $lines){
				$list_product[$lines->id] = $lines->title;
			}
		}

		//
		$disp_data = [
			'select_db'			=> Session::get('operation_select_db'),
			'edit_id'			=> $id,
			'db_product_data'	=> $list_product,
			'db_data'			=> $db_data,
			'redirect_url'		=> config('const.base_admin_url').'/'.config('const.top_content_create_url_path'),
			'list_type'			=> config('const.page_create_type'),
			'view_num'			=> config('const.view_num'),
			'list_open_flg'		=> config('const.admin_open_type'),
			'page_order'		=> config('const.page_order_num'),
			'page_link'			=> config('const.page_link'),
			'ver'				=> time()
		];

		return view('admin.page.content.create', $disp_data); 
	}

	/*
	 * TOPコンテンツ新規作成処理
	 */
	public function createSend(Request $request)
	{
		$validate = [
			'title'			=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.top_content_title_max_length'),
			'groups'		=> 'bail|only_num_check|exist_group_id_check:'.Session::get('operation_select_db'),
			'start_date'	=> 'bail|required|date_format_check',
			'end_date'		=> 'bail|required|date_format_check',
			'html_body'		=> 'bail|surrogate_pair_check|emoji_check',
		];

		if( !empty($request->input('url')) ){
			$validate = array_merge($validate,[
				'url'	=> 'active_url'
			]);
		}

		$this->validate($request, $validate);

		$link_flg = $request->input('link_flg');
		if( empty($link_flg) ){
			$link_flg = 0;
		}

		$now_date = Carbon::now();

		//バナー登録済で新規作成したとき
		if( !empty($request->input('edit_id')) ){
			$update = Top_content::where('id', $request->input('edit_id'))
				->update([
					'title'				=> $request->input('title'),
					'type'				=> $request->input('type'),
					'open_flg'			=> $request->input('open_flg'),
					'link_flg'			=> $link_flg,
					'order_num'			=> $request->input('order'),
					'groups'			=> $request->input('groups'),
					'start_date'		=> $request->input('start_date'),
					'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date')).'00',
					'end_date'			=> $request->input('end_date'),
					'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date')).'00',
					'url'				=> $request->input('url'),
					'html_body'			=> $request->input('html_body'),
					'sort_date'			=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1-$3-$5", $request->input('start_date')),
					'updated_at'		=> $now_date
				]);

		//バナー未登録で新規作成したとき
		}else{
			$top_content = new Top_content([
				'title'				=> $request->input('title'),
				'type'				=> $request->input('type'),
				'open_flg'			=> $request->input('open_flg'),
				'link_flg'			=> $link_flg,
				'order_num'			=> $request->input('order'),
				'groups'			=> $request->input('groups'),
				'start_date'		=> $request->input('start_date'),
				'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date')).'00',
				'end_date'			=> $request->input('end_date'),
				'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date')).'00',
				'url'				=> $request->input('url'),
				'html_body'			=> $request->input('html_body'),
				'sort_date'			=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1-$3-$5", $request->input('start_date')),
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date
			]);

			//DB保存
			$top_content->save();
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['top_content_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * TOPコンテンツ編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Top_content::where('id',$id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_page_path').'?page='.$page);
		}

		//現在時刻取得
		$now_date = Carbon::now();

		$db_product_data = Top_product::where('open_flg', 1)
			->where('sort_start_date', '<=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->where('sort_end_date', '>=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->get();

		$list_product = [0 => '選択なし'];
		if( count($db_product_data) > 0 ){
			foreach($db_product_data as $lines){
				$list_product[$lines->id] = $lines->title;
			}
		}
/*
		$save_img = config('const.public_full_path').config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.top_content_images_path').'/'.$db_data->img;
		$move_img = config('const.public_full_path').config('const.top_content_images_path').'/'.Session::get('operation_select_db').'/'.$db_data->img;

		//画像が存在すれば
		if ( !empty($db_data->img) ) {
			//画像を管理画面に表示するためコピー
			File::copy($save_img, $move_img);
		}
*/
		//画面表示用配列
		$disp_data = [
			'preview_url'		=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview'),
			'redirect_url'		=> config('const.base_admin_url').'/'.config('const.top_content_create_url_path'),
			'edit_id'			=> $id,
			'db_product_data'	=> $list_product,
			'db_data'			=> $db_data,
			'list_type'			=> config('const.page_create_type'),
			'view_num'			=> config('const.view_num'),
			'list_open_flg'		=> config('const.admin_open_type'),
			'page_order'		=> config('const.page_order_num'),
			'page_link'			=> config('const.page_link'),
			'ver'				=> time()
		];

		return view('admin.page.content.edit', $disp_data);
	}

	/*
	 * TOPコンテンツ編集処理
	 */
	public function store(Request $request)
	{
		$edit_id = $request->input('edit_id');

		$link_flg = $request->input('link_flg');
		if( empty($link_flg) ){
			$link_flg = 0;
		}

		$validate = [
			'title'			=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.top_content_title_max_length'),
			'groups'		=> 'bail|only_num_check|exist_group_id_check:'.Session::get('operation_select_db'),
			'start_date'	=> 'bail|required|date_format_check',
			'end_date'		=> 'bail|required|date_format_check',
			'html_body'		=> 'bail|surrogate_pair_check|emoji_check',
		];

		if( !empty($request->input('url')) ){
			$validate = array_merge($validate,[
				'url'	=> 'active_url'
			]);
		}

		$this->validate($request, $validate);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		list($sort_date,$sort_time) = explode(" ", $request->input('start_date'));
		
		//TOPコンテンツ編集
		if( empty($request->input('del')) ){
			$update = Top_content::where('id', $edit_id)
				->update([
					'title'				=> $request->input('title'),
					'type'				=> $request->input('type'),
					'open_flg'			=> $request->input('open_flg'),
					'link_flg'			=> $link_flg,
					'order_num'			=> $request->input('order'),
					'groups'			=> $request->input('groups'),
					'start_date'		=> $request->input('start_date'),
					'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date')).'00',
					'end_date'			=> $request->input('end_date'),
					'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date')).'00',
					'url'				=> $request->input('url'),
					'html_body'			=> $request->input('html_body'),
					'sort_date'			=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1-$3-$5", $sort_date),
					'updated_at'		=> Carbon::now()
				]);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['top_content_edit'].",{$user['login_id']}");

			return null;

		//TOPコンテンツ削除
		}else{
			//画像を削除するため編集データ取得
			$db_data = Top_content::where('id', $edit_id)->first();

			if( !empty($db_data->img) ){
				//画像削除
				system("rm ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.top_content_images_path').'/'.$db_data->img);
			}

			//top_contentsテーブルからデータ削除
			$delete = Top_content::where('id', $edit_id)->delete();

			//top_productsテーブルからデータ削除
			$delete = Top_product::where('id', $edit_id)->delete();

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['top_content_delete'].",{$user['login_id']}");

			return null;
		}
	}

	/*
	 * ページ管理-TOPコンテンツ-一括更新
	 */
	public function bulkUpdate(Request $request)
	{
		$this->validate($request, [
			'start_date.*'	=> 'required',
			'end_date.*'	=> 'required',
		]);

		//ID取得
		$listId = $request->input('id');
		
		//公開フラグ取得
		$listOpenFlg = $request->input('open_flg');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['top_content_update'].",{$user['login_id']}");

		foreach($listId as $index => $id){
			if( !empty($request->input('del_flg')[$index]) ){
				//画像を削除するため編集データ取得
				$db_data = Top_content::where('id', $request->input('del_flg')[$index])->first();

				if( !empty($db_data->img) ){
					//画像削除
					system("rm ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.top_content_images_path').'/'.$db_data->img);
				}

				$delete = Top_content::where('id', $request->input('del_flg')[$index])->delete();
			}else{
				//更新データ設定
				//公開フラグのデフォルトはopen_flg:0
				$update_data = [
						'order_num'			=> $request->input('order')[$index],
						'type'				=> $request->input('type')[$index],
						'open_flg'			=> 0,
						'start_date'		=> $request->input('start_date')[$index],
						'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date')[$index]).'00',
						'end_date'			=> $request->input('end_date')[$index],
						'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date')[$index]).'00',
						'sort_date'			=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1-$3-$5", $request->input('start_date')[$index]),
				];

				//公開フラグがオンならopen_flg:1
				if( !empty($listOpenFlg) && in_array($id, $listOpenFlg) ){
					$update_data['open_flg'] = 1;
				}

				//ポイント設定画面の更新処理
				$update = Top_content::where('id', $id)->update($update_data);
			}
		}

		return null;
	}

	/*
	 * TOPコンテンツ一覧のプレビュー表示
	 */
	public function preview($id, $type)
	{
		$db_data = Top_content::where('id',$id)->first();

		$html_body = $db_data->html_body;
		if( $db_data->link_flg == 1 ){
			if( !empty($db_data->url) ){
				$html_body = $db_data->url;
			}else{
				$html_body = __('messages.external_link_msg');				
			}
		}

		//画面表示用配列
		$disp_data = [
			'img_url'		=> $db_data->img,
			'html_body'		=> $html_body,
			'ver'			=> time()
		];

		if( $db_data->link_flg == 1 ){
			return view('admin.page.content.external_url_preview', $disp_data);
		}

		//キャンペーン用
		if( $type == 1 ){
			return view('admin.page.content.campaign_preview', $disp_data);

		//レギュラー用
		}else{
			return view('admin.page.content.regular_preview', $disp_data);
		}
	}

	/*
	 * ページ管理-TOPコンテンツ-新規作成-プレビュー
	 */
	public function createPreview($type)
	{
		//キャンペーン用
		if( $type == 1 ){
			return view('admin.page.content.create_campaign_preview');

		//レギュラー用
		}else{
			return view('admin.page.content.create_regular_preview');
		}
	}

	/*
	 *  ページ管理-TOPコンテンツ-変換表
	 */
	public function convert($id = null)
	{		
		$db_data = Convert_table::get();

		$disp_data = [
			'db_data'	=> $db_data,
			'id'		=> $id,
			'ver'		=> time(),
		];
		
		return view('admin.page.content.convert_table', $disp_data);
	}

	/*
	 * ページ管理-TOPコンテンツ-画像アップロード処理
	 */
	public function uploadImageSend(Request $request)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//top_contentsテーブルに登録されているidを取得
		$id = $request->input('edit_id');

		//画像名をtop_contentsテーブルにinsert
		DB::connection(Session::get('operation_select_db'))->transaction(function() use($file, &$id){
			//top_contentsテーブルにまだ登録されていないとき
			if( is_null($id) ){
				//最初の１回目の画像アップロードはinsert
				$id = Top_content::insertGetId(['img' => 'tmp_img']);

				$now_date = Carbon::now();

				//画像名をid名にするためupdateを行う
				Top_content::where('id', $id)->update([
					'img'			=> $id.'.'.$file->getClientOriginalExtension(),
					'sort_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1-$3-$5", $now_date),
					'created_at'	=> $now_date
				]);
			}else{
				Top_content::where('id', $id)->update([
					'img'			=> $id.'.'.$file->getClientOriginalExtension()
				]);	
			}

			//画像の保存先を移動(/data/www/siteo/public/ドメインフォルダ/images/top_content)
			$file->move(config('const.public_full_path').config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.top_content_images_path'), $id.'.'.$file->getClientOriginalExtension());
		});

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['top_banner_upload'].",{$user['login_id']}");

		//失敗
		if( is_null($id) ){
			return false;

		//画像アップロード成功
		}else{
			return $id;
		}
	}
}