<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\User;
use App\Model\Melmaga_history_log;
use Auth;
use Carbon\Carbon;

class ViewMelmagaUpdate
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
		//認証済でないとき
		if( !Auth::guard('user')->check() ){
			//akパラメータ(アクセストークン)に値があれば
			if( !empty($request->input('ak')) ){
				$user = User::where([
					'remember_token'	=> $request->input('ak'),
					'status'			=> '1',
					'disable'			=> 0
				])->first();
			}

		//認証済のとき
		}else{
			//認証情報取得
			$user = \Auth::guard('user')->user();
		}

		//現在日時取得
		$now_date = Carbon::now();

		//ユーザーデータとメルマガIDが存在すれば
		if( !empty($user) && !empty($request->input('mid')) ){
			$exist_data = Melmaga_history_log::whereNull('first_view_datetime')->where('melmaga_id', $request->input('mid'))->where('client_id', $user->id)->first();

			//メルマガから初めて閲覧
			if( !empty($exist_data) ){
				$update	 = Melmaga_history_log::where('melmaga_id', $request->input('mid'))
					->where('client_id', $user->id)
					->update([
					'first_view_datetime'	 => $now_date, 
					'read_flg'				 => 1
				]);

			//メルマガから２回目以上の閲覧
			}else{
				$update	 = Melmaga_history_log::where('melmaga_id', $request->input('mid'))
					->where('client_id', $user->id)
					->update([
					'updated_at' => $now_date
				]);				
			}
		}

		return $next($request);
    }
}
