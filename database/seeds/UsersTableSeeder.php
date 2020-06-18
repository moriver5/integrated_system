<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		for($t=0;$t<5000;$t++){
			$ad_cd = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8);

			date_default_timezone_set('UTC');
			$start = strtotime('2017-01-01 00:00:00'); // 0
			$end = strtotime('2018-08-01 14:00:00'); // 2147483647
			$temp_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			$start = strtotime('2017-6-01 00:00:00'); // 0
			$end = strtotime('2018-08-30 14:00:00'); // 2147483647
			$create_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;
			$regist_date = date("Ymdhms", mt_rand($start, $end)) . PHP_EOL;

			$start = strtotime('2017-12-01 00:00:00'); // 0
			$end = strtotime('2018-08-30 14:00:00'); // 2147483647
			$update_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			$start = strtotime('2018-01-01 00:00:00'); // 0
			$end = strtotime('2018-08-01 14:00:00'); // 2147483647
			$last_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			DB::table('users')->insert([
				'login_id' => rand(000000,999999),
				'password' => bcrypt('secret'),
				'mail_address' => 'fmember.nishizawa+'.mt_rand(100000,999999).'@gmail.com',
//				'mobile_mail_address' => str_random(10).'@i.softbank.jp',
				'remember_token' => session_create_id(),
				'group_id' => rand(1,53),
				'mail_status' => rand(0,1),
				'status' => rand(1,4),
				'point' => rand(100,50000),
				'pay_count' => rand(0,50),
				'ad_cd' => $ad_cd,
				'regist_date' => $regist_date,
				'created_at' => $create_date,
				'updated_at' => $update_date,
				'temporary_datetime' => $temp_date,
				'last_access_datetime' => $last_date
			]);
		}
    }
}
