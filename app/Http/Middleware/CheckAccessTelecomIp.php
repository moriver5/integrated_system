<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Model\Payment_log;

class CheckAccessTelecomIp
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
		//アクセス元IPがTELECOMからでない場合
		if( !in_array($_SERVER["REMOTE_ADDR"], config('const.telecom_remote_addr')) ){
			//会員登録前のトップ画面へリダイレクト
			return redirect(config('const.nonmember_top_path'));
		}

		//TELECOM社から決済結果を&で区切る
		$listTelecomResponse = $request->all();

		//銀行振込の場合
		if( preg_match("/telecom\/netbank\/payment\/response/", $_SERVER['REQUEST_URI']) > 0 ){
			$order_id = $listTelecomResponse['option'];

		//クレジットの場合
		}else{
			$order_id = $listTelecomResponse['sendpoint'];
		}

		//TELECOMからの決済結果で返されるsendidとsendpoint、payment_logsテーブルのstatusが3以外(未決済状態)を条件にpaymentlogsテーブルにデータがあるのか確認
		$exist_flg = Payment_log::where('sendid', $listTelecomResponse['sendid'])
			->where('order_id', $order_id)
			->where('status', '!=', config('const.settlement_result')[3])
			->first();

		//データがなければ会員登録前のトップ画面へリダイレクト
		if( empty($exist_flg) ){
			return redirect(config('const.nonmember_top_path'));			
		}

		return $next($request);
    }
}
