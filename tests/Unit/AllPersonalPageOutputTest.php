<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Model\User;

class AllPersonalPageOutputTest extends TestCase
{
    /**
     * 認証後の全ページの表示テスト
     *
     * @return void
     */
    public function testOutput()
    {
		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n";

		//これを設定しないとテスト実行中にログ出力(ClientLogクラス)でエラーになる
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/home';
		$response = $this->actingAs($user)->get('/member/home')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/setting';
		$response = $this->actingAs($user)->get('/member/setting')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/mailbox';
		$response = $this->actingAs($user)->get('/member/mailbox')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/hit';
		$response = $this->actingAs($user)->get('/member/hit')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/voice';
		$response = $this->actingAs($user)->get('/member/voice')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/qa';
		$response = $this->actingAs($user)->get('/member/qa')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/settlement';
		$response = $this->actingAs($user)->get('/member/settlement')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/expectation/toll';
		$response = $this->actingAs($user)->get('/member/expectation/toll')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/expectation/free';
		$response = $this->actingAs($user)->get('/member/expectation/free')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/privacy';
		$response = $this->actingAs($user)->get('/member/privacy')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/rule';
		$response = $this->actingAs($user)->get('/member/rule')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/outline';
		$response = $this->actingAs($user)->get('/member/outline')->assertOk();

		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/info';
		$response = $this->actingAs($user)->get('/member/info')->assertOk();

		User::where("id", $user->id)->delete();
	}
}
