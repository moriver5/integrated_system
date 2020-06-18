<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Model\User;
use App\Model\Check_chg_email;
use Illuminate\Http\Request;
use Validator;

class SettingSendTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSettingSend()
    {
		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→会員情報変更→パスワード変更送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/setting/update/password';
		$response = $this->actingAs($user);

		echo "パスワード変更の正常系テスト→";

		//送信データ
		$send_data = [
			'new_password'				=> "moritomo4649",
			'new_password_confirmation'	=> "moritomo4649",			
		];

		//パスワード変更の正常系テスト
		$response->post('/member/setting/update/password',$send_data)->assertStatus(302);

		//パスワード変更後、DBに登録されているのか取得
		$db_data = User::where("id", $user->id)->get();

		if( !empty($db_data) ){
			foreach($db_data as $index => $lines){
				//送信データとDBに登録されているデータが同じなら
				if( $send_data['new_password_confirmation'] == $lines->password_raw ){
					echo "\033[0;34mOK\033[0m\n";
				}
			}
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/setting/update/email';
		$response = $this->actingAs($user);

		echo "メアド変更の正常系テスト→";

		$rnd_str = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10);

		//送信データ
		$send_data = [
			'pc_email'	=> "fmember.nishizawa+".$rnd_str."@gmail.com"
		];

		//メアド変更の正常系テスト
		$response->post('/member/setting/update/email',$send_data)->assertStatus(302);

		//お問い合わせ送信後、送信データ登録されているのか取得
		$db_data = Check_chg_email::where("login_id", $user->login_id)->get();

		if( !empty($db_data) ){
			foreach($db_data as $index => $lines){
				//送信データとDBに登録されているデータが同じなら
				if( $send_data['pc_email'] == $lines->email ){
					//メール本文のメアド完了リンクへアクセス
					$_SERVER['REQUEST_URI'] = 'テストアクセス<>'.config('const.base_url').config('const.member_setting_email_chg_path').'/'.$user->login_id.'/'.$user->remember_token;
					$response->get(config('const.base_url').config('const.member_setting_email_chg_path').'/'.$user->login_id.'/'.$lines->token)->assertStatus(302);

					//変更後のメアドをusersテーブルから取得
					$user_data = User::where("id", $user->id)->where("mail_address", $send_data['pc_email'])->get();

					//変更後のメアドがusersテーブルに登録されていれば
					if( count($user_data) > 0 ){
						echo "\033[0;34mOK\033[0m\n";					
					}else{
						echo "\033[1;35mNG2\033[0m\n";			
					}
				}
			}
		}else{
			echo "\033[1;35mNG1\033[0m\n";
		}

		//パスワード変更送信時に使用されているバリデーションのみのテスト
		$request = new Request;
		$request->merge([
			'new_password'				=> "",
			'new_password_confirmation'	=> "moritomo4649",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードが空→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "moritomo4649",
			'new_password_confirmation'	=> "",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→確認用パスワードが空→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "もsadf4234hg",
			'new_password_confirmation'	=> "もsadf4234hg",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードが使用可能な文字→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "もsadf4234hg",
			'new_password_confirmation'	=> "もsadf4234hg",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→確認用パスワードが使用可能な文字→";
		if( $validator->errors()->has("new_password_confirmation") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649",
			'new_password_confirmation'	=> "moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードが最大文字数→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649",
			'new_password_confirmation'	=> "moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649moritomo4649",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→確認用パスワードが最大文字数→";
		if( $validator->errors()->has("new_password_confirmation") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "mo",
			'new_password_confirmation'	=> "mo",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードが最小文字数→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "mo",
			'new_password_confirmation'	=> "mo",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→確認用パスワードが最大文字数→";
		if( $validator->errors()->has("new_password_confirmation") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "moritomo4649",
			'new_password_confirmation'	=> "moritomd4649",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードと確認用パスワードの一致→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "moritomo 4649",
			'new_password_confirmation'	=> "moritomo 4649",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードに空白が含まれる→";
		if( $validator->errors()->has("new_password") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		$request = new Request;
		$request->merge([
			'new_password'				=> "moritomo 4649",
			'new_password_confirmation'	=> "moritomo 4649",			
		]);

		$validator = Validator::make($request->all(), [
			'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
		]);

		echo "異常系テスト→新しいパスワードに空白が含まれる→";
		if( $validator->errors()->has("new_password_confirmation") ){
			echo "\033[0;34mOK\033[0m\n";
		}else{
			echo "\033[1;35mNG\033[0m\n";
		}

		User::where("id", $user->id)->delete();
    }
}
