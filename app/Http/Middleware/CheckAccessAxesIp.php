<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Model\Payment_log;

class CheckAccessAxesIp
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
		//アクセス元IPがAXESからでない場合
		if( !in_array($_SERVER["REMOTE_ADDR"], config('const.axes_remote_addr')) ){
			//会員登録前のトップ画面へリダイレクト
			return redirect(config('const.nonmember_top_path'));
		}

		//Axes社から決済結果を&で区切る
		$listAxesResponse = $request->all();

		//AXESからの決済結果で返されるsendidとsendpoint、payment_logsテーブルのstatusが3以外(未決済状態)を条件にpaymentlogsテーブルにデータがあるのか確認
		$exist_flg = Payment_log::where('sendid', $listAxesResponse['sendid'])
			->where('order_id', $listAxesResponse['sendpoint'])
			->where('status', '!=', config('const.settlement_result')[3])
			->first();

		//データがなければ会員登録前のトップ画面へリダイレクト
		if( empty($exist_flg) ){
			return redirect(config('const.nonmember_top_path'));			
		}

		return $next($request);
    }
}
