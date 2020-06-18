<?php

namespace App\Http\Middleware;

use App\Model\Admin;
use Closure;
use Session;

class CheckSelectSite
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
		//認証情報取得
		$user = \Auth::guard('admin')->user();


		//選択DBがセッションに保存されていない場合
		if( empty(Session::get('operation_select_db')) ){

			//選択DBをセッションに保存
			Session::put('operation_select_db', $user->select_db);		
		}

		//選択中のDB名がoperation_dbsテーブルに登録されているデータを取得
		$db_data = Admin::leftJoin('operation_dbs', 'operation_dbs.db', 'admins.select_db')
			->where('admins.email', $user->email)
			->where('operation_dbs.db', $user->select_db)
			->first();

		//データがない場合、サイト選択画面へリダイレクト
		if( empty($db_data->select_db) ){
			//「選択中のDB名がない」または「operation_dbsテーブルに存在しないDB名が選択されている」状態でサイト選択関連のURIはスキップ(スキップしないとリダイレクトが繰り返されるため)
			if( preg_match("/\/admin\/member\/site\/select/", $_SERVER['REQUEST_URI']) > 0 ){
				return $next($request);
			}
			return redirect('/admin/member/site/select');
		}

		//選択中のサイト名をセッションに登録
		Session::put('operation_db_name', $db_data->name);

        return $next($request);
    }
}
