<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Tipster;
use DB;
use Session;
use Utility;
use File;
use Carbon\Carbon;

class AdminMasterTipsterController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 予想師設定一覧
	 */
	public function index(Request $request)
	{
		$db_data = Tipster::paginate(config('const.admin_client_list_limit'));
		
		$disp_data = [
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'db_data'		=> $db_data,
			'ver'			=> time(),
		];
		
		return view('admin.master.tipster.index', $disp_data);
	}

	/*
	 * 予想師設定-新規作成
	 */
	public function create(Request $request, $id = null)
	{
		$db_data = [];
		if( !empty($id) ){
			$db_data = Tipster::where('id', $id)->first();
		}

		$disp_data = [
			'select_domain'	=> config('const.list_domain_const')[Session::get('operation_select_db')],
			'edit_id'		=> $id,
			'db_data'		=> $db_data,
			'redirect_url'	=> config('const.base_admin_url').'/'.config('const.tipster_create_url_path'),
			'ver'			=> time(),
		];

		return view('admin.master.tipster.create', $disp_data);
	}

	/*
	 * 予想師設定-作成処理
	 */
	public function createSend(Request $request)
	{
		$db_value = [
			'name'		 => $request->input('name'),
			'contents'	 => $request->input('contents')
		];

		$db_value['disp_flg'] = 0;
		if( !empty($request->input('disp_flg')) ){
			$db_value['disp_flg'] = $request->input('disp_flg');
		}

		$db_value['is_star'] = 0;
		if( !empty($request->input('is_star')) ){
			$db_value['is_star'] = $request->input('is_star');
		}

		if( empty($request->input('edit_id')) ){
			$tipster = new Tipster($db_value);		
			$tipster->save();
		}else{
			$update = Tipster::where('id', $request->input('edit_id'))->update($db_value);
		}
		
		return null;
	}

	/*
	 * 予想師設定-編集
	 */
	public function edit($edit_id)
	{
		$db_data = Tipster::where("id", $edit_id)->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').'/'.config('const.tipster_top'));
		}

		$disp_data = [
			'select_domain'	=> config('const.list_domain_const')[Session::get('operation_select_db')],
			'edit_id'		=> $edit_id,
			'db_data'		=> $db_data,
			'redirect_url'	=> config('const.base_admin_url').'/'.config('const.tipster_edit_url_path'),
			'ver'		=> time(),
		];

		return view('admin.master.tipster.edit', $disp_data);
	}

	/*
	 * 予想師設定-編集処理
	 */
	public function store(Request $request, $edit_id)
	{
		//削除
		if( $request->input('is_del') == 1 ){
			//DBから削除
			$delete = Tipster::where("id", $edit_id)->delete();
			
			//画像削除
			$imgpath = config('const.public_full_path').config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.tipster_images_path');
			File::delete($imgpath.'/'.$edit_id.'_sale.png', $imgpath.'/'.$edit_id.'_hit.png', $imgpath.'/'.$edit_id.'_intro.png');

			return null;
		}

		$update_value = [
			'name'		 => $request->input('name'),
			'contents'	 => $request->input('contents')
		];

		$update_value['disp_flg'] = 0;
		if( !empty($request->input('disp_flg')) ){
			$update_value['disp_flg'] = $request->input('disp_flg');
		}

		$update_value['is_star'] = 0;
		if( !empty($request->input('is_star')) ){
			$update_value['is_star'] = $request->input('is_star');
		}

		$update = Tipster::where("id", $edit_id)->update($update_value);
		
		return null;
	}

	/*
	 * 予想師-画像アップロード処理
	 */
	public function uploadImageSend(Request $request)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//top_contentsテーブルに登録されているidを取得
		$id = $request->input('edit_id');
		
		//アップロード画像のタイプ
		$type = $request->input('type');

		//画像名をtop_contentsテーブルにinsert
		DB::connection(Session::get('operation_select_db'))->transaction(function() use($type, $file, &$id){

			//top_contentsテーブルにまだ登録されていないとき
			if( is_null($id) ){
				//最初の１回目の画像アップロードはinsert
				$id = Tipster::insertGetId(['disp_flg' => 0]);
			}

			$imgpath = config('const.public_full_path').config('const.list_domain_const')[Session::get('operation_select_db')].'/'.config('const.tipster_images_path');
			$imgfile = $id.'_'.$type.'.'.$file->getClientOriginalExtension();

//			$dest_imgpath = config('const.public_full_path').config('const.tipster_images_path');

			//画像の保存先を移動(/data/www/siteo/public/ドメインフォルダ/images/top_content)
			$file->move($imgpath, $imgfile);

//			File::copy($imgpath.'/'.$imgfile, $dest_imgpath.'/'.$imgfile);
		});

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['top_banner_upload'].",{$user['login_id']}");

		//失敗
		if( is_null($id) ){
			return false;

		//画像アップロード成功
		}else{
			return $id;
		}
	}
}
