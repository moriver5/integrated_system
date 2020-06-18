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
use App\Model\User;
use Session;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\RequestException;

class ExpectationTollExclusiveControlTest extends TestCase
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
		echo "メールアドレス：".$user->mail_address."\n";
		echo "ポイント：".$user->point."\n\n";

		//認証後→厳選情報→厳選情報閲覧
		$_SERVER['REQUEST_URI'] = 'テストアクセス<>/member/expectation/toll';
		$response = $this->actingAs($user);
//		$response = $this->call('GET', '/member/expectation/toll/view/2/2')->assertStatus(200);

//		$client = new Client(['debug' => true,'cookies' => true, 'verify' => false]);
		$client = new Client(['cookies' => true]);

		$jar = new \GuzzleHttp\Cookie\CookieJar();
/*
		$url = config("const.base_url")."/login";
		$res = $client->request("post", $url, [
			'cookies' => $jar,
			'form_params' => [
				'login_id' => $user->login_id,
				'password' => $user->password,
//				'_token' => csrf_token()
			]
        ]);
		$login_info = json_decode($res->getBody());
echo print_r(csrf_token(),true);
 */

		$base_url = config("const.base_url")."/member/expectation/toll/view/2/2/".$user->login_id;
//		$base_url = config("const.base_url")."/member/expectation/toll/view/2/2";

		$urls = [];
		for($i=0;$i<100;$i++){
			$urls[] = $base_url."?access_num=".$i;
//			$urls[] = $base_url;
		}

//		$client = new Client();
		$requests = function ($urls) use ($client) {
			foreach ($urls as $url) {
				yield function () use ($client, $url) {
//echo $url."\n";
					return $client->getAsync($url);
				};
			}
		};

		$pool = new Pool($client, $requests($urls), [
			'concurrency' => 100,
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
