<?php

namespace App\Http\Middleware;

use Closure;

class CheckAllowIp
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
		//管理画面のドメイン以外のアクセスなら
		if( preg_match("/admin\-k\.work/", $_SERVER['HTTP_HOST']) == 0 ){
			return redirect(config('const.nonmember_top_path'));
		}

		//許可IPでなければトップページへリダイレクト
		if( !preg_match("/^\d+$/", array_search($request->ip(), config("const.admin_access_allow_ip"))) ){
			return redirect(config('const.nonmember_top_path'));
		}

        return $next($request);
    }
}
