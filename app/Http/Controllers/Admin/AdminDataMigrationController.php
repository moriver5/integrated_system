<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Migration_failed_user;
use Utility;

class AdminDataMigrationController extends Controller
{
	public function __construct()
	{

	}

	/*
	 *  
	 */
	public function index()
	{
		//会員ページのデフォルトのパラメータを取得
//		$disp_param = Utility::getDefaultDispParam();

		$db_data = Migration_failed_user::paginate(config('const.admin_client_list_limit'));

		//画面表示パラメータ設定
		$disp_data = [
			'db_data'	=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'		=> time(),
		];
		
		//画面表示
		return view('admin.migration.index', $disp_data);
	}
}
