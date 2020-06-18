<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\MemberController;
use App\Model\Contact;
use App\Model\User;
use Illuminate\Http\Request;
use Validator;

class ForgetSendTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testForgetSend()
    {
		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→お問い合わせ画面→お問い合わせ送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/info/confirm';
		$response = $this->actingAs($user);

		echo "正常系テスト→";

		//送信データ
		$send_data = [
			'subject'	=> "件名１",
			'contents'	=> "お問い合わせ内容１",			
		];

		//正常系テスト
		$response->post('/member/info/confirm',$send_data)->assertStatus(302);

		//お問い合わせ送信後、送信データ登録されているのか取得
		$db_data = Contact::where("client_id", $user->id)->get();

		if( !empty($db_data) ){
			foreach($db_data as $index => $lines){
				//送信データとDBに登録されているデータが同じなら
				if( $send_data['subject'] == $lines->subject && 
					$send_data['contents'] == $lines->msg ){
					echo "\033[0;34mOK\033[0m\n";
				}
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

/*
		//エラー系テスト
		$response->from('/member/info')->post('/member/info/confirm',[
							'subject'	=> "",
							'contents'	=> "お問い合わせ内容２",
						]);
 */
		//お問い合わせ画面の送信処理で使用されているバリデーションのみのテスト
		$request = new Request;
		$request->merge([
			'subject'	=> "",
			'contents'	=> "お問い合わせ内容２",
			'_token'	=> csrf_token(),
		]);
//		$controller = new MemberController($request);
//		$controller->infoConfirm($request);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→件名が空→";
		if( $validator->errors()->has("subject") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "件名２",
			'contents'	=> "",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→内容が空→";
		if( $validator->errors()->has("contents") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "𠺕𠂉",
			'contents'	=> "内容",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→件名にサロゲートペアの文字→";
		if( $validator->errors()->has("subject") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "件名",
			'contents'	=> "𠺕𠂉",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→内容にサロゲートペアの文字→";
		if( $validator->errors()->has("contents") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "🥰",
			'contents'	=> "内容",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→件名に絵文字→";
		if( $validator->errors()->has("subject") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "件名",
			'contents'	=> "🥰",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→内容に絵文字→";
		if( $validator->errors()->has("contents") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "あいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえお",
			'contents'	=> "内容",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→件名の最大文字数→";
		if( $validator->errors()->has("subject") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'subject'	=> "件名",
			'contents'	=> "あいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえお",
			'_token'	=> csrf_token(),
		]);

		$validator = Validator::make($request->all(), [
			'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
		]);

		echo "異常系テスト→内容の最大文字数→";
		if( $validator->errors()->has("contents") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		User::where("id", $user->id)->delete();
    }
}
