<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Voice;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminPageVoiceController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * ページ管理-ご利用者の声
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Voice::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//画面表示用配列
		$disp_data = [
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.page.voice.index', $disp_data);
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
			'page_voice_disp_type'	=> config('const.page_race_disp_type'),
			'page_voice_search_item'=> config('const.page_voice_search_item'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.page_race_sort_list'),
		];

		return view('admin.page.voice.page_search', $disp_data);
	}

	/*
	 * 検索処理
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Voice::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//
		$disp_data = [
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.page.voice.index', $disp_data);
	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['voice_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Voice::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'ver'			=> time()
		];

		return view('admin.page.voice.index', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{

		//検索項目
		if( !is_null(Session::get('page_voice_search_item_value')) ){
			if( Session::get('page_voice_search_item') == config('const.page_race_search_item')[0][0] ){
				$query->where(Session::get('page_voice_search_item'), '=', Session::get('page_voice_search_item_value'));
			}else{
				$query->where(Session::get('page_voice_search_item'), 'like', "%".Session::get('page_voice_search_item_value')."%");
			}
		}

		//開催日-開始日
		if( !is_null(Session::get('start_voice_date')) ){
			$query->where('post_date', '>=', Session::get('start_voice_date'));
		}

		//開催日-終了日
		if( !is_null(Session::get('end_voice_date')) ){
			$query->where('post_date', '<=', Session::get('end_voice_date'));
		}

		//表示/非表示
		if( !is_null(Session::get('page_voice_disp_type')) ){
			if( Session::get('page_voice_disp_type') != '' ){
				$listPageType = config('const.page_race_disp_type');
				$query->where('open_flg', explode(",", $listPageType[Session::get('page_voice_disp_type')][0]));
			}
		}

		//ソート
		$sort_item = "id";
		$sort_type = "desc";
		if( !is_null(Session::get('page_voice_sort')) ){
			$listSortType = config('const.page_race_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('page_voice_sort')][0]);
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
			Session::put('page_voice_search_item', $request->input('search_item'));
		}

		//検索項目の値
		Session::put('page_voice_search_item_value', $request->input('search_item_value'));

		//開催日-開始日
		Session::put('start_voice_date', $request->input('start_voice_date'));

		//開催日-終了日
		Session::put('end_voice_date', $request->input('end_voice_date'));

		//表示/非表示
		Session::put('page_voice_disp_type', $request->input('page_disp_type'));

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('page_voice_sort', $request->input('sort'));
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('page_search_disp_num', $request->input('search_disp_num'));
		}
	}

	/*
	 * ページ管理-ご利用者の声-新規作成
	 */
	public function create($id = '')
	{
		$db_data = [];

		//バナーがtop_voicesテーブルに登録されていたら取得
		if( !empty($id) ){
			$db_data = Voice::where('id',$id)->first();
		}

		//
		$disp_data = [
			'edit_id'				=> $id,
			'redirect_url'			=> config('const.base_admin_url').'/'.config('const.voice_url_path'),
			'db_data'				=> $db_data,
			'list_open_flg'			=> config('const.admin_open_type'),
			'ver'					=> time()
		];

		return view('admin.page.voice.create', $disp_data); 
	}

	/*
	 * ページ管理-ご利用者の声-新規作成処理
	 */
	public function createSend(Request $request)
	{

		$this->validate($request, [
			'title'		=> 'bail|surrogate_pair_check|emoji_check',
			'writer'	=> 'bail|surrogate_pair_check|emoji_check',
			'comment'	=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		$now_date = Carbon::now();

		if( !empty($request->input('post_date')) ){
			$post_date = $request->input('post_date');
		}else{
			$post_date = $now_date;
		}

		//バナー登録済で新規作成したとき
		if( !empty($request->input('edit_id')) ){
			$update = Voice::where('id', $request->input('edit_id'))
				->update([
					'open_flg'		=> $request->input('open_flg'),
					'title'			=> $request->input('title'),
					'name'			=> $request->input('writer'),
					'msg'			=> $request->input('comment'),
					'post_date'		=> $post_date,
					'updated_at'	=> $now_date
				]);

		//バナー未登録で新規作成したとき
		}else{
			$voice = new Voice([
				'open_flg'		=> $request->input('open_flg'),
				'title'			=> $request->input('title'),
				'name'			=> $request->input('writer'),
				'msg'			=> $request->input('comment'),
				'img'			=> $request->input('img'),
				'post_date'		=> $post_date,
				'sort_date'		=> $now_date,
				'created_at'	=> $now_date,
				'updated_at'	=> $now_date
			]);

			//DB保存
			$voice->save();
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['voice_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ページ管理-ご利用者の声-編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Voice::where('id',$id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_page_voice_path').'?page='.$page);
		}

		//画面表示用配列
		$disp_data = [
			'redirect_url'	=> config('const.base_admin_url').'/'.config('const.voice_edit_url_path'),
			'edit_id'		=> $id,
			'db_data'		=> $db_data,
			'list_open_flg'	=> config('const.admin_open_type'),
			'ver'			=> time()
		];

		return view('admin.page.voice.edit', $disp_data);
	}

	/*
	 * ページ管理-ご利用者の声-編集処理
	 */
	public function store(Request $request)
	{
		$edit_id = $request->input('edit_id');

		$this->validate($request, [
			'title'		=> 'bail|surrogate_pair_check|emoji_check',
			'writer'	=> 'bail|surrogate_pair_check|emoji_check',
			'comment'	=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//的中実績の編集
		if( empty($request->input('del')) ){
			if( !empty($request->input('post_date')) ){
				$post_date = $request->input('post_date');
			}else{
				$post_date = $now_date;
			}

			$update = Voice::where('id', $edit_id)
				->update([
					'open_flg'		=> $request->input('open_flg'),
					'title'			=> $request->input('title'),
					'name'			=> $request->input('writer'),
					'msg'			=> $request->input('comment'),
					'post_date'		=> $post_date,
					'sort_date'		=> Carbon::now(),
					'updated_at'	=> Carbon::now()
				]);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['voice_edit'].",{$user['login_id']}");

			return null;

		//的中実績の削除
		}else{
			//画像を削除するため編集データ取得
			$db_data = Voice::where('id', $edit_id)->first();

			if( !empty($db_data->img) ){
				//画像削除
				system("rm ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.voice_images_path').'/'.$db_data->img);
			}

			//top_voicesテーブルからデータ削除
			$delete = Voice::where('id', $edit_id)->delete();

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['voice_delete'].",{$user['login_id']}");

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
		$this->log_obj->addLog(config('const.admin_display_list')['voice_update'].",{$user['login_id']}");

		foreach($listId as $index => $id){
			if( !empty($request->input('del_flg')[$index]) ){
				//画像を削除するため編集データ取得
				$db_data = Voice::where('id', $request->input('del_flg')[$index])->first();

				if( !empty($db_data->img) ){
					//画像削除
					system("rm ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.voice_images_path').'/'.$db_data->img);
				}

				$delete = Voice::where('id', $request->input('del_flg')[$index])->delete();
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
				$update = Voice::where('id', $id)->update($update_data);
			}
		}

		return null;
	}

	/*
	 * 画像のアップロード処理
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
				$id = Voice::insertGetId([
					'img'			=> 'tmp_img',
					'sort_date'		=> Carbon::now(),
					'created_at'	=> Carbon::now()
				]);
			}

			//画像名をid名にするためupdateを行う
			Voice::where('id', $id)->update([
				'img'=> $id.'.'.$file->getClientOriginalExtension()
			]);

			//画像の保存先を移動
//			$file->move(public_path('images/voice'), $id.'.'.$file->getClientOriginalExtension());
			$file->move(config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.voice_images_path').'/', $id.'.'.$file->getClientOriginalExtension());
		});

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['voice_banner_upload'].",{$user['login_id']}");

		//失敗
		if( is_null($id) ){
			return false;

		//画像アップロード成功
		}else{
			return $id;
		}
	}

}
