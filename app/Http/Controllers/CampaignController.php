<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\KeibaRequest;
use App\Libs\SysLog;
use App\Libs\ClientLog;
use App\Model\Top_content;
use Utility;

class CampaignController extends Controller
{
	protected $log_obj;
	protected $pv_log_obj;
	protected $list_hit_db_data;
	protected $list_hit_data;

	public function __construct(Request $request)
	{
		$this->log_obj			 = new SysLog(config('const.client_history_log_name'), config('const.client_log_dir_path').config('const.client_member_history_file_name'));

		//PV用ログ
		$this->pv_log_obj		 = new ClientLog();
	}

	public function index($id)
	{
		//会員ページのデフォルトのパラメータを取得
		$disp_param = Utility::getDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['mem_regular']."{$id},{$disp_param['login_id']}");

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_regular']);

		//データ取得
		$db_data = Top_content::where([
			'id'		 => $id,
			'type'		 => 1,
			'open_flg'	 => 1
			])->first();

		//%変換設定で設定した文字列が含まれていれば変換を行う
		$db_data->title = Utility::getConvertData($db_data->title);
		$db_data->html_body = Utility::getConvertData($db_data->html_body);

		$body = $db_data->html_body;
		$body = preg_replace("/\-%login_id\-/", $disp_param['login_id'], $body);
		$body = preg_replace("/\-%password\-/", $disp_param['password_raw'], $body);
		$db_data->html_body = preg_replace("/\-%token\-/", $disp_param['token'], $body);

		//画面表示パラメータ設定
		$disp_data = array_merge([
			'title'			=> config('const.list_title')['mem_campaign'],
			'db_data'		=> $db_data
		],$disp_param);
		
		//画面表示
		return view('member.campaign', $disp_data);
	}

}
