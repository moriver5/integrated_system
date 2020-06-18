<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cookie;
use Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;

class ClientStoreExclusiveControlTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
		$user = factory(\App\Model\User::class)->create();
		echo "\n認証アカウント\n";
		echo "ID：".$user->id."\n";
		echo "ログインID：".$user->login_id."\n";
		echo "パスワード：".$user->password_raw."\n";
		echo "暗号パスワード：".$user->password."\n";
		echo "メールアドレス：".$user->mail_address."\n\n";

		//認証後→会員情報変更→パスワード変更送信
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/settlement/buy';
		$response = $this->actingAs($user);

		$base_url = config("const.base_url")."/admin/member/client/edit/send";

		$urls = [];
		for($i=0;$i<20;$i++){
//			$urls[] = $base_url."?access_num=".$i;
			$urls[] = $base_url;
		}

		$client = new Client();
		$requests = function ($urls) use ($client, $user) {
			foreach ($urls as $url) {
				yield function () use ($client, $url, $user) {
//					return $client->postAsync($url);
					return $client->request("post", $url, [
//						'headers' => [
//							'X-CSSRF-TOKEN' => csrf_token(),
//						],
						'form_params' => [
							'id' => $user->id,
							'name' => $user->login_id,
							'new_password' => $user->password_raw,
							'email' => $user->mail_address,
							'point' => $user->point,
							'ad_cd' => "test222",
							'status' => 1,
							'mail_status' => 1,
							'tel' => "",
							'group_id' => 0,
							'description' => "",
//							'_token' => csrf_token()
						],
//						'verify' => false
					]);
				};
			}
		};

		$pool = new Pool($client, $requests($urls), [
			'concurrency' => 20,
			'fulfilled' => function ($response, $index) use ($urls) {
				echo 'アクセス成功 url:' . $urls[$index] . "\n";
			},
			'rejected' => function ($reason, $index) use ($urls) {
				echo 'アクセス失敗 url:' . $urls[$index] . "\n";
			}
		]);

		$promise = $pool->promise();
		$promise->wait();
    }
}
