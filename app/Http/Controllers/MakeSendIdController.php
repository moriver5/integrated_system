<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Payment_log;
use App\Model\Settlement_type;

class MakeSendIdController extends Controller
{
	public function __construct()
	{

	}

	/*
	 *	Telecomのsendidに使用するユニークなキーを生成し
	 *	payment_logsテーブルのsendidを更新する。
	 * 
	 *	Telecomの決済ページへ遷移する前にユニークキーを生成し、
	 *	決済結果のsendidの戻り値として返すようにし本人が決済したのか照合させる。
	 * 
	 *	このユニークキーは開発者ツールを使用すればわかってしまい、
	 *	あまり意味を持たないかもしれないが少しでもセキュリティを高めるための実装。
	 * 
	 *	Telecom側から決済結果URLへアクセスするときのURLがわかれば、
	 *	決済していないのに決済したと見せかけることが可能なため、
	 *	決済会社を通さない決済完了を防ぐ
	 * 
	 *	決済結果URLのプログラムにもTelecomからのアクセスなのかの判定が必要
	 */
	public function getMakeSendId(Request $request)
	{
/*
		$db_payment = Payment_log::join('settlement_types', 'settlement_types.id', '=', 'payment_logs.agency_id')->where('order_id', $request->input('sendpoint'))->first();

		do{
			//ユニークなキーを生成(各決済会社でsendidの長さを設定)
			$sendid = substr(sha1(uniqid(mt_rand(), true)), 0, $db_payment->sendid_length);

			$exist_sendid = Payment_log::where('sendid', $sendid)->first();
		}while( !empty($exist_sendid) );
*/
		$pay_agency = config('const.list_pay_agency')[2];

		//payment_logsテーブルのsendidを更新
		//Telecomのネットバンク
		if( $request->input('act') == 'netbank' && $request->input('pay_agency') == $pay_agency ){
			$update = Payment_log::where('order_id', $request->input('option'))->update(['sendid' => $request->input('sendid')]);

		}else{
			$update = Payment_log::where('order_id', $request->input('sendpoint'))->update(['sendid' => $request->input('sendid')]);
		}

		return $request->input('sendid');
	}
}
