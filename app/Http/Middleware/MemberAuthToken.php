<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Libs\ClientLog;
use Auth;

class MemberAuthToken extends Controller
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		//認証済チェック
		if( Auth::guard('user')->check() ){
			if( empty($user) ){
				//認証情報取得
				$user = \Auth::guard('user')->user();
			}

			//ステータスが会員以外なら(管理画面から本登録→仮登録にすると仮登録でもログインできてしまうための対応)
			if( $user->status != 1 ){
				Auth::guard('user')->logout();

				//ログインID・パスワードでも認証前なので会員登録前のトップ画面へリダイレクト
				return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : config('const.nonmember_top_path'));
			}

			$pay_type = 0;
			if( $user->pay_count > 0 ){
				$pay_type = 1;
			}

			//ログ出力(access_logテーブル)
			$client_log_obj	 = new ClientLog();

			//access_logsテーブルへログ出力
			$client_log_obj->addLogDb($user->login_id, $pay_type);

			//day_pv_logsテーブルへログ出力
			$client_log_obj->addAdLogDb($user->ad_cd, $user->login_id);

			//personal_access_logsテーブルへログ出力(アクセス履歴)
			$client_log_obj->addPersonalAccessLogDb($user->id, $request->input('mid'));

			//認証済なら会員ページへ
			return $next($request);
		}

		//DBデータ格納用変数
		$db_data = null;

		//リクエストパラメーターからトークン取得
		$sid = $request->route()->parameter('sid');

		//トークンがあればトークンを条件にDBテーブルからデータ検索
		if( !empty($sid) ){
			$db_data = User::where([
				'remember_token'	=> $sid,
				'status'			=> '1',
				'disable'			=> 0
			])->first();
		}else{
			//akパラメータ(アクセストークン)に値があれば
			if( !empty($request->input('ak')) ){
				$db_data = User::where([
					'remember_token'	=> $request->input('ak'),
					'status'			=> '1',
					'disable'			=> 0
				])->first();

				//ログ出力(access_logテーブル)
				$client_log_obj	 = new ClientLog();

				//personal_access_logsテーブルへログ出力(アクセス履歴)
				$client_log_obj->addPersonalAccessLogDb($db_data->id, $request->input('mid'));
			}
		}
			
		//登録データがなければ
		if( empty($db_data) ){
			//ログインID・パスワードでも認証前なので会員登録前のトップ画面へリダイレクト
			return redirect(config('const.nonmember_top_path'));
		}

		//トークンが登録されていたらlaravelの機能を使用し主キー(usersテーブルのid)でログイン処理
		//loginUsingId(usersテーブルの主キー, 継続的ログインにする場合はtrue)
		Auth::guard('user')->loginUsingId($db_data->id, true);

		//認証情報取得
		$user = \Auth::guard('user')->user();
		
		//ログオブジェクト
		$log_obj = new SysLog(config('const.client_history_log_name'), config('const.client_log_dir_path').config('const.client_member_history_file_name'));
		
		//ログ出力(初回の簡単ログイン)
		$log_obj->addLog(config('const.display_list')['simple_login'].",{$user->login_id}");
		
		//ログ出力(access_logテーブル)
		$client_log_obj	 = new ClientLog();
		$pay_type = 0;
		if( $user->pay_count > 0 ){
			$pay_type = 1;
		}
		//access_logsテーブルへログ出力
		$client_log_obj->addLogDb($user->login_id, $pay_type);

		//PV用ログ
		$this->pv_log_obj = new ClientLog();

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_top']);

		//認証済なら会員ページへ
		return $next($request);
	}
}
