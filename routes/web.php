<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*******************
 * 
	代理店管理画面
 * 
 *******************/

//代理店管理画面-ログイン前(http://ドメイン/agency 以降でアクセスがあったら)
Route::group(['prefix' => 'agency'], function() {
	//代理店管理ログイン前-ログイン画面
	Route::get('/', 'Agency\Auth\LoginController@showLoginForm');
	Route::get('login', 'Agency\Auth\LoginController@showLoginForm');
	Route::post('login', 'Agency\Auth\LoginController@login');

	//代理店管理画面-ログアウト
	Route::post('logout', 'Agency\Auth\LoginController@logout');
	Route::get('logout', 'Agency\Auth\LoginController@logout');

	//代理店管理画面-ログイン後の画面(http://ドメイン/agency/member 以降でアクセスがあったら)
	Route::group(['middleware' => 'auth.agency.token'], function() {
		//管理トップ
		Route::get('member', 'Agency\AgencyController@index');
		Route::get('member/home/{sid?}', 'Agency\AgencyController@index');

		//集計-期間
		Route::post('member/aggregate', 'Agency\AgencyController@searchPost');
		Route::get('member/aggregate', 'Agency\AgencyController@search');

		//集計-月別
		Route::post('member/aggregate/month/{ad_cd}', 'Agency\AgencyController@aggregateMonth');
		Route::get('member/aggregate/month/{ad_cd}', 'Agency\AgencyController@aggregateMonth');

	});

});

/*******************
 * 
	管理画面
 * 
 *******************/

//管理画面-ログイン前(http://ドメイン/admin 以降でアクセスがあったら)
Route::group(['prefix' => 'admin', 'middleware' => ['check.allow.ip']], function() {
	//管理ログイン前-アカウント新規作成
	Route::get('regist', 'Admin\Auth\LoginController@register');
	Route::post('regist/send', 'Admin\Auth\RegisterController@create');

	//管理ログイン前-パスワード設定(アカウント未登録用)
	Route::get('password/setting/{sid}', 'Admin\Auth\RegisterController@passwordSetting');
	Route::post('password/setting/send', 'Admin\Auth\RegisterController@passwordSettingSend');

	//管理ログイン前-パスワード再設定(アカウント登録済用)
	Route::get('password/resetting/{sid}', 'Admin\Auth\RegisterController@passwordReSetting');
	Route::post('password/resetting/send', 'Admin\Auth\RegisterController@passwordReSettingSend');

	//管理ログイン前-ログインID・パスワード忘れ
	Route::get('forget', 'Admin\Auth\LoginController@forget');
	Route::post('forget', 'Admin\Auth\LoginController@forgetSend');

	//管理ログイン前-ログイン画面
//	Route::get('/', 'Admin\Auth\LoginController@showLoginForm');
	Route::get('/', function(){
		return redirect('/admin/login');
	});
	Route::get('login', 'Admin\Auth\LoginController@showLoginForm');
	Route::post('login', 'Admin\Auth\LoginController@login');

	//管理画面-ログアウト
	Route::post('logout', 'Admin\Auth\LoginController@logout');
	Route::get('logout', 'Admin\Auth\LoginController@logout');
 	
	//管理画面-ログイン後の画面(http://ドメイン/admin/member 以降でアクセスがあったら)
	Route::group(['middleware' => ['auth.admin.token', 'check.select.db']], function() {
		//管理トップ
		Route::get('member', 'Admin\AdminMemberController@index');
		Route::get('member/home/{sid?}', 'Admin\AdminMemberController@index');

		//サイト選択
		Route::get('member/site/select', 'Admin\AdminMemberController@selectSite');

		//サイト選択処理
		Route::post('member/site/select/send', 'Admin\AdminMemberController@selectSiteSend');

		//アカウント新規作成関連
		Route::get('member/create/{page?}', 'Admin\AdminMemberController@create');
		Route::post('member/create/send', 'Admin\AdminMemberController@createSend');

		//アカウント編集関連
		Route::get('member/edit/{page}/{id}', 'Admin\AdminMemberController@edit');
		Route::post('member/edit/send', 'Admin\AdminMemberController@store');

		//info-一覧
		Route::get('member/info', 'Admin\AdminInfoController@index');

		//info-削除
		Route::post('member/info/delete/send', 'Admin\AdminInfoController@delete');

		//info-返信画面
		Route::get('member/info/replay/{page}/{id}', 'Admin\AdminInfoController@replayMail');

		//info-返信画面-送信
		Route::post('member/info/replay/send', 'Admin\AdminInfoController@replayMailSend');

		//info-検索設定
		Route::get('member/info/search/setting', 'Admin\AdminInfoController@searchSetting');

		//info-検索
		Route::get('member/info/search', 'Admin\AdminInfoController@search');
		Route::post('member/info/search', 'Admin\AdminInfoController@searchPost');

		//クライアント一覧
		Route::get('member/client', 'Admin\AdminClientController@index');

		//クライアント-一括削除
		Route::post('member/client/del/send', 'Admin\AdminClientController@bulkDeleteSend');

		//クライアント検索-顧客データ新規作成
		Route::get('member/client/create', 'Admin\AdminClientController@create');
		Route::post('member/client/create/send', 'Admin\AdminClientController@createSend');

		//クライアント検索
		Route::get('member/client/search', 'Admin\AdminClientController@search');
		Route::post('member/client/search', 'Admin\AdminClientController@searchPost');

		//クライアントインポート
		Route::get('member/client/import', 'Admin\AdminClientController@importClientData');
		Route::post('member/client/import/upload', 'Admin\AdminClientController@importClientUpload');

		//クライアントインポート-不正メールアドレスリストのダウンロード
		Route::get('member/client/import/dl/bad_email', 'Admin\AdminClientController@downLoadBadEmail');

		//クライアントインポート-不明ドメインリストのダウンロード
		Route::get('member/client/import/dl/unknown_mx_domain', 'Admin\AdminClientController@downLoadUnknownMxDomain');

		//クライアントインポート-重複メールアドレスリストのダウンロード
		Route::get('member/client/import/dl/duplicate_email', 'Admin\AdminClientController@downLoadDuplicateEmail');

		//クライアントインポート-不正メールアドレスリストファイルの削除
		Route::get('member/client/import/del/bad_email', 'Admin\AdminClientController@deleteBadEmail');

		//クライアントインポート-不明ドメインリストファイルの削除
		Route::get('member/client/import/del/unknown_mx_domain', 'Admin\AdminClientController@deleteUnknownMxDomain');

		//クライアントインポート-重複メールアドレスリストファイルの削除
		Route::get('member/client/import/del/duplicate_email', 'Admin\AdminClientController@deleteDuplicateEmail');

		//クライアント検索エクスポート
		Route::post('member/client/search/export', 'Admin\AdminClientController@clientExport');

		//クライアントエクスポートの操作ログ
		Route::get('member/client/export/opeartion/log', 'Admin\AdminClientController@clientExportOperationLog');
		Route::post('member/client/export/opeartion/log', 'Admin\AdminClientController@clientExportOperationLog');

		//クライアント-グループ移行-トップ
		Route::get('member/client/group', 'Admin\AdminClientController@group');
		
		//クライアント-グループ移行-条件検索画面
		Route::get('member/client/group/search/setting', 'Admin\AdminClientController@groupSearchSetting');

		//クライアント-グループ移行-条件検索
		Route::post('member/client/group/search/count', 'Admin\AdminClientController@groupSearchCount');

		//クライアント-グループ移行-グループ移動
		Route::post('member/client/group/search/move', 'Admin\AdminClientController@groupSearchMove');

		//クライアント-ステータス変更-トップ
		Route::get('member/client/status', 'Admin\AdminClientController@status');

		//クライアント-ステータス変更-条件検索画面
		Route::get('member/client/status/search/setting', 'Admin\AdminClientController@statusSearchSetting');
		
		//クライアント-ステータス変更-条件検索
		Route::post('member/client/status/search/count', 'Admin\AdminClientController@statusSearchCount');

		//クライアント-ステータス変更-顧客一覧
		Route::get('member/client/status/search/list', 'Admin\AdminClientController@statusSearchList');

		//クライアント-ステータス変更-ポイント付与
		Route::post('member/client/status/search/point', 'Admin\AdminClientController@statusPointAdd');
		
		//クライアント編集画面
		Route::get('member/client/edit/{page}/{id}', 'Admin\AdminClientController@edit');

		//クライアント編集画面-更新処理
		Route::post('member/client/edit/send', 'Admin\AdminClientController@store');

		//クライアント編集画面-ポイント履歴
		Route::get('member/client/edit/point/history/{id}', 'Admin\AdminClientController@historyPoint');

		//クライアント編集画面-ポイント手動追加画面表示
		Route::get('member/client/edit/{id}/point/add', 'Admin\AdminClientController@addPoint');

		//クライアント編集画面-ポイント手動追加処理
		Route::post('member/client/edit/{id}/point/add/send', 'Admin\AdminClientController@addPointSend');

		//クライアント編集画面-個別メール画面表示
		Route::get('member/client/edit/{id}/mail/view', 'Admin\AdminClientController@editMail');

		//クライアント編集画面-個別メール送信処理
		Route::post('member/client/edit/{id}/mail/view/send', 'Admin\AdminClientController@editMailSend');

		//クライアント編集画面-個別メール送信履歴画面表示
		Route::get('member/client/edit/{id}/mail/history', 'Admin\AdminClientController@historyMailLog');

		//クライアント編集画面-個別メール送信履歴-詳細画面表示
		Route::get('member/client/edit/{id}/mail/history/detail/{detail_id}', 'Admin\AdminClientController@historyMailLogDetail');

		//クライアント編集画面-注文追加画面表示
		Route::get('member/client/edit/{id}/order/add', 'Admin\AdminClientController@addOrder');

		//クライアント編集画面-注文追加処理
		Route::post('member/client/edit/{id}/order/add/send', 'Admin\AdminClientController@addOrderSend');

		//クライアント編集画面-注文履歴画面表示
		Route::get('member/client/edit/{id}/order/history', 'Admin\AdminClientController@historyOrder');

		//クライアント編集画面-注文情報詳細画面表示
		Route::get('member/client/edit/{id}/order/history/{order_id}', 'Admin\AdminClientController@historyOrderDetail');

		//クライアント編集画面-注文情報詳細画面-更新
		Route::post('member/client/edit/order/history/update/send', 'Admin\AdminClientController@updateHistoryOrderDetail');

		//クライアント編集画面-メルマガ履歴画面表示
		Route::get('member/client/edit/{id}/melmaga/history', 'Admin\AdminClientController@historyMelmaga');

		//クライアント編集画面-アクセス履歴
		Route::get('member/client/edit/{id}/access/history', 'Admin\AdminClientController@accessHistory');

		//クライアント検索設定
		Route::get('member/client/search/setting', 'Admin\AdminClientController@searchSetting');

		//グループ管理
		Route::get('member/group', 'Admin\AdminMasterGroupController@index');

		//グループ管理-更新
		Route::post('member/group/send', 'Admin\AdminMasterGroupController@store');

		//グループ管理-グループ追加画面表示
		Route::get('member/group/add', 'Admin\AdminMasterGroupController@create');

		//グループ管理-グループ追加処理
		Route::post('member/group/add/send', 'Admin\AdminMasterGroupController@createSend');
		
		//グループ管理-一括移行
		Route::get('member/group/move/bulk', 'Admin\AdminMasterGroupController@bulkMoveGroup');

		//グループ管理-一括移行-更新
		Route::post('member/group/move/bulk/send', 'Admin\AdminMasterGroupController@bulkMoveGroupSend');

		//ページ管理-TOPコンテンツ
		Route::get('member/page', 'Admin\AdminPageController@index');

		//ページ管理-TOPコンテンツ-プレビュー
		Route::get('member/page/preview/{id}/{type}', 'Admin\AdminPageController@preview');

		//ページ管理-TOPコンテンツ-一括更新
		Route::post('member/page/update/send', 'Admin\AdminPageController@bulkUpdate');

		//ページ管理-TOPコンテンツ-検索設定画面表示
		Route::get('member/page/search/setting', 'Admin\AdminPageController@searchSetting');

		//ページ管理-TOPコンテンツ-検索
		Route::get('member/page/search', 'Admin\AdminPageController@search');
		Route::post('member/page/search', 'Admin\AdminPageController@searchPost');
		
		//ページ管理-TOPコンテンツ-新規作成
		Route::get('member/page/create/{id?}', 'Admin\AdminPageController@create');
		Route::post('member/page/create/send', 'Admin\AdminPageController@createSend');

		//ページ管理-TOPコンテンツ-変換表
		Route::get('member/page/convert/{id?}', 'Admin\AdminPageController@convert');

		//ページ管理-TOPコンテンツ-新規作成-プレビュー
		Route::get('member/page/create/preview/{type}', 'Admin\AdminPageController@createPreview');

		//ページ管理-TOPコンテンツ-画像アップロード処理
		Route::post('member/page/banner/upload/send', 'Admin\AdminPageController@uploadImageSend');

		//ページ管理-TOPコンテンツ編集
		Route::get('member/page/edit/{page}/{id}', 'Admin\AdminPageController@edit');

		//ページ管理-TOPコンテンツ編集-更新処理
		Route::post('member/page/edit/send', 'Admin\AdminPageController@store');

		//ページ管理-商品設定-一覧
		Route::get('member/page/product', 'Admin\AdminPageProductController@index');

		//ページ管理-商品設定-新規作成
		Route::get('member/page/product/create/{page?}', 'Admin\AdminPageProductController@create');

		//ページ管理-商品設定-新規作成処理
		Route::post('member/page/product/create/send', 'Admin\AdminPageProductController@createSend');

		//ページ管理-商品設定-編集
		Route::get('member/page/product/edit/{page}/{id}', 'Admin\AdminPageProductController@edit');

		//ページ管理-商品設定-編集-更新処理
		Route::post('member/page/product/edit/send', 'Admin\AdminPageProductController@store');

		//ページ管理-商品設定-一括更新
		Route::post('member/page/product/update/send', 'Admin\AdminPageProductController@bulkUpdate');

		//ページ管理-商品設定-検索設定画面表示
		Route::get('member/page/product/search/setting', 'Admin\AdminPageProductController@searchSetting');

		//ページ管理-商品設定-検索
		Route::get('member/page/product/search', 'Admin\AdminPageProductController@search');
		Route::post('member/page/product/search', 'Admin\AdminPageProductController@searchPost');



		//ページ管理-的中実績
		Route::get('member/page/achievement', 'Admin\AdminPageAchievementController@index');

		//ページ管理-的中実績-一括更新
		Route::post('member/page/achievement/update/send', 'Admin\AdminPageAchievementController@bulkUpdate');

		//ページ管理-的中実績-検索設定画面表示
		Route::get('member/page/achievement/search/setting', 'Admin\AdminPageAchievementController@searchSetting');

		//ページ管理-的中実績-検索
		Route::get('member/page/achievement/search', 'Admin\AdminPageAchievementController@search');
		Route::post('member/page/achievement/search', 'Admin\AdminPageAchievementController@searchPost');
		
		//ページ管理-的中実績-新規作成
		Route::get('member/page/achievement/create/{id?}', 'Admin\AdminPageAchievementController@create');
		Route::post('member/page/achievement/create/send', 'Admin\AdminPageAchievementController@createSend');

		//ページ管理-的中実績編集
		Route::get('member/page/achievement/edit/{page}/{id}', 'Admin\AdminPageAchievementController@edit');

		//ページ管理-的中実績編集-更新処理
		Route::post('member/page/achievement/edit/send', 'Admin\AdminPageAchievementController@store');



		//ページ管理-ご利用者の声
		Route::get('member/page/voice', 'Admin\AdminPageVoiceController@index');

		//ページ管理-ご利用者の声-一括更新
		Route::post('member/page/voice/update/send', 'Admin\AdminPageVoiceController@bulkUpdate');

		//ページ管理-ご利用者の声-検索設定画面表示
		Route::get('member/page/voice/search/setting', 'Admin\AdminPageVoiceController@searchSetting');

		//ページ管理-ご利用者の声-検索
		Route::get('member/page/voice/search', 'Admin\AdminPageVoiceController@search');
		Route::post('member/page/voice/search', 'Admin\AdminPageVoiceController@searchPost');
		
		//ページ管理-ご利用者の声-新規作成
		Route::get('member/page/voice/create/{id?}', 'Admin\AdminPageVoiceController@create');
		Route::post('member/page/voice/create/send', 'Admin\AdminPageVoiceController@createSend');

		//ページ管理-ご利用者の声-画像アップロード処理
		Route::post('member/page/voice/upload/send', 'Admin\AdminPageVoiceController@uploadImageSend');

		//ページ管理-ご利用者の声編集
		Route::get('member/page/voice/edit/{page}/{id}', 'Admin\AdminPageVoiceController@edit');

		//ページ管理-ご利用者の声-更新処理
		Route::post('member/page/voice/edit/send', 'Admin\AdminPageVoiceController@store');


		//ランディングページ-LP一覧
		Route::get('member/lp', 'Admin\AdminLandingPageController@index');

		//ランディングページ編集-プレビュー表示
		Route::post('member/lp/create/content/{id}/{type}/{name}/preview', 'Admin\AdminLandingPageController@previewLandingPageSend');
		Route::get('member/lp/create/content/{id}/{type}/{name}/preview', 'Admin\AdminLandingPageController@previewLandingPage');

		//ランディングページ-LP一覧-参照
		Route::get('member/lp/create/content/{id}/{type}/{name?}', 'Admin\AdminLandingPageController@createLandingPage');

		//ランディングページ-LP一覧-参照-更新処理
		Route::post('member/lp/create/content/{id}/{type}/{name}/send', 'Admin\AdminLandingPageController@updateLandingPageSend');

		//ランディングページ-LP一覧-参照-画像
		Route::get('member/lp/create/img/{id}', 'Admin\AdminLandingPageController@uploadLandingPageImg');

		//ランディングページ-LP一覧-参照-画像アップロード処理
		Route::post('member/lp/create/img/{id}/upload', 'Admin\AdminLandingPageController@uploadLandingPageImgUpload');

		//ランディングページ-LP一覧-参照-画像削除
		Route::post('member/lp/create/img/{id}/delete', 'Admin\AdminLandingPageController@deleteLandingPageImg');

		//ランディングページ-LP一覧-検索設定画面表示
		Route::get('member/lp/search/setting', 'Admin\AdminLandingPageController@searchSetting');

		//ランディングページ-LP一覧-検索
		Route::get('member/lp/search', 'Admin\AdminLandingPageController@search');
		Route::post('member/lp/search', 'Admin\AdminLandingPageController@searchPost');
		
		//ランディングページ-LP一覧-新規作成
		Route::get('member/lp/create', 'Admin\AdminLandingPageController@create');
		Route::post('member/lp/create/send', 'Admin\AdminLandingPageController@createSend');

		//ランディングページ-LP一覧-LP編集-個別ページ追加
		Route::post('member/lp/create/content/{id}/{type}/{name}/add/page/send', 'Admin\AdminLandingPageController@addLandingPageSend');

		//ランディングページ-LP一覧-編集
		Route::get('member/lp/edit/{page}/{id}', 'Admin\AdminLandingPageController@edit');

		//ランディングページ-LP一覧-編集処理
		Route::post('member/lp/edit/send', 'Admin\AdminLandingPageController@store');



		//予想管理-予想一覧
		Route::get('member/forecast', 'Admin\AdminForecastController@index');

		//予想管理-予想一覧-一括更新
		Route::post('member/forecast/update/send', 'Admin\AdminForecastController@bulkUpdate');

		//予想管理-予想一覧-検索設定画面表示
		Route::get('member/forecast/search/setting', 'Admin\AdminForecastController@searchSetting');

		//予想管理-予想一覧-検索
		Route::get('member/forecast/search', 'Admin\AdminForecastController@search');
		Route::post('member/forecast/search', 'Admin\AdminForecastController@searchPost');
		
		//予想管理-予想一覧-新規作成
		Route::get('member/forecast/create', 'Admin\AdminForecastController@create');
		Route::post('member/forecast/create/send', 'Admin\AdminForecastController@createSend');

		//予想管理-予想一覧-新規作成-preview
		Route::get('member/forecast/create/preview', 'Admin\AdminForecastController@createPreview');

		//予想管理-予想一覧-新規作成-フォーメーション組合せ数計算
		Route::get('member/forecast/create/calc', 'Admin\AdminForecastController@createCalc');

		//予想管理-予想一覧-編集
		Route::get('member/forecast/edit/{page}/{id}', 'Admin\AdminForecastController@edit');

		//予想管理-予想一覧-編集処理
		Route::post('member/forecast/edit/send', 'Admin\AdminForecastController@store');

		//予想管理-予想一覧-編集-プレビュー
		Route::get('member/forecast/edit/{page}/{id}/preview', 'Admin\AdminForecastController@editPreview');

		//予想管理-閲覧者一覧
		Route::get('member/forecast/visitor', 'Admin\AdminForecastVisitorController@index');

		//予想管理-閲覧者検索
		Route::get('member/forecast/visitor/search', 'Admin\AdminForecastVisitorController@index');
		Route::post('member/forecast/visitor/search', 'Admin\AdminForecastVisitorController@searchPost');

		//予想管理-アクセス
		Route::get('member/forecast/access/{page}/{id}', 'Admin\AdminForecastController@access');

		//予想管理-アクセス-メール配信画面
		Route::get('member/forecast/access/{page}/{id}/mail', 'Admin\AdminForecastController@editMail');

		//予想管理-アクセス-メール配信処理
		Route::post('member/forecast/access/mail/send', 'Admin\AdminForecastController@editMailSend');



		//メルマガ-即時配信メルマガ-トップ
		Route::get('member/melmaga', 'Admin\AdminMelmagaController@index');

		//メルマガ-検索設定画面表示
		Route::get('member/melmaga/search/setting', 'Admin\AdminMelmagaController@searchSetting');

		//メルマガ-検索
		Route::get('member/melmaga/search', 'Admin\AdminMelmagaController@search');
		Route::post('member/melmaga/search', 'Admin\AdminMelmagaController@searchPost');

		//メルマガ-検索-メルマガ即時配信
		Route::post('member/melmaga/search/mail/send', 'Admin\AdminMelmagaController@sendImmediateMelmaga');

		//メルマガ-メルマガ配信履歴
		Route::get('member/melmaga/mail/history', 'Admin\AdminMelmagaController@historySendMelmaga');

		//メルマガ-メルマガ配信履歴-配信メルマガ確認
		Route::get('member/melmaga/mail/history/view/{page}/{send_id}/{client_id?}', 'Admin\AdminMelmagaController@viewHistorySendMelmaga');

		//メルマガ-送信失敗一覧
		Route::get('member/melmaga/mail/failed/list', 'Admin\AdminMelmagaController@failedSendMelmaga');

		//メルマガ-送信失敗一覧-メルマガ送信失敗リスト
		Route::get('member/melmaga/mail/failed/list/emails/{page}/{melmaga_id}', 'Admin\AdminMelmagaController@listFailedSendMelmaga');

		//メルマガ-予約配信メルマガ-トップ
		Route::get('member/melmaga/reserve', 'Admin\AdminMelmagaReserveController@index');

		//メルマガ-検索設定画面表示
		Route::get('member/melmaga/reserve/search/setting', 'Admin\AdminMelmagaReserveController@searchSetting');

		//メルマガ-予約配信メルマガ-検索
		Route::get('member/melmaga/reserve/search', 'Admin\AdminMelmagaReserveController@search');
		Route::post('member/melmaga/reserve/search', 'Admin\AdminMelmagaReserveController@searchPost');

		//メルマガ-検索-予約配信メルマガ-メルマガ予約配信
		Route::post('member/melmaga/reserve/search/mail/send', 'Admin\AdminMelmagaReserveController@sendReserveMelmaga');

		//メルマガ-予約配信メルマガ-予約状況
		Route::get('member/melmaga/reserve/status', 'Admin\AdminMelmagaReserveController@statusReserveMelmaga');

		//メルマガ-予約配信メルマガ-予約状況-メルマガ編集
		Route::get('member/melmaga/reserve/status/edit/{page}/{melmaga_id}', 'Admin\AdminMelmagaReserveController@editReserveMelmaga');

		//メルマガ-予約配信メルマガ-予約状況-メルマガ編集-更新
		Route::post('member/melmaga/reserve/status/edit/{melmaga_id}/send', 'Admin\AdminMelmagaReserveController@sendEditReserveMelmaga');

		//メルマガ-予約配信メルマガ-予約状況-キャンセル
		Route::post('member/melmaga/reserve/status/cancel/{page}/{id}', 'Admin\AdminMelmagaReserveController@sendCancel');

		//メルマガ-登録後送信メール
		Route::get('member/melmaga/registered/mail', 'Admin\AdminMelmagaRegisteredMailController@index');

		//メルマガ-登録後送信メール-一括削除
		Route::post('member/melmaga/registered/mail/delete/send', 'Admin\AdminMelmagaRegisteredMailController@bulkUpdate');

		//メルマガ-登録後送信メール-新規作成
		Route::get('member/melmaga/registered/mail/create', 'Admin\AdminMelmagaRegisteredMailController@create');

		//メルマガ-登録後送信メール-新規作成-作成処理
		Route::post('member/melmaga/registered/mail/create/send', 'Admin\AdminMelmagaRegisteredMailController@createSend');

		//メルマガ-登録後送信メール-検索設定
		Route::get('member/melmaga/registered/mail/search/setting', 'Admin\AdminMelmagaRegisteredMailController@searchSetting');

		//メルマガ-登録後送信メール-検索設定-検索処理
		Route::get('member/melmaga/registered/mail/search', 'Admin\AdminMelmagaRegisteredMailController@search');
		Route::post('member/melmaga/registered/mail/search', 'Admin\AdminMelmagaRegisteredMailController@searchPost');

		//メルマガ-登録後送信メール-編集画面表示
		Route::get('member/melmaga/registered/mail/edit/{page}/{id}', 'Admin\AdminMelmagaRegisteredMailController@edit');

		//メルマガ-登録後送信メール-編集画面表示-編集処理
		Route::post('member/melmaga/registered/mail/edit/send', 'Admin\AdminMelmagaRegisteredMailController@store');

		//メルマガ-絵文字変換表示(HTML)
		Route::get('member/melmaga/emoji/convert/html', 'Admin\AdminEmojiController@convertHtmlMelmaga');

		//マスタ管理-予想師一覧
		Route::get('member/master/tipster/setting', 'Admin\AdminMasterTipsterController@index');

		//マスタ管理-予想師-画像アップロード
		Route::post('member/master/tipster/setting/upload/send', 'Admin\AdminMasterTipsterController@uploadImageSend');

//マスタ管理-予想師-新規作成
		Route::get('member/master/tipster/setting/create/{id?}', 'Admin\AdminMasterTipsterController@create');

		//マスタ管理-予想師-作成処理
		Route::post('member/master/tipster/setting/create/send', 'Admin\AdminMasterTipsterController@createSend');

		//マスタ管理-予想師-編集
		Route::get('member/master/tipster/setting/edit/{id}', 'Admin\AdminMasterTipsterController@edit');

		//マスタ管理-予想師-編集処理
		Route::post('member/master/tipster/setting/edit/{id}/send', 'Admin\AdminMasterTipsterController@store');

		//マスタ管理-決済会社設定
		Route::get('member/master/settlement/agency/setting', 'Admin\AdminMasterSettlementAgencyController@index');

		//マスタ管理-決済会社設定-選択処理
		Route::post('member/master/settlement/agency/setting/send', 'Admin\AdminMasterSettlementAgencyController@sendAgencyUpdate');

		//マスタ管理-出力文言設定
		Route::get('member/master/sentence/setting', 'Admin\AdminMasterSentenceController@index');
		Route::get('member/master/sentence/setting/redirect/{id}', 'Admin\AdminMasterSentenceController@index');

		//マスタ管理-プレビュー表示
		Route::get('member/master/sentence/setting/preview/{id}', 'Admin\AdminMasterSentenceController@preview');
		
		//マスタ管理-出力文言更新
		Route::post('member/master/sentence/setting/send', 'Admin\AdminMasterSentenceController@store');

		//マスタ管理-出力文言設定-変換表表示
		Route::get('member/master/sentence/setting/convert/{id}', 'Admin\AdminMasterSentenceController@convert');

		//マスタ管理-絵文字変換表示
		Route::get('member/master/emoji/convert/{id}', 'Admin\AdminEmojiController@convert');

		//マスタ管理-自動メール文設定
		Route::get('member/master/mail_sentence/setting', 'Admin\AdminMasterMailContentController@index');
		Route::get('member/master/mail_sentence/setting/redirect/{id}', 'Admin\AdminMasterMailContentController@index');

		//マスタ管理-メールアドレス禁止ワード設定
		Route::get('member/master/mailaddress_ng_word/setting', 'Admin\AdminMasterMailAddressNgWordController@index');

		//マスタ管理-メールアドレス禁止ワード設定-更新処理
		Route::post('member/master/mailaddress_ng_word/setting/send', 'Admin\AdminMasterMailAddressNgWordController@store');

		//マスタ管理-リレーサーバー設定
		Route::get('member/master/relayserver/setting', 'Admin\AdminMasterRelayServerController@index');

		//マスタ管理-リレーサーバー設定-更新処理
		Route::post('member/master/relayserver/setting/send', 'Admin\AdminMasterRelayServerController@store');

		//マスタ管理-自動メール文更新
		Route::post('member/master/mail_sentence/setting/send', 'Admin\AdminMasterMailContentController@store');

		//マスタ管理-自動メール文設定-変換表表示
		Route::get('member/master/mail_sentence/setting/convert/{id}', 'Admin\AdminMasterMailContentController@convert');

		//マスタ管理-%変換設定
		Route::get('member/master/convert/setting', 'Admin\AdminMasterConvertController@index');

		//マスタ管理-%変換設定-更新処理
		Route::post('member/master/convert/setting/send', 'Admin\AdminMasterConvertController@store');

		//マスタ管理-%変換設定-キー追加画面表示
		Route::get('member/master/convert/setting/add', 'Admin\AdminMasterConvertController@create');

		//マスタ管理-%変換設定-キー追加処理
		Route::post('member/master/convert/setting/add/send', 'Admin\AdminMasterConvertController@createSend');

		//マスタ管理-ログインボーナス付与ポイント画面表示
		Route::get('member/master/grant/login/bonus/point', 'Admin\AdminMasterPointController@createGrantLoginBonusPoint');

		//マスタ管理-ログインボーナス付与ポイント設定処理
		Route::post('member/master/grant/login/bonus/point/send', 'Admin\AdminMasterPointController@createGrantLoginBonusPointSend');

		//マスタ管理-付与ポイント画面表示
		Route::get('member/master/grant/point', 'Admin\AdminMasterPointController@createGrantPoint');

		//マスタ管理-付与ポイント設定処理
		Route::post('member/master/grant/point/send', 'Admin\AdminMasterPointController@createGrantPointSend');

		//マスタ管理-お知らせ一覧画面表示
		Route::get('member/master/info', 'Admin\AdminMasterInfoController@index');

		//マスタ管理-お知らせ設定作成画面表示
		Route::get('member/master/info/create/{id?}', 'Admin\AdminMasterInfoController@create');

		//マスタ管理-お知らせ設定処理
		Route::post('member/master/info/create/send', 'Admin\AdminMasterInfoController@createInfoSend');

		//マスタ管理-お知らせ設定編集画面表示
		Route::get('member/master/info/edit/{id}', 'Admin\AdminMasterInfoController@edit');

		//マスタ管理-お知らせ設定編集処理
		Route::post('member/master/info/edit/{id}/send', 'Admin\AdminMasterInfoController@store');

		//マスタ管理-お知らせ削除処理
		Route::post('member/master/info/delete/send', 'Admin\AdminMasterInfoController@bulkDelete');

		//マスタ管理-購入ポイントカテゴリ設定画面表示
		Route::get('member/master/purchase/point/category/setting', 'Admin\AdminMasterPointController@pointCategorySetting');

		//マスタ管理-バナー設定トップ
		Route::get('member/master/banner/setting', 'Admin\AdminBannerController@index');

		//マスタ管理-バナー追加画面
		Route::get('member/master/banner/create', 'Admin\AdminBannerController@create');

		//マスタ管理-バナー追加処理
		Route::post('member/master/banner/create/send', 'Admin\AdminBannerController@createSend');

		//マスタ管理-バナー更新処理
		Route::post('member/master/banner/update/send', 'Admin\AdminBannerController@store');

		//マスタ管理-購入ポイントカテゴリ設定更新処理
		Route::post('member/master/purchase/point/category/setting/send', 'Admin\AdminMasterPointController@pointCategorySettingSend');

		//マスタ管理-購入ポイントカテゴリ設定画面-購入ポイント設定画面表示
		Route::get('member/master/purchase/point/category/setting/detail/{id}', 'Admin\AdminMasterPointController@pointCategorySettingDetail');

		//マスタ管理-購入ポイントカテゴリ設定画面-購入ポイント設定画面-更新処理
		Route::post('member/master/purchase/point/category/setting/detail/{id}/send', 'Admin\AdminMasterPointController@pointCategorySettingDetailSend');

		//マスタ管理-購入ポイントカテゴリ設定画面-購入ポイント設定画面-購入ポイント設定追加画面表示
		Route::get('member/master/purchase/point/category/setting/detail/{id}/add', 'Admin\AdminMasterPointController@pointCategorySettingCreate');

		//マスタ管理-購入ポイントカテゴリ設定画面-購入ポイント設定画面-購入ポイント設定追加処理
		Route::post('member/master/purchase/point/category/setting/detail/{id}/add/send', 'Admin\AdminMasterPointController@pointCategorySettingCreateSend');

		//マスタ管理-購入ポイントカテゴリ追加画面表示
		Route::get('member/master/purchase/point/category/add', 'Admin\AdminMasterPointController@pointCategoryCreate');

		//マスタ管理-購入ポイントカテゴリ追加処理
		Route::post('member/master/purchase/point/category/add/send', 'Admin\AdminMasterPointController@pointCategoryCreateSend');

		//マスタ管理-倍率設定処理
		Route::post('member/master/magnification/setting/send', 'Admin\AdminMasterPointController@MagnificationSettingSend');

		//マスタ管理-確認アドレス設定
		Route::get('member/master/confirm/email/setting', 'Admin\AdminMasterConfirmEmailSettingController@index');

		//マスタ管理-確認アドレス設定-アドレス追加画面
		Route::get('member/master/confirm/email/setting/add', 'Admin\AdminMasterConfirmEmailSettingController@create');

		//マスタ管理-確認アドレス設定-アドレス追加-追加処理
		Route::post('member/master/confirm/email/setting/add/send', 'Admin\AdminMasterConfirmEmailSettingController@createSend');

		//マスタ管理-確認アドレス設定-更新処理
		Route::post('member/master/confirm/email/setting/del/send', 'Admin\AdminMasterConfirmEmailSettingController@updateSend');

		//マスタ管理-メンテナンス設定
		Route::get('member/maintenance/setting', 'Admin\AdminMasterMaintenanceController@index');

		//マスタ管理-メンテナンス設定処理
		Route::post('member/maintenance/setting/send', 'Admin\AdminMasterMaintenanceController@createSend');

		//マスタ管理-メンテナンス設定-メンテナンス画面プレビュー
		Route::get('member/maintenance/setting/preview', 'Admin\AdminMasterMaintenanceController@preview');

		//マスタ管理-画像UPLOAD
		Route::get('member/master/img/upload/{sort_type?}', 'Admin\AdminImgUploadController@index');

		//マスタ管理-画像UPLOAD処理
		Route::post('member/master/img/upload/send', 'Admin\AdminImgUploadController@uploadImg');

		//マスタ管理-画像削除処理
		Route::post('member/master/img/upload/delete', 'Admin\AdminImgUploadController@deleteImg');

		//ページ管理-利用者の声
		Route::get('member/voice', 'Admin\AdminUserVoiceController@index');

		//集計-アクセス解析-年
		Route::get('member/analytics/access/{year?}', 'Admin\AdminAnalyticsController@index');

		//集計-アクセス解析-月
		Route::get('member/analytics/access/{year}/{month}', 'Admin\AdminAnalyticsController@monthAnalysis');

		//集計-アクセス解析-日
		Route::get('member/analytics/access/{year}/{month}/{day}', 'Admin\AdminAnalyticsController@dayAnalysis');

		//集計-メルマガ解析-トップ
		Route::get('member/analytics/melmaga/access', 'Admin\AdminMelmagaAnalyticsController@index');

		//集計-メルマガ解析-閲覧済
		Route::get('member/analytics/melmaga/access/visited/{melmaga_id}', 'Admin\AdminMelmagaAnalyticsController@viewVisited');

		//集計-メルマガ解析-閲覧済
		Route::get('member/analytics/melmaga/access/unseen/{melmaga_id}', 'Admin\AdminMelmagaAnalyticsController@viewUnseen');

		//集計-商品解析
		Route::get('member/analytics/products/pay', 'Admin\AdminProductsAnalyticsController@index');

		//集計-商品解析-購入者
		Route::get('member/analytics/products/pay/{product_id}', 'Admin\AdminProductsAnalyticsController@viewCustomer');

		//集計-リピート解析
		Route::get('member/analytics/products/repeat', 'Admin\AdminRepeatAnalyticsController@index');

		//集計-PVログ-年
		Route::get('member/analytics/pv/access/{year?}', 'Admin\AdminPvAnalyticsController@index');

		//集計-PVログ-月
		Route::get('member/analytics/pv/access/{year}/{month}/{pv_name}', 'Admin\AdminPvAnalyticsController@monthAnalysis');

		//集計-利用統計-年
		Route::get('member/analytics/statistics/access/{year?}', 'Admin\AdminUserStatisticsController@index');

		//集計-利用統計-月
		Route::get('member/analytics/statistics/access/{year}/{month}', 'Admin\AdminUserStatisticsController@monthAnalysis');

		//集計-利用統計-日
		Route::get('member/analytics/statistics/access/{year}/{month}/{day}', 'Admin\AdminUserStatisticsController@dayAnalysis');

		//集計-利用統計-注文状況-月
		Route::get('member/analytics/statistics/access/order/status/{year}/{month}', 'Admin\AdminUserStatisticsController@monthOrderStatus');

		//集計-利用統計-注文状況-日
		Route::get('member/analytics/statistics/access/order/status/{year}/{month}/{day}', 'Admin\AdminUserStatisticsController@dayOrderStatus');

		//集計-売上集計-年
		Route::get('member/analytics/sales/summary/{year?}', 'Admin\AdminSalesSummaryController@index');

		//集計-売上集計-月
		Route::get('member/analytics/sales/summary/{year}/{month}', 'Admin\AdminSalesSummaryController@monthAnalysis');

		//集計-入金構成-年
		Route::get('member/analytics/payment/structure/{year?}', 'Admin\AdminPaymentStructureController@index');

		//集計-入金構成-月
		Route::get('member/analytics/payment/structure/{year}/{month}', 'Admin\AdminPaymentStructureController@monthAnalysis');

		//集計-入金構成-日
		Route::get('member/analytics/payment/structure/{year}/{month}/{day}', 'Admin\AdminPaymentStructureController@dayAnalysis');

		//集計-新規入金-年
		Route::get('member/analytics/newpayment/{year?}', 'Admin\AdminNewPaymentController@index');

		//集計-新規入金-月
		Route::get('member/analytics/newpayment/{year}/{month}', 'Admin\AdminNewPaymentController@monthAnalysis');

		//集計-新規入金-日
		Route::get('member/analytics/newpayment/{year}/{month}/{day}', 'Admin\AdminNewPaymentController@dayAnalysis');

		//集計-売上構成-年
		Route::get('member/analytics/sales/structure/{year?}', 'Admin\AdminSalesStructureController@index');

		//集計-イベント効果-年
		Route::get('member/analytics/event/{year?}', 'Admin\AdminEventAnalyticsController@index');

		//集計-イベント効果-月
		Route::get('member/analytics/event/{year}/{month}', 'Admin\AdminEventAnalyticsController@monthAnalysis');

		//集計-イベント効果-日
		Route::get('member/analytics/event/{year}/{month}/{day}', 'Admin\AdminEventAnalyticsController@dayAnalysis');

		//集計-イベント効果-アクセス詳細
		Route::get('member/analytics/event/{year}/{month}/{day}/{forecast_id}', 'Admin\AdminEventAnalyticsController@dayAnalysisDetail');

		//集計-購買動向分析-トップ画面
		Route::get('member/analytics/purchasing_trends', 'Admin\AdminPurchasingTrendsController@index');

		//集計-購買動向分析-抽出処理～購入人数表示
		Route::post('member/analytics/purchasing_trends/search', 'Admin\AdminPurchasingTrendsController@searchPost');

		//集計-購買動向分析-
		Route::post('member/analytics/purchasing_trends/search/export', 'Admin\AdminPurchasingTrendsController@clientExport');

		//集計-購買動向分析-
		Route::post('member/analytics/purchasing_trends/search/export/paycount', 'Admin\AdminPurchasingTrendsController@paycountExport');

		//集計-決済レポート
		Route::get('member/analytics/payment/report', 'Admin\AdminPaymenyAnalyticsController@index');

		//集計-決済レポート-検索設定
		Route::get('member/analytics/payment/report/search/setting', 'Admin\AdminPaymenyAnalyticsController@searchSetting');

		//集計-決済レポート-検索設定-検索
		Route::post('member/analytics/payment/report/search/send', 'Admin\AdminPaymenyAnalyticsController@searchPost');

		//広告-広告コード-一覧
		Route::get('member/ad/adcode', 'Admin\AdminAdCodeController@index');

		//広告-広告コード-一覧-一括削除
		Route::post('member/ad/adcode/send', 'Admin\AdminAdCodeController@bulkDeleteSend');

		//広告-広告コード-新規作成
		Route::get('member/ad/adcode/create', 'Admin\AdminAdCodeController@create');

		//広告-広告コード-新規作成処理
		Route::post('member/ad/adcode/create/send', 'Admin\AdminAdCodeController@createSend');

		//広告-広告コード-編集
		Route::get('member/ad/adcode/edit/{page}/{ad_id}', 'Admin\AdminAdCodeController@edit');

		//広告-広告コード-編集処理
		Route::post('member/ad/adcode/edit/send', 'Admin\AdminAdCodeController@store');

		//広告-広告コード-検索設定
		Route::get('member/ad/adcode/search/setting', 'Admin\AdminAdCodeController@searchSetting');

		//広告-広告コード-検索処理
		Route::post('member/ad/adcode/search', 'Admin\AdminAdCodeController@searchPost');
		Route::get('member/ad/adcode/search', 'Admin\AdminAdCodeController@search');

		//広告-代理店-一覧
		Route::get('member/ad/agency', 'Admin\AdminAdAgencyController@index');

		//広告-代理店-一括削除
		Route::post('member/ad/agency/send', 'Admin\AdminAdAgencyController@bulkDeleteSend');

		//広告-代理店-新規作成
		Route::get('member/ad/agency/create', 'Admin\AdminAdAgencyController@create');

		//広告-代理店-新規作成処理
		Route::post('member/ad/agency/create/send', 'Admin\AdminAdAgencyController@createSend');

		//広告-代理店-編集
		Route::get('member/ad/agency/edit/{page}/{ad_id}', 'Admin\AdminAdAgencyController@edit');

		//広告-代理店-編集処理
		Route::post('member/ad/agency/edit/send', 'Admin\AdminAdAgencyController@store');

		//広告-媒体集計-一覧
		Route::get('member/ad/media', 'Admin\AdminAdMediaController@index');

		//広告-媒体集計-検索設定
		Route::get('member/ad/media/search/setting', 'Admin\AdminAdMediaController@searchSetting');

		//広告-媒体集計-検索処理
		Route::post('member/ad/media/search', 'Admin\AdminAdMediaController@searchPost');
		Route::get('member/ad/media/search', 'Admin\AdminAdMediaController@search');

		//データ移行失-―一覧
		Route::get('member/migration/failed', 'Admin\AdminDataMigrationController@index');
		
	});
});

/*******************
 * 
	会員登録・ログイン
 * 
 *******************/

Route::group(['middleware' => ['view.switch']], function () {
	//ログイン用API
	Route::get('/api', 'Auth\LoginController@loginApi');

	//SP用ログイン
	Route::get('/sp/login', 'SiteOperationController@sp_login');

	//ログインID・パスワード忘れ
	Route::get('/forget', 'SiteOperationController@forget');
	Route::post('/forget/send', 'SiteOperationController@forgetSend');

	//お問い合わせ
	Route::get('/info', 'SiteOperationController@info');

	//お問い合わせ-送信
	Route::post('/info/send', 'SiteOperationController@sendInfo');

	//お問い合わせ-完了
	Route::get('/info/send/comp', 'SiteOperationController@compInfo');

	//プライバシー
	Route::get('/privacy', 'SiteOperationController@privacy');

	//利用規約
	Route::get('/rule', 'SiteOperationController@rule');

	//特定商取引法に基づく表記
	Route::get('/outline', 'SiteOperationController@outline');

	//競馬法に関する特記事項
	Route::get('/legal', 'SiteOperationController@legal');

	//仮登録ボタン押下
	Route::post('/regist', 'Auth\RegisterController@create');

	//本登録URLアクセス
	Route::get('/registend/{sid}', 'Auth\RegisterController@store');

	//ログインボタン押下
	Route::post('/login', 'Auth\LoginController@login');
});

/*******************
 * 
	会員ページ
 * 
 *******************/

Route::group(['middleware' => ['auth.token', 'member.login.bonus', 'member.lastaccess.update', 'view.melmaga', 'view.switch']], function () {

	//トップ
	Route::get('/member/bonus/{login_bonus_flg}', 'MemberController@index');

	//トップ
	Route::get('/member/home/{sid?}', 'MemberController@index');

	//キャンペーン情報
	Route::get('/member/campaign/{id}', 'CampaignController@index');

	//レギュラー情報
	Route::get('/member/regular/{id}', 'RegularController@index');

	//情報公開(商品一覧と予想一覧)
	Route::get('/member/expectation/list', 'MemberController@expectationList');

	//商品詳細
	Route::get('/member/product/detail/{product_id}', 'MemberController@detailProduct');

	//情報公開-詳細表示
	Route::get('/member/expectation/free/view/{category}/{id}', 'MemberController@viewExpectation');

	//情報公開-詳細表示
	Route::get('/member/expectation/toll/view/{category}/{id}', 'MemberController@viewExpectation');

	//的中実績
	Route::get('/member/hit', 'MemberController@hit');

	//喜びの声
	Route::get('/member/voice', 'MemberController@voice');

	//よくある質問
	Route::get('/member/qa', 'MemberController@qa');

	//お問い合わせ
	Route::get('/member/info', 'MemberController@info');

	//お問い合わせ-送信
	Route::post('/member/info/confirm', 'MemberController@infoConfirm');

	//お問い合わせ-送信完了後
	Route::get('/member/info/end', 'MemberController@infoSendEnd');

	//商品購入完了
	Route::get('/member/settlement/done/{order_id?}', 'MemberController@settlement_done');

	//商品購入
	Route::get('/member/settlement/{product_id?}', 'MemberController@settlement');

	//商品購入-決済ページ(銀行/クレジット/ネットバンク)
	Route::post('/member/settlement/buy', 'SettlementController@buyProduct');

	//商品購入-ポイント-決済ページ(銀行/クレジット/ネットバンク)
	Route::post('/member/settlement/buy/point', 'SettlementController@buyPoint');

	//商品購入-確認画面-AXESのsendidを生成
	Route::post('/member/settlement/buy/send', 'MakeSendIdController@getMakeSendId');

	//ログアウト
	Route::get('/member/logout', 'MemberController@logout');

	//会員情報変更
	Route::get('/member/setting', 'MemberController@userSetting');

	//会員情報変更-パスワード変更
	Route::post('/member/setting/update/password', 'MemberController@settingUpdatePassword');

	//会員情報変更-メールアドレス変更
	Route::post('/member/setting/update/email', 'MemberController@settingUpdateEmail');

	//会員情報変更-メールアドレス変更のリンクをクリック
	Route::get('/member/setting/update/email/check/{login_id}/{key}', 'MemberController@clickUpdateEmailLink');

	//会員情報変更-パスワード・メールアドレス変更完了
	Route::get('/member/setting/end/{type?}', 'MemberController@settingUpdateEnd');

	//プライバシーポリシー
	Route::get('/member/privacy', 'MemberController@privacy');

	//利用規約
	Route::get('/member/rule', 'MemberController@rule');
	
	//特定商取引
	Route::get('/member/outline', 'MemberController@outline');

	//競馬法に関する特記事項
	Route::get('/member/legal', 'MemberController@legal');

	//簡単ご利用ガイド
	Route::get('/member/guide', 'MemberController@guide');

	//MAILBOX-一覧
	Route::get('/member/mailbox', 'MemberController@mailbox');

	//MAILBOX-個別メルマガ表示
	Route::get('/member/mailbox/history/{melmaga_id}', 'MemberController@viewHistoryMelmaga');

	//MAILBOX-個別お問い合わせ表示
	Route::get('/member/mailbox/info/history/{info_id}', 'MemberController@viewHistoryInfo');

});

/*
 * 決済完了後、決済会社(aexs)からのアクセス
 */
Route::group(['middleware' => ['axes.access.ip']], function () {
	Route::get('/axes/credit/payment/response', 'AxesPaymentController@axesCreditPaymentResponse');
	Route::get('/axes/netbank/payment/response', 'AxesPaymentController@axesNetbankPaymentResponse');
});

/*
 * 決済完了後、決済会社(telecom)からのアクセス
 */
Route::group(['middleware' => ['telecom.access.ip']], function () {
	Route::get('/telecom/credit/payment/response', 'TelecomPaymentController@telecomCreditPaymentResponse');
	Route::get('/telecom/credit/speed/payment/response', 'TelecomPaymentController@telecomCreditPaymentResponse');
	Route::get('/telecom/netbank/payment/response', 'TelecomPaymentController@telecomNetbankPaymentResponse');
});

/*
 * 決済完了後、決済会社(credix)からのアクセス
 */

Route::group(['middleware' => ['credix.access.ip']], function () {
	Route::get('/credix/credit/payment/response', 'CredixPaymentController@credixCreditPaymentResponse');
	Route::get('/credix/credit/speed/payment/response', 'CredixPaymentController@credixNetbankPaymentResponse');
	Route::get('/credix/netbank/payment/response', 'CredixPaymentController@credixNetbankPaymentResponse');
});


Route::group(['middleware' => ['view.switch']], function () {
	Route::post('/LP/{id}/mail_send_exec.php', 'Auth\RegisterController@create');

	//ユーザーログイン前トップ画面(最初に定義すると他の画面を読み込まないので一番下に定義する)
	Route::get('/', 'SiteOperationController@index');
	Route::get('/LP/{id?}/{file?}', 'SiteOperationController@index');

	//ルーティングで設定したURL以外でアクセスの場合すべてトップページ表示
	Route::get('{string?}', 'SiteOperationController@index', function($string){
	})->where('string', '[A-Za-z0-9?=\.\/_\:-]*');
});


