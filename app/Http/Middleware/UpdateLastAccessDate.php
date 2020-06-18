<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\User;
use Carbon\Carbon;

class UpdateLastAccessDate
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
		$user = \Auth::guard('user')->user();

		$now_date = Carbon::now();

		//アクション回数インクリメント
		$user->action++;

		//顧客の最終アクセス日時を更新
		$update	 = User::where('login_id', $user->login_id)
			->update([
				'action'					 => $user->action,
				'last_access_datetime'		 => $now_date,
				'sort_last_access_datetime'	 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $now_date)
			]);
		
		return $next($request);
	}
}
