<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Model\Top_product;
use App\Model\Payment_log;
use App\Model\User;
use App\Model\Magnification_setting;
use App\Model\Point_setting;
use Carbon\Carbon;
use Cookie;
use Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;

class AxesPaymentResponseTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAxesPaymentResponse()
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
		$total_amount = 0;
		foreach($db_data as $lines){
			$total_amount = $lines->money;
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "商品選択→PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "商品選択→\033[1;35mNG\033[0m\n";
		}

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

		$sendid = null;
		$point = 0;
		$status = 1;
		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$status = $lines->status;
				$point = $lines->point;
				$total_amount = $lines->money;
				$sendid = $lines->sendid;
				echo "Axes決済画面→PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}else{
				echo "Axes決済画面→\033[1;35mNG\033[0m\n";
			}
		}

//		echo "レスポンスURL：".config("const.base_url")."/axes/credit/payment/response?clientip=1011004040&money=".$total_amount."&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok\n";

		//Axesからのレスポンスを想定したアクセス
		$send_response = $this->call('GET', "/axes/credit/payment/response?clientip=1011004040&money=".$total_amount."&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok");

		$db_data = User::where("login_id", $user->login_id)->first();
		$db_pay_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("sendid", $sendid)
					->first();

		echo "Axesクレジット決済結果→";
	
		if( $db_data->pay_amount == $total_amount && 
			$db_data->point == $point && 
			$db_pay_data->status == 3 ){
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$db_pay_data->status]."→\033[0;34mOK\033[0m\n";			
		}else{
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$status]."→\033[1;35mNG\033[0m\n";			
		}

		echo "Axesクレジット決済結果の重複→";

		//Axesからのレスポンスを想定したアクセス
		$send_response = $this->call('GET', "/axes/credit/payment/response?clientip=1011004040&money=".$total_amount."&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok");

		$db_data = User::where("login_id", $user->login_id)->first();
		$db_pay_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("sendid", $sendid)
					->first();

		if( $db_data->pay_amount == $total_amount && 
			$db_data->point == $point && 
			$db_pay_data->status == 3 ){
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$db_pay_data->status]."→\033[0;34mOK\033[0m\n";			
		}else{
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$status]."→\033[1;35mNG\033[0m\n";			
		}

//		echo "レスポンスURL：".config("const.base_url")."/axes/credit/payment/response?clientip=1011004040&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok\n";

		echo "Axesクレジット決済結果NG→トップページへリダイレクト→\033[0;34mOK\033[0m";

		$response = $this->call('GET', "/axes/credit/payment/response?clientip=1011004040&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok")->assertRedirect(config("const.base_url"));




		$user = factory(\App\Model\User::class)->create();
		echo "\n\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→会員情報変更→パスワード変更送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/settlement/buy';
		$response = $this->actingAs($user);

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

		//クレジット決済の異常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy', $send_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 2)
					->where("type", 0)
					->where("status", 1)
					->get();

		$order_id = "";
		$payment_id = "";
		$total_amount = 0;
		foreach($db_data as $lines){
			$total_amount = $lines->money;
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "商品選択→PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "商品選択→\033[1;35mNG\033[0m\n";
		}

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

		$sendid = null;
		$point = 0;
		$status = 1;
		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$status = $lines->status;
				$point = $lines->point;
				$total_amount = $lines->money;
				$sendid = $lines->sendid;
				echo "Axes決済画面→PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}else{
				echo "Axes決済画面→\033[1;35mNG\033[0m\n";
			}
		}

//		echo "レスポンスURL：".config("const.base_url")."/axes/credit/payment/response?clientip=1011004040&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok\n";

		//Axesからのレスポンスを想定したアクセス
		$send_response = $this->call('GET', "/axes/credit/payment/response?clientip=1011004040&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok");
//echo print_r($send_response->baseResponse->original, true);
		echo "Axesクレジット決済結果NG→";

		if( $send_response->baseResponse->original == "ng" ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}




		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→会員情報変更→パスワード変更送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/settlement/buy';
		$response = $this->actingAs($user);

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
			'buy_method'	=> 3,			
		];

		if( count($db_data) > 0 ){
			$listProductId = [];
			foreach($db_data as $lines){
				$listProductId[] = $lines->id;
			}
			$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		}

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
		$total_amount = 0;
		foreach($db_data as $lines){
			$total_amount = $lines->money;
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "商品選択→PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "商品選択→\033[1;35mNG\033[0m\n";
		}

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

		//ネットバンク決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/send', $send_pay_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("status", 1)
					->where("sendid", $send_response->baseResponse->original)
					->get();

		$sendid = null;
		$point = 0;
		$status = 1;
		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$status = $lines->status;
				$point = $lines->point;
				$total_amount = $lines->money;
				$sendid = $lines->sendid;
				echo "Axes決済画面→PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}else{
				echo "Axes決済画面→\033[1;35mNG\033[0m\n";
			}
		}

//		echo "レスポンスURL：".config("const.base_url")."/axes/credit/payment/response?clientip=1011004040&money=".$total_amount."&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok\n";

		//Axesからのレスポンスを想定したアクセス
		$send_response = $this->call('GET', "/axes/netbank/payment/response?clientip=1081001719&money=".$total_amount."&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&order_no=".time()."%2d817194024664%2d429984&tracking_no=1080280519269830&status=03&error_message=%2d&payment=01")->assertStatus(200);

		$db_data = User::where("login_id", $user->login_id)->first();
		$db_pay_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("sendid", $sendid)
					->first();

		echo "Axesネットバンク決済結果→";
	
		if( $db_data->pay_amount == $total_amount && 
			$db_data->point == $point && 
			$db_pay_data->status == 3 ){
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$db_pay_data->status]."→\033[0;34mOK\033[0m\n";			
		}else{
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$status]."→\033[1;35mNG\033[0m\n";			
		}

		echo "Axesネットバンク決済結果の重複→";

		//Axesからのレスポンスを想定したアクセス
		$send_response = $this->call('GET', "/axes/netbank/payment/response?clientip=1081001719&money=".$total_amount."&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&order_no=".time()."%2d817194024664%2d429984&tracking_no=1080280519269830&status=03&error_message=%2d&payment=01")->assertRedirect(config("const.base_url"));

		$db_data = User::where("login_id", $user->login_id)->first();
		$db_pay_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("sendid", $sendid)
					->first();

		if( $db_data->pay_amount == $total_amount && 
			$db_data->point == $point && 
			$db_pay_data->status == 3 ){
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$db_pay_data->status]."→\033[0;34mOK\033[0m\n";			
		}else{
			echo "合計：{$total_amount}円→ポイント：{$point}→決済ステータス：".config('const.list_history_pay_status')[$status]."→\033[1;35mNG\033[0m\n";			
		}

//		echo "レスポンスURL：".config("const.base_url")."/axes/credit/payment/response?clientip=1011004040&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&result=ok\n";

		echo "Axesネットバンク決済結果NG→トップページへリダイレクト→\033[0;34mOK\033[0m";

		$response = $this->call('GET', "/axes/netbank/payment/response?clientip=1081001719&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&order_no=".time()."%2d817194024664%2d429984&tracking_no=1080280519269830&status=03&error_message=%2d&payment=01")->assertRedirect(config("const.base_url"));




		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→会員情報変更→パスワード変更送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/settlement/buy';
		$response = $this->actingAs($user);

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
			'buy_method'	=> 3,			
		];

		if( count($db_data) > 0 ){
			$listProductId = [];
			foreach($db_data as $lines){
				$listProductId[] = $lines->id;
			}
			$send_data['product_id'][] = $listProductId[rand(0, count($listProductId) - 1)];
		}

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
		$total_amount = 0;
		foreach($db_data as $lines){
			$total_amount = $lines->money;
			$order_id = $lines->order_id;
			$payment_id = $lines->payment_id;
		}

		$cookies = [];
		$success_flg = false;
		foreach($send_response->headers->getCookies() as $lines){
			$cookie_key = $lines->getName();
			if( $cookie_key == config('const.product_order_id_cookie_name') ){
				$success_flg = true;
				echo "商品選択→PAYID：{$payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→\033[0;34mOK\033[0m\n";
			}
			$cookies[$cookie_key] = $lines->getValue();
		}

		if( !$success_flg ){
			echo "商品選択→\033[1;35mNG\033[0m\n";
		}

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

		//ネットバンク決済の正常系テスト
		$send_response = $this->call('POST', '/member/settlement/buy/send', $send_pay_data)->assertStatus(200);

		$db_data = Payment_log::where("login_id", $user->login_id)
					->where("product_id", $send_data['product_id'])
					->where("pay_type", 3)
					->where("type", 0)
					->where("status", 1)
					->where("sendid", $send_response->baseResponse->original)
					->get();

		$sendid = null;
		$point = 0;
		$status = 1;
		foreach($db_data as $lines){
			if( $order_id == $lines->order_id ){
				$status = $lines->status;
				$point = $lines->point;
				$total_amount = $lines->money;
				$sendid = $lines->sendid;
				echo "Axes決済画面→PAYID：{$lines->payment_id}→注文ID：{$order_id}→商品ID：{$send_data['product_id'][0]}→送信ID：{$lines->sendid}→\033[0;34mOK\033[0m\n";
			}else{
				echo "Axes決済画面→\033[1;35mNG\033[0m\n";
			}
		}

		$send_response = $this->call('GET', "/axes/netbank/payment/response?clientip=1081001719&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&order_no=".time()."%2d817194024664%2d429984&tracking_no=1080280519269830&status=04&error_message=%2d&payment=01")->assertStatus(200);
//echo print_r($send_response->baseResponse->original, true);
		echo "Axesネットバンク決済結果エラー→";

		if( $send_response->baseResponse->original == "ng" ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$send_response = $this->call('GET', "/axes/netbank/payment/response?clientip=1081001719&money=0&email=".urlencode($user->mail_address)."&sendid=".$sendid."&sendpoint=".$order_id."&order_no=".time()."%2d817194024664%2d429984&tracking_no=1080280519269830&status=05&error_message=%2d&payment=01")->assertStatus(200);
		echo "Axesネットバンク決済結果失敗→";

		if( $send_response->baseResponse->original == "ng" ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

    }

}
