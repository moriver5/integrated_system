<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libs\ClientLog;
use Utility;
use Carbon\Carbon;
use DB;

class redirectLandingPageController extends Controller
{
	protected $pv_log_obj;
	
	public function __construct(Request $request)
	{
		//PV用ログ
		$this->pv_log_obj		 = new ClientLog();
	}

	/*
	 * ランディングページへリダイレクト
	 */
	public function index($id)
	{
		if( empty($id) ){
			//トップページへリダイレクト
			return redirect(config('const.base_url'));			
		}

		//パラメータ取得(QUERY_STRING)
		$query_string = '';
		if( !empty($_SERVER['REDIRECT_QUERY_STRING']) ){
			$query_string = '?'.$_SERVER['REDIRECT_QUERY_STRING'];
		}

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['landing_page'].$_SERVER['REDIRECT_URL'].$query_string);

		//ランディングページへリダイレクト
		return redirect(config('const.base_url').'/'.config('const.landing_url_path').'/'.$id);
	}
}
