<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Payment_log;

class AxesMakeSendIdController extends Controller
{
	public function __construct()
	{

	}

	/*
	 *	AXESのsendidに使用するユニークなキーを生成し
	 *	payment_logsテーブルのsendidを更新する。
	 * 
	 *	AXESの決済ページへ遷移する前にユニークキーを生成し、
	 *	決済結果のsendidの戻り値として返すようにし本人が決済したのか照合させる。
	 * 
	 *	このユニークキーは開発者ツールを使用すればわかってしまい、
	 *	あまり意味を持たないかもしれないが少しでもセキュリティを高めるための実装。
	 * 
	 *	AXES側から決済結果URLへアクセスするときのURLがわかれば、
	 *	決済していないのに決済したと見せかけることが可能なため、
	 *	決済会社を通さない決済完了を防ぐ
	 * 
	 *	決済結果URLのプログラムにもAXESからのアクセスなのかの判定が必要
	 */
	public function getMakeAxesSendId(Request $request)
	{
		do{
			//ユニークなキーを生成
			$sendid = substr(sha1(uniqid(mt_rand(), true)), 0, config('const.sendid_length'));

			$exist_sendid = Payment_log::where('sendid', $sendid)->first();
		}while( !empty($exist_sendid) );

		//payment_logsテーブルのsendidを更新
		$update = Payment_log::where('order_id', $request->input('sendpoint'))->update(['sendid' => $sendid]);

		return $sendid;
	}
}
