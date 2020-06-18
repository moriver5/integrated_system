<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Top_product;
use App\Model\Tipster;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminPageProductController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * ページ管理-商品設定-一覧
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Top_product::query();
		$query->join('tipsters', 'top_products.tipster', '=', 'tipsters.id');
		$query->select('top_products.*', 'tipsters.name', 'tipsters.id as tipster_id');
		$query->orderBy('top_products.id', 'desc');

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

		return view('admin.page.product.index', $disp_data);
	}

	/*
	 * 検索設定
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

		return view('admin.page.product.product_search', $disp_data);
	}

	/*
	 * 検索条件取得
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{

		//検索項目
		if( !is_null(Session::get('product_title')) ){
			$query->where('title', 'like', "%".Session::get('product_title')."%");
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null(Session::get('product_search_disp_num')) ){
			$list_disp_limit = config('const.search_disp_num');
			$disp_limit = $list_disp_limit[Session::get('product_search_disp_num')];
		}
		
		$query->orderBy('id', 'desc');

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->count();

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
		//検索項目の値
		Session::put('product_title', $request->input('title'));

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('product_search_disp_num', $request->input('search_disp_num'));
		}
	}

	/*
	 * 検索結果のページャーのリンクから呼び出し
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Top_product::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//
		$disp_data = [
			'preview_url'	=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview').'/',
			'db_data'		=> $db_data,
			'list_type'		=> config('const.page_create_type'),
			'page_order'	=> config('const.page_order_num'),
			'ver'			=> time()
		];

		return view('admin.page.product.index', $disp_data);
	}

	/*
	 * 検索設定画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['top_product_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Top_product::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'preview_url'	=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview').'/',
			'db_data'		=> $db_data,
			'list_type'		=> config('const.page_create_type'),
			'view_num'		=> config('const.view_num'),
			'page_order'	=> config('const.page_order_num'),
			'ver'			=> time()
		];

		return view('admin.page.product.index', $disp_data);
	}

	/*
	 * ページ管理-商品設定-新規作成
	 */
	public function create($page)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//予想師取得
		$listTipster = Utility::getTipster();

		//
		$disp_data = [
			'page'			=> $page,
			'list_tipster'	=> $listTipster,
			'list_open_flg'	=> config('const.admin_open_type'),
			'page_order'	=> config('const.page_order_num'),
			'ver'			=> time()
		];

		return view('admin.page.product.create', $disp_data); 
	}
	
	/*
	 * ページ管理-商品設定-新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'tipster'		=> 'bail|required|numeric',
			'quantity'		=> 'bail|required|numeric',
			'money'			=> 'bail|required|numeric',
			'point'			=> 'bail|required|numeric',
			'quantity'		=> 'bail|required|numeric',
			'title'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.top_product_title_max_length'),
			'groups'		=> 'bail|only_num_check|exist_group_id_check:'.Session::get('operation_select_db'),
			'start_date'	=> 'bail|required|date_format_check',
			'end_date'		=> 'bail|required|date_format_check',
			'sold_out_date'	=> 'bail|required|date_format_check',
		]);

		$listDiscount = [];
		foreach($request->all() as $key => $val){
			if( preg_match("/^title([0-9]+)/", $key, $ids) > 0 ){
				$listDiscount['json'][] = [
					'title'		 => $request->input('title'.$ids[1]),
					'groups'	 => $request->input('groups'.$ids[1]),
					'hold_pt'	 => $request->input('hold_pt'.$ids[1]),
					'money'		 => $request->input('money'.$ids[1]),					
				];
			}
		}
		$json_encode = json_encode($listDiscount, JSON_UNESCAPED_UNICODE);

		$now_date = Carbon::now();

		DB::connection(Session::get('operation_select_db'))->transaction(function() use($request, $json_encode, $now_date){
			DB::connection(Session::get('operation_select_db'))->insert("insert into top_products("
				. "title, "
				. "comment, "
				. "open_flg, "
				. "groups, "
				. "saddle, "
				. "tickets, "
				. "order_num, "
				. "tipster, "
				. "quantity, "
				. "money, "
				. "point, "
				. "discount, "
				. "start_date, "
				. "sort_start_date, "
				. "end_date, "
				. "sort_end_date, "
				. "sold_out_date, "
				. "sort_sold_out_date, "
				. "sort_date, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "'".e($request->input('title'))."', "
				. "'".$request->input('comment')."', "
				. "".$request->input('open_flg').", "
				. "'".e($request->input('groups'))."', "
				. "'".$request->input('saddle')."', "
				. "'".$request->input('tickets')."', "
				. $request->input('order').", "
				. $request->input('tipster').", "
				. $request->input('quantity').", "
				. $request->input('money').", "
				. $request->input('point').", "
				. "'".$json_encode."', "
				. "'".$request->input('start_date')."', "
				. preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date'))."00, "
				. "'".$request->input('end_date')."', "
				. preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date'))."00, "
				. "'".$request->input('sold_out_date')."', "
				. preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('sold_out_date'))."00, "
				. "'{$request->input('start_date')}', "
				. "'{$now_date}', "
				. "'{$now_date}')");
		});
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['top_product_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ページ管理-商品設定-編集画面
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Top_product::where('id',$id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_page_product_path').'?page='.$page);
		}

		$json_data = [];
		$json_data = json_decode($db_data->discount, true);

		//データがない場合
		if( empty($json_data['json']) ){
			$json_data['json'] = [];
		}

		//予想師取得
		$listTipster = Utility::getTipster();

		//画面表示用配列
		$disp_data = [
			'preview_url'	=> config('const.base_url').config('const.admin_base_path').config('const.admin_content_preview'),
			'redirect_url'	=> config('const.base_admin_url').'/'.config('const.top_content_create_url_path'),
			'edit_id'		=> $id,
			'list_tipster'	=> $listTipster,
			'db_data'		=> $db_data,
			'list_discount'	=> $json_data['json'],
			'discount'		=> count($json_data['json']),
			'list_type'		=> config('const.page_create_type'),
			'view_num'		=> config('const.view_num'),
			'list_open_flg'	=> config('const.admin_open_type'),
			'page_order'	=> config('const.page_order_num'),
			'page_link'		=> config('const.page_link'),
			'ver'			=> time()
		];

		return view('admin.page.product.edit', $disp_data);
	}

	/*
	 * ページ管理-商品設定-編集処理
	 */
	public function store(Request $request)
	{
		$edit_id = $request->input('edit_id');

		$this->validate($request, [
			'tipster'		=> 'bail|required|numeric',
			'quantity'		=> 'bail|required|numeric',
			'money'			=> 'bail|required|numeric',
			'point'			=> 'bail|required|numeric',
			'quantity'		=> 'bail|required|numeric',
			'title'			=> 'bail|required||surrogate_pair_check|emoji_check|max:'.config('const.top_product_title_max_length'),
			'groups'		=> 'bail|only_num_check|exist_group_id_check:'.Session::get('operation_select_db'),
			'start_date'	=> 'bail|required|date_format_check',
			'end_date'		=> 'bail|required|date_format_check',
			'sold_out_date'	=> 'bail|required|date_format_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		list($sort_date,$sort_time) = explode(" ", $request->input('start_date'));

		$listDiscount = [];
		foreach($request->all() as $key => $val){
			if( preg_match("/^title([0-9]+)/", $key, $ids) > 0 ){
				$listDiscount['json'][] = [
					'title'		 => $request->input('title'.$ids[1]),
					'groups'	 => $request->input('groups'.$ids[1]),
					'hold_pt'	 => $request->input('hold_pt'.$ids[1]),
					'money'		 => $request->input('money'.$ids[1]),					
				];
			}
		}
		$json_encode = json_encode($listDiscount, JSON_UNESCAPED_UNICODE);

		//TOPコンテンツ編集
		if( empty($request->input('del')) ){
			$update = Top_product::where('id', $edit_id)
				->update([
					'title'				=> $request->input('title'),
					'comment'			=> $request->input('comment'),
					'open_flg'			=> $request->input('open_flg'),
					'order_num'			=> $request->input('order'),
					'saddle'			=> $request->input('saddle'),
					'tickets'			=> $request->input('tickets'),
					'groups'			=> $request->input('groups'),
					'tipster'			=> $request->input('tipster'),
					'quantity'			=> $request->input('quantity'),
					'money'				=> $request->input('money'),
					'point'				=> $request->input('point'),
					'discount'			=> $json_encode,
					'start_date'		=> $request->input('start_date'),
					'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date'))."00",
					'end_date'			=> $request->input('end_date'),
					'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date'))."00",
					'sold_out_date'		=> $request->input('sold_out_date'),
					'sort_sold_out_date'=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('sold_out_date'))."00",
					'sort_date'			=> $sort_date,
					'updated_at'		=> Carbon::now()
				]);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['top_product_edit'].",{$user['login_id']}");

			return null;

		//TOPコンテンツ削除
		}else{
			//top_bannersテーブルからデータ削除
			$delete = Top_product::where('id', $edit_id)->delete();

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['top_product_delete'].",{$user['login_id']}");

			return null;
		}
	}

	/*
	 * ページ管理-商品設定-一括更新
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
		$this->log_obj->addLog(config('const.admin_display_list')['top_product_update'].",{$user['login_id']}");

		foreach($listId as $index => $id){
			if( !empty($request->input('del_flg')[$index]) ){
				$delete = Top_product::where('id', $request->input('del_flg')[$index])->delete();
			}else{
				list($sort_date, $sort_time) = explode(" ", $request->input('start_date')[$index]);
				//更新データ設定
				//公開フラグのデフォルトはopen_flg:0
				$update_data = [
						'order_num'			=> $request->input('order')[$index],
						'open_flg'			=> 0,
						'money'				=> $request->input('money')[$index],
						'point'				=> $request->input('point')[$index],
						'start_date'		=> $request->input('start_date')[$index],
						'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_date')[$index])."00",
						'end_date'			=> $request->input('end_date')[$index],
						'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_date')[$index])."00",
						'sort_date'			=> $sort_date,
				];

				//公開フラグがオンならopen_flg:1
				if( !empty($listOpenFlg) && in_array($id, $listOpenFlg) ){
					$update_data['open_flg'] = 1;
				}

				//ポイント設定画面の更新処理
				$update = Top_product::where('id', $id)->update($update_data);
			}
		}

		return null;
	}
}
