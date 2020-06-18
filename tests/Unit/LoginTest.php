<?php

namespace Tests\Unit;

use App\Model\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Auth;

class LoginTest extends TestCase
{
    /**
     * ログイン前の全ページ出力とログイン認証テスト
     *
     * @return void
     */
    public function testLogin()
    {
		//ログイン前の全ページアクセス
		$this->get('/')->assertOk();
		$this->get('/info')->assertOk();
		$this->get('/privacy')->assertOk();
		$this->get('/rule')->assertOk();
		$this->get('/outline')->assertOk();

		//ユーザー作成
		$user = factory(\App\Model\User::class)->create();

		//まだ認証されていない
		$this->assertFalse(Auth::guard('user')->check());

		// ログインを実行
		$response = $this->post('/login', [
			'login_id'	=> "$user->login_id",
			'password'	=> "$user->password_raw",
//			'_token'	=> csrf_token()
		]);

		//ログイン後、/member/homeへリダイレクトしているか確認
		$response->assertStatus(302);
		$response->assertRedirect('/member/home');

		//ユーザーの認証にどのガードを使用するかを指定
		$response = $this->actingAs($user, 'user');

		//認証されているか確認
		$response->assertTrue(Auth::guard('user')->check());

		//認証ユーザー取得
		$authed_user = \Auth::guard('user')->user();

		//登録ユーザーと登録後のユーザー情報の照合確認
		if( $authed_user->login_id == $user->login_id ){
			echo "\n認証済 => OK\n";
			echo "ログインID：".$user->login_id."\n";
			echo "パスワード：".$user->password_raw."\n";
			echo "暗号パスワード：".$user->password."\n";
			echo "メールアドレス：".$user->mail_address."\n";
		}

		//これを設定しないとテスト実行中にログ出力(ClientLogクラス)でエラーになる
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/home';

		//ログイン後のトップページ
		$response->get(url('/member/home'))->assertStatus(200);

		User::where("id", $user->id)->delete();

//			->assertSeeText($user->lohin_id)
//			->assertSeeText($user->point);
//			->assertViewIs("member.home");
//			->dump();
//			->assertSeeText("もももももも");
//			->assertSeeText("情報を購入する");
//error_log($response->getContent()."\n",3,"/data/www/storage/jray/storage/logs/nishi_log.txt");

//		$response->assertTrue(true);
/*
		//ログイン後の会員情報変更
		$response->actingAs($user, 'user')
			->get('/member/setting')
			->assertSee("会員情報変更")
			->assertSee("パスワード変更");
		$response->assertTrue(true);

		//メールBOX
		$response->actingAs($user, 'user')
			->get('/member/mailbox')
			->assertSee("受信メール一覧")
			->assertSee("お問い合わせメール一覧");
		$response->assertTrue(true);

		//的中実績
		$response->actingAs($user, 'user')
			->get('/member/hit')
			->assertSee("2,381,000円")
			->assertSee("日本ダービー 3連単");
		$response->assertTrue(true);

		//会員様の声
		$response->actingAs($user, 'user')
			->get('/member/voice');
		$response->assertTrue(true);
*/
    }
}
