<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'login_id', 
		'password', 
		'password_raw', 
		'mail_address', 
		'mobile_mail_address',
		'identify_id',
		'session_id',
		'remember_token', 
		'mail_status', 
		'status', 
		'point', 
		'pay_count', 
		'pay_amount', 
		'group_id', 
		'ad_cd', 
		'credit_certify_phone_no', 
		'bonus_flg',
		'action',
		'pay_datetime', 
		'sort_pay_datetime', 
		'last_access_datetime', 
		'sort_last_access_datetime', 
		'quit_datetime', 
		'sort_quit_datetime', 
		'temporary_datetime',
		'sort_temporary_datetime',
		'description',
		'regist_date',
		'created_at',
		'updated_at',
		'disable'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];
}
