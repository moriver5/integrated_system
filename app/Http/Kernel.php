<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	/**
	 * The application's global HTTP middleware stack.
	 *
	 * These middleware are run during every request to your application.
	 *
	 * @var array
	 */
	/*
	 * ※重要
	 * ミドルウェアは配列の先頭から順番に実行されているようです。
	 * 順番を入れ替えると意図した動作にならないことあり。
	 */
	protected $middleware = [
		/*
		 * 自作メンテナンスモード
		 */
		\App\Http\Middleware\CheckForMaintenanceMode::class,

		/*
		 *	クリックジャッキング攻撃対策用ヘッダー
		 */
		\App\Http\Middleware\ResponseHeader::class,

		/*
		 * クロスドメイン用
		 */
		\Barryvdh\Cors\HandleCors::class,

		/*
		 * デフォルトのメンテナンスモード
		 * 自作のメンテナンスモードを優先するためコメント
		 */
//		\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
		\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
		\App\Http\Middleware\TrimStrings::class,
		\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
		/*
		 * キャリア判定
		 * add by nishizawa date 2017.9.4
		 */
//		\App\Http\Middleware\ViewSwitchMiddleware::class,

		/*
		 * htmlの minify (圧縮) をするプラグイン
		 * add by nishizawa date 2018.5.11
		 */
		\RenatoMarinho\LaravelPageSpeed\Middleware\ElideAttributes::class,
		\RenatoMarinho\LaravelPageSpeed\Middleware\InsertDNSPrefetch::class,
		\RenatoMarinho\LaravelPageSpeed\Middleware\RemoveComments::class,
		\RenatoMarinho\LaravelPageSpeed\Middleware\TrimUrls::class,
		\RenatoMarinho\LaravelPageSpeed\Middleware\RemoveQuotes::class,
		\RenatoMarinho\LaravelPageSpeed\Middleware\CollapseWhitespace::class,

		//$middlewareGroupsから移動(__construct内でSessionを使用できるようにするため)
		\Illuminate\Session\Middleware\StartSession::class,
	];

	/**
	 * The application's route middleware groups.
	 *
	 * @var array
	 */
	protected $middlewareGroups = [
		'web' => [
			\App\Http\Middleware\EncryptCookies::class,
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
//			\Illuminate\Session\Middleware\StartSession::class,
			// \Illuminate\Session\Middleware\AuthenticateSession::class,
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,
			\App\Http\Middleware\VerifyCsrfToken::class,
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		],

		'api' => [
			'throttle:60,1',
			'bindings',
		],
	];

	/**
	 * The application's route middleware.
	 *
	 * These middleware may be assigned to groups or used individually.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		//許可IP以外のアクセスか確認
		'check.allow.ip' => \App\Http\Middleware\CheckAllowIp::class,
		'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
		'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
		'auth.token' => \App\Http\Middleware\MemberAuthToken::class,
		'auth.admin.token' => \App\Http\Middleware\AdminMemberAuthToken::class,
		'auth.agency.token' => \App\Http\Middleware\AgencyMemberAuthToken::class,
		'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
		'can' => \Illuminate\Auth\Middleware\Authorize::class,
		'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
		'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
		//顧客の最終アクセス日時を更新する
		'member.lastaccess.update' => \App\Http\Middleware\UpdateLastAccessDate::class,
		//顧客へログインボーナスを付与
		'member.login.bonus' => \App\Http\Middleware\AddBonusPoint::class,
		//決済結果URLへのアクセスがAXESのIPなのか判定
		'axes.access.ip' => \App\Http\Middleware\CheckAccessAxesIp::class,
		//決済結果URLへのアクセスがTELECOMのIPなのか判定
		'telecom.access.ip' => \App\Http\Middleware\CheckAccessTelecomIp::class,
		//決済結果URLへのアクセスがCREDIXのIPなのか判定
		'credix.access.ip' => \App\Http\Middleware\CheckAccessCredixIp::class,
		//メルマガからのアクセスなら更新
		'view.melmaga' => \App\Http\Middleware\ViewMelmagaUpdate::class,
		//選択中のDB確認
		'check.select.db' => \App\Http\Middleware\CheckSelectSite::class,
		//アクセス元ドメインごとにディレクトリを選択
		'view.switch' => \App\Http\Middleware\ViewSwitchMiddleware::class,		
	];
}
