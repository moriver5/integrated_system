<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Model\User::class, function (Faker $faker) {
	$now_date = Carbon::now();
	$password = str_random(config('const.password_length'));

	return [
		'login_id'					=> $faker->randomNumber(6, false), 
		'password'					=> bcrypt($password),
		'password_raw'				=> $password, 
		'mail_address'				=> "fmember.nishizawa+test".rand (100,10000000)."@gmail.com", 
		'remember_token'			=> session_create_id(), 
		'mail_status'				=> 1, 
		'status'					=> 1, 
		'point'						=> 200, 
		'pay_count'					=> 0, 
		'pay_amount'				=> 0, 
		'last_access_datetime'		=> $now_date, 
		'sort_last_access_datetime'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00', 
		'temporary_datetime'		=> $now_date,
		'sort_temporary_datetime'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
		'regist_date'				=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
		'created_at'				=> $now_date,
		'updated_at'				=> $now_date,
		'disable'					=> 0
    ];
});
