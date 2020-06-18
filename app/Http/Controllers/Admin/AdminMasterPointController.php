<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Grant_point;
use App\Model\Point_category;
use App\Model\Magnification_setting;
use App\Model\Point_setting;
use DB;
use Utility;
use Carbon\Carbon;
use Session;

class AdminMasterPointController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 会員登録後の付与ポイント設定画面表示
	 */
	public function createGrantPoint()
	{
		
		//付与ポイント取得
		$db_point = Grant_point::where('type', 'registed')->first();

		if( empty($db_point) ){
			$point = 0;
		}else{
			$point = $db_point->point;
		}

		$disp_data = [
			'point'		=> $point,
			'ver'		=> time(),
		];
		
		return view('admin.master.grant_point', $disp_data);
	}

	/*
	 * 会員登録後の付与ポイント設定処理
	 */
	public function createGrantPointSend(Request $request)
	{
		$point = $request->input('point');

		//ポイントのエラーチェック
		$this->validate($request, [
			'point'	 => 'required|numeric'
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//登録されていなければinsert、登録されていればupdate
		DB::connection(Session::get('operation_select_db'))->insert("insert ignore into grant_points(type,point,created_at,updated_at) values("
		. "'registed',"
		. "{$point},"
		. "'".Carbon::now()."',"
		. "'".Carbon::now()."') "
		. "on duplicate key update "
		. "type = 'registed', "
		. "point = {$point}, "
		. "updated_at = '".Carbon::now()."';");
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_grant'].",{$user['login_id']}");
		
		return null;
	}
	
	/*
	 * 購入ポイントカテゴリ設定画面表示
	 */
	public function pointCategorySetting()
	{
		$db_data = Point_category::get();

		$slt_category = Magnification_setting::where('type', 'registed')->first();

		if( empty($slt_category) ){
			$start_date	 = '0000-00-00 00:00:00';
			$end_date	 = '0000-00-00 00:00:00';
			$default_id	 = '';
			$category_id = '';
		}else{
			$start_date	 = $slt_category->start_date;
			$end_date	 = $slt_category->end_date;
			$default_id	 = $slt_category->default_id;
			$category_id = $slt_category->category_id;
		}

		if( $start_date == '0000-00-00 00:00:00' ){
			$start_date = '';
		}

		if( $end_date == '0000-00-00 00:00:00' ){
			$end_date = '';			
		}
		
		$disp_data = [
			'default_id'	 => $default_id,
			'slt_category'	 => $category_id,
			'start_date'	 => $start_date,
			'end_date'		 => $end_date,
			'db_data'		 => $db_data,
			'ver'			 => time(),
		];
		
		return view('admin.master.purchase_point_setting', $disp_data);
	}
	
	/*
	 * 購入ポイント設定画面表示
	 */
	public function pointCategorySettingDetail($id)
	{
		//ポイントカテゴリのリストを取得
		$slt_category = Point_category::where('id', $id)->first();

		//購入ポイント設定のリストを取得
		$db_data = Point_setting::where('category_id', $id)->get();

		$disp_data = [
			'id'			 => $id,
			'slt_category'	 => $slt_category->remarks,
			'db_data'		 => $db_data,
			'ver'			 => time(),
		];
		
		return view('admin.master.point_setting_detail', $disp_data);
	}
	
	/*
	 * 購入ポイント設定の更新処理
	 */
	public function pointCategorySettingDetailSend(Request $request, $id)
	{
		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_setting_update'].",{$user['login_id']}");

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'money.*'		=> 'bail|required|numeric',
				'point.*'		=> 'bail|required|numeric',
				'disp_msg.*'	=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.pt_setting_text_max_length'),
				'remarks.*'		=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.pt_setting_remarks_max_length'),
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Point_setting::where('id', $id)->delete();

			}else{
				//ポイント設定画面の更新処理
				$update = Point_setting::where('id', $id)
					->update([
						'money'		=> $request->input('money')[$index],
						'point'		=> $request->input('point')[$index],
						'disp_msg'	=> $request->input('disp_msg')[$index],
						'remarks'	=> $request->input('remarks')[$index]
					]);
			}
		}
		
		return null;
	}
	
	/*
	 * 購入ポイント設定追加画面表示
	 */
	public function pointCategorySettingCreate($id)
	{

		$disp_data = [
			'id'			 => $id,
			'ver'			 => time(),
		];
		
		return view('admin.master.point_setting_create', $disp_data);
	}
	
	/*
	 * 購入ポイント設定の追加処理
	 */
	public function pointCategorySettingCreateSend(Request $request, $id)
	{
		//エラーチェック
		$this->validate($request, [
			'money'		 => 'bail|required|numeric',
			'point'		 => 'bail|required|numeric',
			'disp_msg'	 => 'bail|surrogate_pair_check|emoji_check|max:'.config('const.pt_setting_text_max_length'),
			'remarks'	 => 'bail|surrogate_pair_check|emoji_check|max:'.config('const.pt_setting_remarks_max_length'),
		]);

		//保存データ設定
		$db = new Point_setting([
			'category_id'	=> $id,
			'money'			=> $request->input('money'),
			'point'			=> $request->input('point'),
			'disp_msg'		=> $request->input('disp_msg'),
			'remarks'		=> $request->input('remarks')
		]);

		//DB保存
		$db->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_setting_add'].",{$user['login_id']}");

		return null;
	}
	
	/*
	 * 購入ポイントカテゴリ設定の更新処理
	 */
	public function pointCategorySettingSend(Request $request)
	{
		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_category_update'].",{$user['login_id']}");

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'category_name.*'	=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.pt_category_name_max_length'),
				'remarks.*'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.pt_category_remarks_max_length'),
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Point_category::where('id', $id)->delete();

			}else{
				//グループ管理画面の更新処理
				$update = Point_category::where('id', $id)
					->update([
						'name'		=> $request->input('category_name')[$index],
						'remarks'	=> $request->input('remarks')[$index],
					]);
			}
		}

		$this->validate($request, [
			'default_id'	=> 'required',
		]);

		//今日の日付取得
		$now_date = Carbon::now();

		//通常設定ならmagnification_settingsテーブルのdefaultカラムを更新
		DB::connection(Session::get('operation_select_db'))->insert("insert ignore into magnification_settings(type,default_id,category_id,created_at,updated_at) values("
			. "'registed',"
			. $request->input('default_id').","
			. $request->input('default_id').","
			. "'".$now_date."',"
			. "'".$now_date."') on duplicate key update "
			. "default_id = ".$request->input('default_id').","
			. "updated_at = '".$now_date."';");

		return null;
	}

	/*
	 * 購入ポイントカテゴリ追加画面表示
	 */
	public function pointCategoryCreate()
	{
				
		$disp_data = [
			'ver'		=> time(),
		];
		
		return view('admin.master.category_create', $disp_data);
	}

	/*
	 * 購入ポイントカテゴリ設定のカテゴリ追加処理
	 */
	public function pointCategoryCreateSend(Request $request)
	{
		//入力エラーチェック
		$this->validate($request, [
			'category_name'	=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.pt_category_name_max_length'),
			'remarks'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.pt_category_remarks_max_length')
		]);

		//今日の日付取得
		$now_date = Carbon::now();

		$db_value = [
			'name'		 => $request->input('category_name'),
			'remarks'	 => $request->input('remarks'),
			'created_at' => $now_date,
			'updated_at' => $now_date,
		];
/*	
		//point_categoriesテーブルにポイントカテゴリ名を追加
		$db_obj = new Point_category($db_value);

		//DB保存
		$db_obj->save();
*/
		//ポイントカテゴリ追加
		$id = Point_category::insertGetId($db_value);

		//今日の日付取得
		$now_date = Carbon::now();

		//通常設定がONのとき
		if( !empty($request->input('default')) ){
			//通常設定ならmagnification_settingsテーブルのdefaultカラムを更新
			DB::connection(Session::get('operation_select_db'))->insert("insert ignore into magnification_settings(type,default_id,category_id,created_at,updated_at) values("
				. "'registed',"
				. $id.","
				. $id.","
				. "'".$now_date."',"
				. "'".$now_date."') on duplicate key update "
				. "default_id = ".$id.","
				. "updated_at = '".$now_date."';");
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_category_add'].",{$user['login_id']}");
		
		return null;
	}

	/*
	 * 倍率設定の更新処理
	 */
	public function MagnificationSettingSend(Request $request)
	{
		//入力エラーチェック
		$this->validate($request, [
			'category_name'	=> 'required',
			'start_date'	=> 'date_format_check',
			'end_date'		=> 'date_format_check'
		]);

		//登録されていなければinsert、登録されていればupdate
		DB::connection(Session::get('operation_select_db'))->insert("insert ignore into magnification_settings(type,category_id,start_date,end_date,created_at,updated_at) values("
			. "'registed',"
			. $request->input('category_name').","
			. "'".$request->input('start_date')."',"
			. "'".$request->input('end_date')."',"
			. "'".Carbon::now()."',"
			. "'".Carbon::now()."') on duplicate key update "
			. "category_id = ".$request->input('category_name').","
			. "start_date = '".$request->input('start_date')."',"
			. "end_date = '".$request->input('end_date')."',"
			. "updated_at = '".Carbon::now()."';");

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['magnification_update'].",{$user['login_id']}");

		return null;
	}
	
	/*
	 * ログインボーナスの付与ポイント設定画面表示
	 */
	public function createGrantLoginBonusPoint()
	{
		
		//付与ポイント取得
		$db_grant_pt = Grant_point::where('type', 'loginbonus')->first();

		if( empty($db_grant_pt) ){
			$point = 0;
		}else{
			$point = $db_grant_pt->point;
		}

		$disp_data = [
			'point'		=> $point,
			'dispmsg'	=> $db_grant_pt->dispmsg,
			'disptime'	=> $db_grant_pt->disptime,
			'disp_flg'	=> $db_grant_pt->disp_flg,
			'ver'		=> time(),
		];
		
		return view('admin.master.login_bonus_point', $disp_data);
	}

	/*
	 * ログインボーナスの付与ポイント設定処理
	 */
	public function createGrantLoginBonusPointSend(Request $request)
	{
		$point = $request->input('point');

		//ポイントのエラーチェック
		$this->validate($request, [
			'point'	 => 'required|numeric'
		]);

		if( !empty($request->input('disptime')) ){
			$disptime = $request->input('disptime');
		}else{
			$disptime = 0;
		}

		if( !empty($request->input('disp_flg')) ){
			$disp_flg = $request->input('disp_flg');
		}else{
			$disp_flg = 0;
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//登録されていなければinsert、登録されていればupdate
		DB::connection(Session::get('operation_select_db'))->insert("insert ignore into grant_points(type,point,dispmsg,disptime,disp_flg,created_at,updated_at) values("
		. "'loginbonus',"
		. "{$point},"
		. "'".$request->input('dispmsg')."',"
		. $disptime.","
		. $disp_flg.","
		. "'".Carbon::now()."',"
		. "'".Carbon::now()."') "
		. "on duplicate key update "
		. "type = 'loginbonus', "
		. "point = {$point}, "
		. "dispmsg = '".$request->input('dispmsg')."', "
		. "disptime = ".$disptime.", "
		. "disp_flg = ".$disp_flg.", "
		. "updated_at = '".Carbon::now()."';");
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_grant'].",{$user['login_id']}");
		
		return null;
	}
	
}
