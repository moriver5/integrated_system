<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Model\Top_product;
use App\Model\Payment_log;
use App\Model\Magnification_setting;
use App\Model\Point_setting;
use Carbon\Carbon;
use Cookie;
use Validator;

class SettlementSendTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBuySend()
    {
		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→会員情報変更→パスワード変更送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/settlement/buy';
		$response = $this->actingAs($user);

		echo "_/_/_/_/_/_/キャンペーン情報_/_/_/_/_/_/\n";
		echo "クレジット決済の正常系テスト→";

		//現在時刻取得
		$now_date = Carbon::now();

		//payment_logsテーブルから決済済以外のproduct_idを取得
		$sub_query = Payment_log::query()
			->select('product_id')
			->where('login_id', $user->login_id)
			->where('status', '=', config('const.settlement_result')[3])
			->distinct()
			->get();

		//1度購入した商品を省いた商品を取得
		$query = Top_product::query();
		$db_data = $query->where('open_flg', 1)
			->leftJoin('payment_logs', 'payment_logs.product_id', '=', 'top_products.id')
			->select('top_products.*')
			->where('top_products.sort_start_date', '<=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->where('top_products.sort_end_date', '>=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->where('top_products.money', '>', 0)
			->whereNotIn('top_products.id', $sub_query)
			->orderBy('top_products.order_num', 'asc')
			->orderBy('top_products.id', 'asc')
			->distinct()
			->get();

		//送信データ
		$send_data = [
			'product_id'	=> [],
			'buy_method'	=> 2,			
		];

		if( count($db_data) > 0 ){
			$listProductId = [];
			foreach($db_data as $lines){
				$listProductId[] = $lines->id;
			}
			$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		}

		//クレジット決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("status", 1)
					->get();

		$order_id = "";
		$payment_id = "";
		foreach($db_data as $lines){
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "クレジット決済の注文し直し1正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("status", 1)
					->get();

		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}else{
				echo "\033[1;35mNG\033[0m\n";
			}
		}

		echo "クレジット決済の注文し直し2正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("status", 1)
					->get();

		$total_amount = 0;
		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}else{
				echo "\033[1;35mNG\033[0m\n";
			}
		}

		echo "クレジット決済の決済画面へ正常系テスト→";

		//送信データ
		$send_pay_data = [
			'clientip'	=> config('const.credit_client_ip'),
			'money'	=> $total_amount,
			'email'	=> $user->mail_address,
			'sendid'	=> "",
			'sendpoint'	=> $order_id,
			'success_url'	=> config('const.settlement_success_link_url'),
			'success_str'	=> mb_convert_encoding(config('const.axes_success_link_text'), 'SJIS-win', 'UTF-8'),
			'failure_url'	=> config('const.axes_failure_link_url'),
			'failure_str'	=> mb_convert_encoding(config('const.axes_failure_link_text'), 'SJIS-win', 'UTF-8'),
		];

		//クレジット決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/send', $send_pay_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("status", 1)
					->where("sendid", $send_response->baseResponse->original)
					->get();

		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}else{
				echo "\033[1;35mNG\033[0m\n";
			}
		}

		//ネットバンク決済
		echo "ネットバンク決済の正常系テスト→";

		//送信データ
		$send_data = [
			'product_id'	=> [],
			'buy_method'	=> 3,			
		];

		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];

		//ネットバンク決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("status", 1)
					->get();

		$order_id = "";
		$payment_id = "";
		foreach($db_data as $lines){
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "ネットバンク決済の注文し直し1正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("status", 1)
					->get();

		$order_id1 = null;
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				if( $order_id == $lines->order_id ){
					continue;
				}
				$order_id1 = $lines->order_id;
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id1}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "ネットバンク決済の注文し直し2正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("status", 1)
					->get();

		$order_id2 = null;
		$total_amount = 0;
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				if( $order_id == $lines->order_id || 
					$order_id1 == $lines->order_id || 
					$order_id2 == $lines->order_id ){
					continue;
				}
				$order_id2 = $lines->order_id;
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$lines->order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "ネットバンク決済の決済画面へ正常系テスト→";

		//送信データ
		$send_pay_data = [
			'clientip'	=> config('const.credit_client_ip'),
			'money'	=> $total_amount,
			'email'	=> $user->mail_address,
			'sendid'	=> "",
			'sendpoint'	=> $order_id2,
			'success_url'	=> config('const.settlement_success_link_url'),
			'success_str'	=> mb_convert_encoding(config('const.axes_success_link_text'), 'SJIS-win', 'UTF-8'),
			'failure_url'	=> config('const.axes_failure_link_url'),
			'failure_str'	=> mb_convert_encoding(config('const.axes_failure_link_text'), 'SJIS-win', 'UTF-8'),
		];

		//クレジット決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/send', $send_pay_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("status", 1)
					->where("sendid", $send_response->baseResponse->original)
					->get();

		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$lines->order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}


		echo "\n_/_/_/_/_/_/ポイント購入_/_/_/_/_/_/\n";
		echo "クレジット決済の正常系テスト→";

		//現在時刻取得
		$now_date = Carbon::now();

		$query = Magnification_setting::query();
		$query->join('point_settings', 'magnification_settings.category_id', '=', 'point_settings.category_id');
		$query->where('magnification_settings.start_date','<=', $now_date);
		$query->where('magnification_settings.end_date', '>=', $now_date);
		$db_data = $query->get();

		//倍率設定がされていなければ通常設定のポイントを取得
		if( count($db_data) == 0 ){
			//magnification_settingsテーブルの通常設定IDを取得
			$db_data = Magnification_setting::first();
			if( !empty($db_data) ){
				//通常設定の購入ポイントを取得
				$query = Point_setting::query();
				$db_data = $query->where('category_id', $db_data->default_id)->get();
			}
		}

		//送信データ
		$send_data = [
			'product_id'	=> [],
			'buy_method'	=> 2,			
		];

		if( count($db_data) > 0 ){
			$listProductId = [];
			foreach($db_data as $lines){
				$listProductId[] = $lines->id;
			}
			$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		}

		//クレジット決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/point', $send_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 1)
					->where("status", 1)
					->get();

		$order_id = "";
		$payment_id = "";
		foreach($db_data as $lines){
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "クレジット決済の注文し直し1正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy/point', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 1)
					->where("status", 1)
					->get();

		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}else{
				echo "\033[1;35mNG\033[0m\n";
			}
		}

		echo "クレジット決済の注文し直し2正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy/point', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 1)
					->where("status", 1)
					->get();

		$total_amount = 0;
		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}else{
				echo "\033[1;35mNG\033[0m\n";
			}
		}

		echo "クレジット決済の決済画面へ正常系テスト→";

		//送信データ
		$send_pay_data = [
			'clientip'	=> config('const.credit_client_ip'),
			'money'	=> $total_amount,
			'email'	=> $user->mail_address,
			'sendid'	=> "",
			'sendpoint'	=> $order_id,
			'success_url'	=> config('const.settlement_success_link_url'),
			'success_str'	=> mb_convert_encoding(config('const.axes_success_link_text'), 'SJIS-win', 'UTF-8'),
			'failure_url'	=> config('const.axes_failure_link_url'),
			'failure_str'	=> mb_convert_encoding(config('const.axes_failure_link_text'), 'SJIS-win', 'UTF-8'),
		];

		//クレジット決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/send', $send_pay_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 1)
					->where("status", 1)
					->where("sendid", $send_response->baseResponse->original)
					->get();

		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}else{
				echo "\033[1;35mNG\033[0m\n";
			}
		}

		//ネットバンク決済
		echo "ネットバンク決済の正常系テスト→";

		//送信データ
		$send_data = [
			'product_id'	=> [],
			'buy_method'	=> 3,			
		];

		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];

		//ネットバンク決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/point', $send_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 1)
					->where("status", 1)
					->get();

		$order_id = "";
		$payment_id = "";
		foreach($db_data as $lines){
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "ネットバンク決済の注文し直し1正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy/point', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 1)
					->where("status", 1)
					->get();

		$order_id1 = null;
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				$order_id1 = $lines->order_id;
				echo "PAYID：{$lines->payment_id}→注文ID：{$order_id1}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "ネットバンク決済の注文し直し2正常系テスト→";

		//クレジット決済の正常系テスト→注文し直し
		$send_data['product_id'] = [];
		$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		$send_response = $this->call('POST', '/member/settlement/buy/point', $send_data, $cookies)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 1)
					->where("status", 1)
					->get();

		$order_id2 = null;
		$total_amount = 0;
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				if( $order_id1 == $lines->order_id || 
					$order_id2 == $lines->order_id ){
					continue;
				}
				$order_id2 = $lines->order_id;
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$lines->order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		echo "ネットバンク決済の決済画面へ正常系テスト→";

		//送信データ
		$send_pay_data = [
			'clientip'	=> config('const.credit_client_ip'),
			'money'	=> $total_amount,
			'email'	=> $user->mail_address,
			'sendid'	=> "",
			'sendpoint'	=> $order_id2,
			'success_url'	=> config('const.settlement_success_link_url'),
			'success_str'	=> mb_convert_encoding(config('const.axes_success_link_text'), 'SJIS-win', 'UTF-8'),
			'failure_url'	=> config('const.axes_failure_link_url'),
			'failure_str'	=> mb_convert_encoding(config('const.axes_failure_link_text'), 'SJIS-win', 'UTF-8'),
		];

		//クレジット決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/send', $send_pay_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 1)
					->where("status", 1)
					->where("sendid", $send_response->baseResponse->original)
					->get();

		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				$total_amount = $lines->money;
				echo "PAYID：{$lines->payment_id}→注文ID：{$lines->order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		//パスワード変更送信時に使用されているバリデーションのみのテスト
		$request = new Request;
		$request->merge([
			'product_id' => ""
		]);

		$validator = Validator::make($request->all(), [
			'product_id' => 'required'
		]);

		echo "異常系テスト→商品未選択→";
		if( $validator->errors()->has("product_id") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}
    }
}
