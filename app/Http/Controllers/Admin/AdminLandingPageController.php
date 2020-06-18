<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Landing_page;
use App\Model\Landing_pages_content;
use App\Model\Landing_pages_preview;
use App\Model\Group;
use App\Model\User;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use File;

class AdminLandingPageController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * ランディングページ-LP一覧
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Landing_page::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//件数取得
		$total = $db_data->total();

		//画面表示用配列
		$disp_data = [
			'base_url'			=> config('const.list_url_const')[Session::get('operation_select_db')],
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.index', $disp_data);
	}

	/*
	 * ランディングページ作成トップ画面
	 */
	public function createLandingPage($id, $type, $name = null)
	{
		//ランディングデータ取得
		$query = Landing_page::query();

		//ページ名のリスト取得
		$db_page_data = $query->join('landing_pages_contents', 'landing_pages.id', '=', 'landing_pages_contents.lp_id')
			->select('name','type')
			->where([
				'landing_pages_contents.lp_id'	=> $id,
				'landing_pages_contents.type'	=> $type
			])
			->get();

		//ランディングデータ取得
		$query = Landing_page::query();

		$db_data = $query->join('landing_pages_contents', 'landing_pages.id', '=', 'landing_pages_contents.lp_id')
			->where([
				'landing_pages_contents.lp_id'	=> $id,
				'landing_pages_contents.name'	=> $name,
				'landing_pages_contents.type'	=> $type
			])
			->first();

		//ファイルが存在しなかったらindexを表示
		if( count($db_data) == 0 ){
			return redirect(config('const.base_admin_url').config('const.admin_lp_path')."/create/content/{$id}/{$type}/index");
		}

		if( preg_match("/(css|js)$/", $name) > 0 ){
			$link_url = config('const.list_url_const')[Session::get('operation_select_db')]."/".config('const.landing_url_path')."/{$id}/".config('const.list_career_lp_dir')[$type]."/{$name}";
		}else{
			$link_url = config('const.list_url_const')[Session::get('operation_select_db')]."/".config('const.landing_url_path')."/{$id}/{$name}";	
		}

		//画面表示用配列
		$disp_data = [
			'add_page_post_url'	=> config('const.baseurl')."/admin/member/lp/create/content/{$id}/{$type}/{$name}/add/page/send",
			'post_url'			=> config('const.baseurl')."/admin/member/lp/create/content/{$id}/{$type}/{$name}/send",
			'preview_url'		=> config('const.baseurl')."/admin/member/lp/create/content/{$id}/{$type}/{$name}/preview",
			'link_url'			=> $link_url,
			'csrf_token'		=> csrf_token(),
			'lp_default_page'	=> $db_page_data,
			'id'				=> $id,
			'type'				=> $type,
			'current_page'		=> $name,
			'list_open_flg'		=> config('const.admin_open_type'),
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.lp.landing_page', $disp_data);
	}

	/*
	 * ランディングページ-プレビュー
	 */
	public function previewLandingPageSend(Request $request, $id, $type, $name)
	{
		//エラーチェック
		$this->validate($request, [
//			'lp_content'	=> 'bail|required|surrogate_pair_check|emoji_check',
			'lp_content'	=> 'bail|required',
		]);

		$content = $request->input('lp_content');

		$db_data = DB::connection(Session::get('operation_select_db'))->select("select count(*) as count from landing_pages_previews where lp_id = {$id} and name = '{$name}';");

		//プレビューデータinsert
		if( $db_data[0]->count == 0 ){
			Landing_pages_preview::insert([
				'lp_id'		=> $id,
				'name'		=> $name,
				'content'	=> $content,
				'created_at'=> Carbon::now(),
				'updated_at'=> Carbon::now()
			]);

		//プレビューデータupdate
		}else{
			$update = Landing_pages_preview::where([
				'lp_id'	=> $id,
				'name'	=> $name
				])->update([
					'content'		=> $content,
					'updated_at'	=> Carbon::now()
			]);
		}

		return "ok";
	}

	/*
	 * ランディングページ-プレビュー
	 */
	public function previewLandingPage($id, $type, $name)
	{
		//ランディングデータ取得
//		$query = Landing_page::query();

//		$db_lp_data = Landing_page::where('id',$id)->first();

		$db_data = DB::connection(Session::get('operation_select_db'))->select("select content from landing_pages_previews where lp_id = {$id} and type= {$type} and name = '{$name}';");

		$content = preg_replace("/(src.*=.*[\"'])(\/.*?(png|gif|jpg|jpeg))/u", "$1".config('const.list_url_const')[Session::get('operation_select_db')]."$2", $db_data[0]->content);
		$content = preg_replace("/(href.*=.*[\"'])(\/.*?\.css)/u", "$1".config('const.list_url_const')[Session::get('operation_select_db')]."$2", $content);
		$content = preg_replace("/(href.*=.*[\"'])(\/.*?\.pdf)/u", "$1".config('const.list_url_const')[Session::get('operation_select_db')]."$2", $content);

		return response($content);
	}

	/*
	 * ランディングページ更新処理
	 */
	public function updateLandingPageSend(Request $request, $id, $type, $name)
	{
		//エラーチェック
		$this->validate($request, [
//			'lp_content'	=> 'bail|required|surrogate_pair_check|emoji_check',
			'lp_content'	=> 'bail|required',
		]);

		//Content取得
		$content = $request->input('lp_content');

		//公開フラグ取得
		$open_flg = $request->input('open_flg');
		
		//削除フラグ取得
		$del_flg = $request->input('del');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//個別ページ削除
		if( $del_flg == 1 ){
			//データを削除
			$delete = Landing_pages_content::where([
				'lp_id'	=> $id,
				'type'	=> $type,
				'name'	=> $name
				])->delete();

			//個別ページのファイル削除(/data/www/siteo/storage/app/public/ドメインフォルダ/LP)
			system("rm ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.config('const.list_career_lp_dir')[$type].'/'.$name);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_delete_page'].",{$user['login_id']}");

		//個別ページ更新
		}else{
			//データを更新
			$update = Landing_pages_content::where([
				'lp_id'	=> $id,
				'type'	=> $type,
				'name'	=> $name
				])->update([
					'url_open_flg'	=> $open_flg,
					'content'		=> $content,
					'updated_at'	=> Carbon::now()
				]);

			//ファイルに書き込み
			$file_size = File::put(config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.config('const.list_career_lp_dir')[$type].'/'.$name, Utility::getConvertData($content));

			//シンボリックリンクを張る
			if( !empty($open_flg) ){
				system("ln -s ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.config('const.list_career_lp_dir')[$type].'/'.$name.' '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.config('const.list_career_lp_dir')[$type].'/');
				system("ln -s ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/.htaccess '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/');

			//シンボリックリンクを削除
			}else{
				system("unlink ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.config('const.list_career_lp_dir')[$type].'/'.$name);
			}

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_update_page'].",{$user['login_id']}");
		}

		return null;
	}

	/*
	 * ランディングページ画面追加
	 */
	public function addLandingPageSend(Request $request, $id, $name)
	{
		//ページ名のチェック
		$this->validate($request, [
			'page'	=> 'check_file_name',
		]);

		//Content取得
		$add_file_name = $request->input('page');

		$now_date = Carbon::now();

		//データ登録
		$lp_content = new Landing_pages_content([
			'lp_id'				=> $id,
			'name'				=> $add_file_name,
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		]);

		//DB保存
		$lp_content->save();

		//ファイルに書き込み
		$file_size = File::put(config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.$add_file_name, '');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_add_page'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ランディングページに使用する画像登録画面
	 */
	public function uploadLandingPageImg($id)
	{
		//動的クエリを生成するため
		$db_data = Landing_page::where('id',$id)->first();

		//landing_pagesテーブルに登録されている画像取得
		$list_img = [];
		if( !empty($db_data->img) ){
			$list_img = explode(",", $db_data->img);
		}

		//画面表示用配列
		$disp_data = [
			'post_url'			=> config('const.base_admin_url').'/'.config('const.lp_create_img_path').'/'.$id.'/delete',
			'img_url'			=> config('const.list_url_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path'),
			'redirect_url'		=> config('const.base_admin_url').'/'.config('const.lp_create_img_path').'/'.$id,
			'id'				=> $id,
			'list_img'			=> $list_img,
			'ver'				=> time()
		];

		return view('admin.lp.landing_page_img', $disp_data);
	}

	/*
	 * ランディングページに使用する画像を削除
	 */
	public function deleteLandingPageImg(Request $request, $id)
	{
		$this->validate($request, [
			'img'	=> 'required',
		]);

		//削除する画像を取得
		$listDelImg = $request->input('img');

		//landing_pageテーブルに登録されている画像を取得
		$listImg = [];
		$db_data = Landing_page::where('id',$id)->first();
		if( !empty($db_data->img) ){
			$listImg = explode(",", $db_data->img);
		}

		$listUpdateImg = [];
		foreach($listImg as $img){
			//DBに登録されている画像が削除リストに含まれていれば
			if( in_array($img, $listDelImg) ){
				//ディレクトリから画像削除
				system("rm ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.'/'.$img);
				continue;
			}
			//登録する画像を配列に格納
			$listUpdateImg[] = $img;
		}

		//landing_pagesテーブルを更新
		$update = Landing_page::where('id', $id)
			->update([
				'img'			=> implode(",", array_unique($listUpdateImg)),
				'updated_at'	=> Carbon::now()
			]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_delete'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadLandingPageImgUpload(Request $request)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id = $request->input('edit_id');

		//ランディングページのディレクトリ
		$landing_dir = config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id;

		$img_name = $file->getClientOriginalName();

		//
		DB::connection(Session::get('operation_select_db'))->transaction(function() use($img_name, $id){

			$listImg = [];
			$db_data = Landing_page::where('id',$id)->first();
			if( !empty($db_data->img) ){
				$listImg = explode(",", $db_data->img);
			}
			$listImg[] = $img_name;

			$update = Landing_page::where('id', $id)
				->update([
					'img'			=> implode(",", array_unique($listImg)),
					'updated_at'	=> Carbon::now()
				]);

		});

		//画像の保存先を移動(/data/www/siteo/public/ドメインフォルダ/LP)
		$file->move($landing_dir, $img_name);

			//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		return null;
	}

	/*
	 * LP新規作成画面
	 */
	public function create()
	{
		//
		$disp_data = [
			'list_open_flg'		=> config('const.admin_open_type'),
			'ver'				=> time()
		];

		return view('admin.lp.create', $disp_data); 
	}

	/*
	 * LP新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
//			'description' => 'bail|surrogate_pair_check|emoji_check|max:'.config('const.lp_memo_max_length'),
			'description' => 'bail|max:'.config('const.lp_memo_max_length'),
		]);

		$now_date = Carbon::now();

		//公開フラグ取得
		$open_flg = $request->input('open_flg');

		//landing_pagesテーブルにinsert
		$id = Landing_page::insertGetId([
			'open_flg'			=> $open_flg,
			'memo'				=> $request->input('description'),
			'sort_date'			=> $now_date,
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		]);

		//landing_pages_contentsテーブルにデフォルトデータをinsert
		//$type pc:0 mb:1 sp:2 pcsp:3
		foreach([0,2] as $type){
			foreach(config('const.lp_default_page') as $name){
				//データ登録
				$lp_content = new Landing_pages_content([
					'lp_id'				=> $id,
					'name'				=> $name,
					'type'				=> $type,
					'created_at'		=> $now_date,
					'updated_at'		=> $now_date
				]);

				//DB保存
				$lp_content->save();
			}
		}

		//ランディングページ用のフォルダを作成
		system("mkdir ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.';chown -R apache:apache '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.';chmod -R 775 '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.';');
		system("mkdir ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.'/pc;chown -R apache:apache '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.'/pc;chmod -R 775 '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.'/pc;');
		system("mkdir ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.'/sp;chown -R apache:apache '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.'/sp;chmod -R 775 '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.'/sp;');

		//シンボリックリンクを張る
		if( !empty($open_flg) ){
			system("ln -s ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.' '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/');
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * LP編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Landing_page::where('id',$id)->first();

		//landing_pagesテーブルにデータがなかったら一覧ページにリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_lp_path').'?page='.$page);
		}

		//画面表示用配列
		$disp_data = [
			'list_open_flg'		=> config('const.admin_open_type'),
			'edit_id'			=> $id,
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.lp.edit', $disp_data);
	}

	/*
	 * LP編集処理
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
//			'description'	=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.lp_memo_max_length'),
			'description'	=> 'bail|max:'.config('const.lp_memo_max_length'),
		]);

		//DBに登録されているID取得
		$id = $request->input('edit_id');

		//公開フラグ取得
		$open_flg = $request->input('open_flg');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//LPページの編集
		if( empty($request->input('del')) ){
			$update = Landing_page::where('id', $id)
				->update([
					'open_flg'		=> $open_flg,
					'memo'			=> $request->input('description'),
					'updated_at'	=> Carbon::now()
				]);

			//ランディングページの保存ディレクトリまでのフルパス
			$lp_dir_path = config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/';

			//ランディングページ用のフォルダが生成されていない場合
			if( !file_exists("{$lp_dir_path}{$id}") ){
				//ランディングページ用のフォルダを作成
				system("mkdir {$lp_dir_path}{$id};chown -R apache:apache {$lp_dir_path}{$id};chmod -R 775 {$lp_dir_path}{$id};");
			}

			//シンボリックリンクを張る
			if( !empty($open_flg) ){
				system("ln -s ".config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id.' '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/');

			//シンボリックリンクを削除
			}else{
				system("unlink ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_url_path').'/'.$id);
			}

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_update'].",{$user['login_id']}");

			return null;

		//LPページの削除
		}else{
			//landing_pageテーブルからデータ削除
			$delete = Landing_page::where('id', $id)->delete();

			//landing_pages_contentsテーブルからデータ削除
			$delete = Landing_pages_content::where('lp_id', $id)->delete();

			//シンボリックリンクを削除
			system('unlink '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.';');

			//ランディングページ用のフォルダを削除
			system('rm -rf '.config('const.project_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.landing_dir_path').'/'.$id.';');

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_delete'].",{$user['login_id']}");

			return null;
		}
	}

	/*
	 * 検索画面表示
	 */
	public function searchSetting()
	{
		//画面表示用配列
		$disp_data = [
			'session'					=> Session::all(),
			'ver'						=> time(),
			'lp_search_item'			=> config('const.lp_search_item'),
			'lp_search_like_type'		=> config('const.search_like_type'),
			'lp_disp_type'				=> config('const.lp_disp_type'),
			'sort_list'					=> config('const.lp_sort_list'),
		];

		return view('admin.lp.lp_search', $disp_data);
	}

	/*
	 * 検索処理
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Landing_page::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		//
		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.index', $disp_data);
	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Landing_page::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'base_url'			=> config('const.base_url'),
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.index', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		//landing_pages_contentsテーブルと結合
		$query->leftJoin('landing_pages_contents', 'landing_pages.id', '=', 'landing_pages_contents.lp_id');

		//取得するカラム名を指定
		$query->pluck('landing_pages.id','landing_pages.open_flg','landing_pages.memo','landing_page_contents.url_open_flg','name');
//		$query->pluck('landing_pages.id','landing_pages.open_flg','landing_pages.memo','landing_pages.open_flg');

		//landing_pages_contentテーブルのindexから検索
		$query->where('landing_pages_contents.name', 'index');
		$query->where('landing_pages_contents.type', 0);
//		$query->groupBy('landing_pages_contents.lp_id');
		$query->orderBy('landing_pages.id', 'desc');

		//検索項目
		if( !is_null(Session::get('lp_search_item_value')) ){
//			$query->where(Session::get('lp_search_item'), config('const.search_like_type')[Session::get('lp_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('lp_search_like_type')][1], Session::get('lp_search_item_value')));

			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listItem = explode(",", Session::get('lp_search_item_value'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('lp_search_item'), config('const.search_like_type')[Session::get('lp_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('lp_search_like_type')][1], $item ));
				}
			});
		}

		//公開
		if( !empty(Session::get('lp_disp_type')) ){
			$listPageType = config('const.forecast_disp_type');
			$query->where('landing_pages.open_flg', $listPageType[Session::get('lp_disp_type')][0]);
		}

		//ソート
		$sort_item = "id";
		$sort_type = "asc";
		if( !is_null(Session::get('lp_sort')) ){
			$listSortType = config('const.lp_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('lp_sort')][0]);
			$query->orderBy($sort_item, $sort_type);
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
			$db_data = $query->paginate(config('const.admin_client_list_limit'));
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
			Session::put('lp_search_item', $request->input('search_item'));
		}

		//検索の値
		Session::put('lp_search_item_value', $request->input('search_item_value'));

		//LIKE検索タイプ
		Session::put('lp_search_like_type', $request->input('search_like_type'));

		//公開
		Session::put('lp_disp_type', $request->input('disp_type'));

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('lp_sort', $request->input('sort'));
		}
	}

	/*
	 * 一括削除処理
	 */
/*
	public function bulkUpdate(Request $request)
	{

		//ID取得
		$listId = $request->input('id');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_page_delete'].",{$user['login_id']}");

		//削除にチェックが入っていれば
		if( !empty($listId) ){
			foreach($listId as $index => $id){
				if( !empty($request->input('del_flg')[$index]) ){
					$delete = Landing_page::where('id', $request->input('del_flg')[$index])->delete();
				}
			}
		}

		return null;
	}
*/
}
