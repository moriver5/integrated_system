<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Email_ng_word;
use Utility;
use DB;
use Carbon\Carbon;
use Session;

class AdminMasterMailAddressNgWordController extends Controller
{
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 *  メールアドレス禁止ワード設定画面表示
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = Email_ng_word::where('type', 'mail')->first();

		$disp_data = [
			'ng_word'	=> !empty($db_data->word) ? preg_replace("/,/", "\n", $db_data->word):'',
			'ver'		=> time(),
		];
		
		return view('admin.master.ng_word.index', $disp_data);
	}

	/*
	 *  メールアドレス禁止ワードの更新処理
	 */
	public function store(Request $request)
	{
		//エラーチェック
		$this->validate($request, [
			'ng_word'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['ng_word_update'].",{$user['login_id']}");

		$word = trim(preg_replace("/\r\n/", ",", addslashes($request->input('ng_word'))));

		//禁止ワード更新処理
		DB::connection(Session::get('operation_select_db'))->insert("insert ignore into email_ng_words("
			. "type, "
			. "word, "
			. "created_at, "
			. "updated_at) "
			. "values("
			. "'mail', "
			. "'".$word."', "
			. "'".Carbon::now()."', "
			. "'".Carbon::now()."') "
			. "on duplicate key update "
			. "word = '{$word}';");

		return null;
	}
}
