<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\KeibaRequest;
use App\Libs\SysLog;
use App\Libs\ClientLog;
use App\Model\Contact;
use App\Model\Top_product;
use App\Model\Payment_log;
use App\Model\Point_setting;
use App\Model\Create_order_id;
use App\Model\Magnification_setting;
use App\Model\Banner;
use App\Model\Settlement_type;
use App\Model\Mail_content;
use App\Mail\SendMail;
use Mail;
use Utility;
use DB;
use Carbon\Carbon;
use Session;

class SettlementController extends Controller
{
	private $log_obj;
	protected $pv_log_obj;
	protected $list_banner_data;

	public function __construct(Request $request)
	{
		$this->log_obj			 = new SysLog(config('const.client_history_log_name'), config('const.client_log_dir_path').config('const.client_member_history_file_name'));

		//PV用ログ
		$this->pv_log_obj		 = new ClientLog();

		//バナーデータを取得
		$this->list_banner_data = Utility::getBanner();

		//ログインボーナス取得
		list($this->login_bonus_pt, $this->login_bonus_msg, $this->login_bonus_disptime) = Utility::getLoginBonusInfo();
		
		$this->userinfo = Utility::getUserInfo();
	}
	
	/**
	 * 画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function buyProduct(Request $request)
	{
		$this->validate($request, [
			'product_id'	=> 'required'
		]);

		//会員ページのデフォルトのパラメータを取得
		$disp_param = Utility::getDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['mem_buy_bank'].",{$disp_param['login_id']}");

		//決済手段取得
		$method = $request->input('buy_method');

		//注文ID取得
		$order_id = $this->_getOrderId($request, $disp_param['login_id'], $method);

		$total	 = 0;
		$count	 = 0;
		$db_data = [];
		if( !empty($request->input('product_id')) ){
			//チェックした商品の商品IDをセッションに保存
//			Session::put('order_product_ids_'.$disp_param['login_id'], $request->input('product_id'));

			//同じ注文IDでpayment_logsテーブルにすでに登録されていれば削除
			$db_data = Payment_log::where('order_id', $order_id)->first();
			if( !empty($db_data) ){
				$delete = Payment_log::where('order_id', $order_id)->delete();
			}

			//現在時刻取得
			$now_date = Carbon::now();

			//決済会社取得
			$db_payment_agency = Settlement_type::where('active', 1)->first();

			$listProductMoney = [];
			foreach($request->input('money') as $productid_money){
				list($product_id, $money) = explode("_", $productid_money);
				$listProductMoney[$product_id] = $money;
			}

			//チェックした商品をpayment_logsテーブルに登録
//			$db_data = Top_product::whereIn('id', $request->input('product_id'))->get();
			$list_product_data = Utility::getProduct($now_date, $disp_param, $request->input('product_id'));
			$list_sort_product_data = [];
			foreach($list_product_data as $lines){
				$list_sort_product_data[$lines['id']][] = $lines;
			}
			$list_new_product_data = [];
			foreach($list_sort_product_data as $id => $lines){
				if( count($lines) > 1 ){
					$discount;
					$min_data;
					foreach ($lines as $key => $sort_lines) {
						if( $key == 0 ){
							$discount = $sort_lines['discount'];
						}
						if( $discount >= $sort_lines['discount'] ){
							$min_data = $sort_lines;
						}
					}
					$list_new_product_data[] = $min_data;
				}else{
					$list_new_product_data[] = $lines[0];
				}
			}

			$count = count($list_new_product_data);
			foreach($list_new_product_data as $lines){
				$total += $listProductMoney[$lines['id']];

				//payment_logsテーブルにデータ登録
				//決済までしないこともあるのでデフォルトのstatus:1(未決済)
				DB::insert("insert ignore into payment_logs(agency_id,pay_type,login_id,type,product_id,order_id,money,point,pay_count,status,ad_cd,sort_date,regist_date,created_at,updated_at) values("
					.$db_payment_agency->id.","
					.$method.",'"
					.$disp_param['login_id']."',0,"
					.$lines['id'].","
					.$order_id.","
					.$listProductMoney[$lines['id']].","
					.$lines['point'].","
					.$disp_param['pay_count'].",'1','"
					.$disp_param['ad_cd']."','"
					.preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $now_date)."','"
					.$now_date."','"
					.$now_date."','"
					.$now_date."');");
			}
		}

		//テレコムクレジット決済で過去に決済があれば最新のデータ１件情報取得
		$settled_data = Payment_log::where('agency_id', 1)->where('sendid', $disp_param['login_id'])->where('status', 3)->orderBy('sort_date', 'desc')->first();

		$telno = '';

		//テンプレート設定
		$template = 'member.'.config('const.list_agency_settlement_tpl')[$db_payment_agency->id][$method];

		//銀行振込
		if( $method == 1 ){

			//自動メールのデータ取得
			$db_cnt = Mail_content::where('id', 10)->first();

			//データがあれば
			if( !empty($db_cnt) ){
				//変換後の文字列を取得
				list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($db_cnt->body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

				//変換後の文字列を取得
				$body = Utility::getConvertData($body);
				$body = preg_replace("/\-%usermail\-/", $disp_param['email'], $body);
				$body = preg_replace("/\-%transfer_amount\-/", $total, $body);
				$body = preg_replace("/\-%order_date\-/", $now_date, $body);
				$body = preg_replace("/\-%order_id\-/", $order_id, $body);
				$body = preg_replace("/\-%login_id\-/", $disp_param['login_id'], $body);
				$body = preg_replace("/\-%password\-/", $disp_param['password_raw'], $body);
				$body = preg_replace("/\-%token\-/", $disp_param['token'], $body);
				$body = preg_replace("/\-%accessKey\-/", $disp_param['token'], $body);

				list($host_ip, $port) = Utility::getSmtpHost('setting');

				//送信元情報設定
				$options = [
					'client_id'	 => $disp_param['client_id'],
					'host_ip'	 => $host_ip,
					'port'		 => $port,
					'from'		 => $from_mail,
					'from_name'	 => $from_name,
					'subject'	 => $subject,
					'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.config('const.product_order'),
				];

				//送信データ設定
				$data = [
					'contents'		=> $body,
				];

				//メールアドレス変更先へメール送信
				Mail::to($disp_param['email'])->send( new SendMail($options, $data) );
			}
			$disp_name = config('const.display_list')['mem_product_buy_bank'];
			$settlement_url = '';

		//クレジット
		}elseif( $method == 2 ){
			$settlement_url = $db_payment_agency->credit_url;

			//過去の決済データがあればスピード決済
			if( !empty($settled_data) ){
				$telno = $settled_data->tel;
				$settlement_url = $db_payment_agency->speed_credit_url;
			}

			$disp_name = config('const.display_list')['mem_product_buy_credit'];

		//ネットバンク
		}elseif( $method == 3 ){
			$settlement_url = $db_payment_agency->netbank_url;
			$disp_name = config('const.display_list')['mem_product_buy_netbank'];
		}

		//PV出力
		$this->pv_log_obj->addPvLogDb($disp_name);

		$disp_data = array_merge([
			'login_bonus_flg'		=> false,
			'login_bonus_pt'		=> $this->login_bonus_pt,
			'login_bonus_msg'		=> $this->login_bonus_msg,
			'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
			'userinfo'				=> $this->userinfo,
			'payment_agency'=> $db_payment_agency,
			'site'			=> config('const.list_site_const')[$_SERVER['SERVER_NAME']],
			'title'			=> config('const.list_title')['mem_settlement_check'],
			'list_banner'	=> $this->list_banner_data,
			'pay_method'	=> $method,
			'pay_agency'	=> $db_payment_agency->name,
			'sendid'		=> $disp_param['login_id'],
			'product_id'	=> implode(",", $request->input('product_id')),
			'order_id'		=> $order_id,
			'telno'			=> $telno,
			'settlement_url'=> $settlement_url,
			'list_money'	=> $listProductMoney,
			'total'			=> $count,
			'total_amount'	=> $total,
			'db_data'		=> $list_new_product_data
		],$disp_param);

		//注文IDがクッキーに保存されていないとき
		if( empty($request->cookie(config('const.product_order_id_cookie_name'))) ){
			//注文IDを保存するクッキーを生成
			$cookie = cookie(config('const.product_order_id_cookie_name'), $order_id, config('const.product_order_cookie_life_time'));

			//クッキーを付加してクライアントへ送信
			return response()->view($template, $disp_data)->cookie($cookie);

		//注文IDがすでにクッキーに保存されているとき
		}else{
			//決済後、クッキーの有効期限内に他の商品を注文したとき同じ注文IDになるので新規の注文IDをクッキーに保存(上書き)しクライアントへ送信
			if( $request->cookie(config('const.product_order_id_cookie_name')) != $order_id ){
				//注文IDを保存するクッキーを生成
				$cookie = cookie(config('const.product_order_id_cookie_name'), $order_id, config('const.product_order_cookie_life_time'));

				//クッキーを付加してクライアントへ送信
				return response()->view($template, $disp_data)->cookie($cookie);	
			}else{
				return view($template, $disp_data);
			}
		}
	}

	/**
	 * 画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function buyPoint(Request $request)
	{
		$this->validate($request, [
			'product_id'	=> 'required'
		]);

		//会員ページのデフォルトのパラメータを取得
		$disp_param = Utility::getDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['mem_buy_bank'].",{$disp_param['login_id']}");

		//決済手段取得
		$method = $request->input('buy_method');

		//注文ID取得
		$order_id = $this->_getOrderId($request, $disp_param['login_id'], $method);

		$total	 = 0;
		$count	 = 0;
		$db_data = [];
		if( !empty($request->input('product_id')) ){

			//同じ注文IDでpayment_logsテーブルにすでに登録されていれば削除
			$db_data = Payment_log::where('order_id', $order_id)->first();
			if( !empty($db_data) ){
				$delete = Payment_log::where('order_id', $order_id)->delete();
			}

			//現在時刻取得
			$now_date = Carbon::now();

			//決済会社取得
			$db_payment_agency = Settlement_type::where('active', 1)->first();

			//倍率設定済の購入ポイント取得
			$query = Magnification_setting::query();
			$query->join('point_settings', 'magnification_settings.category_id', '=', 'point_settings.category_id');
			$query->where('magnification_settings.start_date','<=', $now_date);
			$query->where('magnification_settings.end_date', '>=', $now_date);
			$query->where('point_settings.id', $request->input('product_id'));
			$db_data = $query->get();

			//倍率設定がされていなければ通常設定のポイントを取得
			if( count($db_data) == 0 ){
				//magnification_settingsテーブルの通常設定IDを取得
				$db_data = Magnification_setting::first();
				if( !empty($db_data) ){
					//通常設定の購入ポイントを取得
					$query = Point_setting::query();
					$db_data = $query->where('category_id', $db_data->default_id)->where('id', $request->input('product_id'))->get();
				}
			}
			$count = count($db_data);

			//チェックした商品をpayment_logsテーブルに登録
			foreach($db_data as $lines){
				$total += $lines->money;

				//payment_logsテーブルにデータ登録
				//決済までしないこともあるのでデフォルトのstatus:1(未決済)
				DB::insert("insert ignore into payment_logs(agency_id,pay_type,login_id,type,product_id,order_id,money,point,pay_count,status,ad_cd,sort_date,regist_date,created_at,updated_at) values("
					.$db_payment_agency->id.","
					.$method.",'"
					.$disp_param['login_id']."',1,"
					.$lines->id.","
					.$order_id.","
					.$lines->money.","
					.$lines->point.","
					.$disp_param['pay_count'].",'1','"
					.$disp_param['ad_cd']."','"
					.preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5", $now_date)."','"
					.$now_date."','"
					.$now_date."','"
					.$now_date."');");
			}
		}

		//テレコムクレジット決済で過去に決済があれば最新のデータ１件情報取得
		$settled_data = Payment_log::where('agency_id', 1)->where('sendid', $disp_param['login_id'])->where('status', 3)->orderBy('sort_date', 'desc')->first();

		$telno = '';

		//テンプレート設定
		$template = 'member.'.config('const.list_agency_settlement_tpl')[$db_payment_agency->id][$method];

		//銀行振込
		if( $method == 1 ){
			//自動メールのデータ取得
			$db_cnt = Mail_content::where('id', 10)->first();

			//データがあれば
			if( !empty($db_cnt) ){
				//変換後の文字列を取得
				list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($db_cnt->body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

				//変換後の文字列を取得
				$body = Utility::getConvertData($body);
				$body = preg_replace("/\-%usermail\-/", $disp_param['email'], $body);
				$body = preg_replace("/\-%transfer_amount\-/", $total, $body);
				$body = preg_replace("/\-%order_date\-/", $now_date, $body);
				$body = preg_replace("/\-%order_id\-/", $order_id, $body);
				$body = preg_replace("/\-%login_id\-/", $disp_param['login_id'], $body);
				$body = preg_replace("/\-%password\-/", $disp_param['password_raw'], $body);
				$body = preg_replace("/\-%token\-/", $disp_param['token'], $body);
				$body = preg_replace("/\-%accessKey\-/", $disp_param['token'], $body);

				list($host_ip, $port) = Utility::getSmtpHost('setting');

				//送信元情報設定
				$options = [
					'client_id'	 => $disp_param['client_id'],
					'host_ip'	 => $host_ip,
					'port'		 => $port,
					'from'		 => $from_mail,
					'from_name'	 => $from_name,
					'subject'	 => $subject,
					'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.config('const.product_order'),
				];

				//送信データ設定
				$data = [
					'contents'		=> $body,
				];

				//メールアドレス変更先へメール送信
				Mail::to($disp_param['email'])->send( new SendMail($options, $data) );
			}
			$settlement_url = '';
			$disp_name = config('const.display_list')['mem_product_buy_bank'];

		//クレジット
		}elseif( $method == 2 ){
			$settlement_url = $db_payment_agency->credit_url;

			//過去の決済データがあればスピード決済
			if( !empty($settled_data) ){
				$telno = $settled_data->tel;
				$settlement_url = $db_payment_agency->speed_credit_url;
			}

			$disp_name = config('const.display_list')['mem_product_buy_credit'];

		//ネットバンク
		}elseif( $method == 3 ){
			$settlement_url = $db_payment_agency->netbank_url;
			$disp_name = config('const.display_list')['mem_product_buy_netbank'];
		}

		//PV出力
		$this->pv_log_obj->addPvLogDb($disp_name);

		$disp_data = array_merge([
			'login_bonus_flg'		=> false,
			'login_bonus_pt'		=> $this->login_bonus_pt,
			'login_bonus_msg'		=> $this->login_bonus_msg,
			'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
			'userinfo'				=> $this->userinfo,
			'payment_agency'=> $db_payment_agency,
			'site'			=> config('const.list_site_const')[$_SERVER['SERVER_NAME']],
			'title'			=> config('const.list_title')['mem_settlement_check'],
			'list_banner'	=> $this->list_banner_data,
			'pay_method'	=> $method,
			'sendid'		=> $disp_param['login_id'],
			'product_id'	=> $request->input('product_id'),
			'order_id'		=> $order_id,
			'telno'			=> $telno,
			'settlement_url'=> $settlement_url,
			'total'			=> $count,
			'total_amount'	=> $total,
			'db_data'		=> $db_data
		],$disp_param);

		//注文IDがクッキーに保存されていないとき
		if( empty($request->cookie(config('const.product_order_id_cookie_name'))) ){
			//注文IDを保存するクッキーを生成
			$cookie = cookie(config('const.product_order_id_cookie_name'), $order_id, config('const.product_order_cookie_life_time'));

			//クッキーを付加してクライアントへ送信
			return response()->view($template, $disp_data)->cookie($cookie);

		//注文IDがすでにクッキーに保存されているとき
		}else{
			//決済後、クッキーの有効期限内に他の商品を注文したとき同じ注文IDになるので新規の注文IDをクッキーに保存(上書き)しクライアントへ送信
			if( $request->cookie(config('const.product_order_id_cookie_name')) != $order_id ){
				//注文IDを保存するクッキーを生成
				$cookie = cookie(config('const.product_order_id_cookie_name'), $order_id, config('const.product_order_cookie_life_time'));

				//クッキーを付加してクライアントへ送信
				return response()->view($template, $disp_data)->cookie($cookie);	
			}else{
				return view($template, $disp_data);
			}
		}
	}

	/*
	 *	create_order_idsテーブルから注文IDを取得
	 */
	private function _getOrderId(Request $request, $login_id, $method){
		//注文IDがまだ生成されていないorネットバンク決済の場合($method:3 銀行振込はデータ取得処理に時間かかるため、その間に別の商品を注文すると決済結果を取得する前に同じ注文IDで上書きされてしますので)
		if( empty($request->cookie(config('const.product_order_id_cookie_name'))) || $method == 3 ){
			//注文IDを生成
			DB::insert("insert ignore into create_order_ids(order_id) select MAX(order_id) + 1 from create_order_ids on duplicate key update order_id = order_id + 1;");

			//注文IDを取得
			$db_data = Create_order_id::first();

			$order_id = $db_data->order_id;

		//注文IDがすでに生成されていれば
		}else{
			//クッキーから注文ID取得
			$order_id = $request->cookie(config('const.product_order_id_cookie_name'));

			//クッキーに保存されている注文IDで未完了以外になっているデータを取得
			$db_data = Payment_log::where('order_id', $order_id)
				->where('status', '!=', config('const.settlement_result')[1])
				->first();

			//データがあれば新規に注文IDを生成
			if( !empty($db_data) ){
				//注文IDを生成
				DB::insert("insert ignore into create_order_ids(order_id) select MAX(order_id) + 1 from create_order_ids on duplicate key update order_id = order_id + 1;");

				//注文IDを取得
				$db_data = Create_order_id::first();

				$order_id = $db_data->order_id;
			}

			//銀行振込決済(pay_type:3)でpayment_logsテーブルのstatusが未完了のデータを取得
			$db_data = Payment_log::where('order_id', $order_id)
				->where('pay_type', 3)
				->where('status', '=', config('const.settlement_result')[1])
				->first();

			//未完了データがある場合、Link Point決済の結果待ちの可能性があるため注文IDを新規作成
			if( !empty($db_data) ){
				//注文IDを生成
				DB::insert("insert ignore into create_order_ids(order_id) select MAX(order_id) + 1 from create_order_ids on duplicate key update order_id = order_id + 1;");

				//注文IDを取得
				$db_data = Create_order_id::first();

				$order_id = $db_data->order_id;
			}
		}
		return $order_id;
	}
}
