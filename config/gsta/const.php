<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

	//テスト環境
	if( $_SERVER['HOSTNAME'] == 'dev-php01' ){
		$const = [
			'mail_from'						=> 'info@test.g-stars.net',
			'replay_to_mail'				=> 'info@test.g-stars.net',
			'replay_to_mail_account'		=> 'info@',
			'return_path_to_mail'			=> 'failmail@test.g-stars.net',
			'return_path_to_mail_account'	=> 'failmail',
			'base_url'						=> 'https://test.g-stars.net',
			'settlement_success_link_url'	=> 'https://test.g-stars.net/member/settlement/done',
			'axes_success_link_text'		=> '購入ページへ戻る',
			'axes_failure_link_url'			=> 'https://test.g-stars.net/member/settlement',
			'axes_failure_link_text'		=> '購入ページへ戻る',
			'list_site_const'				=> [
				'test.g-stars.net'			=> 'gsta',
			],
		];

	//本番環境
	}else{
		$const = [
			'mail_from'						=> 'info@g-stars.net',
			'replay_to_mail'				=> 'info@g-stars.net',
			'replay_to_mail_account'		=> 'info@',
			'return_path_to_mail'			=> 'failmail@g-stars.net',
			'return_path_to_mail_account'	=> 'failmail',
			'base_url'						=> 'https://g-stars.net',
			'settlement_success_link_url'	=> 'https://g-stars.net/member/settlement/done',
			'axes_success_link_text'		=> '購入ページへ戻る',
			'axes_failure_link_url'			=> 'https://g-stars.net/member/settlement',
			'axes_failure_link_text'		=> '購入ページへ戻る',
			'list_site_const'				=> [
				'g-stars.net'				=> 'gsta',
			],
		];
	}

return array_merge($const, [
	'list_mobile_domain'			=> [												//携帯ドメインの正規表現
		'docomo\.ne\.jp',
		'docomo\.blackberry\.com',
		'ezweb\.ne\.jp',
		'softbank\.ne\.jp',
		'i\.softbank\.jp',
		'vodafone\.ne\.jp',
		'disney\.ne\.jp',
		'disneymobile\.ne\.jp',
		'yahoo\.ne\.jp',
		'y\-mobile\.ne\.jp',
		'ymobile1\.ne\.jp',
		'ymobile\.ne\.jp',
		'emnet\.ne\.jp',
		'emobile\-s\.ne\.jp',
		'emobile\.ne\.jp',
		'pdx\.ne\.jp',
		'willcom\.com',
		'wcm\.ne\.jp',
		'gol\.com',
		'rakuten\.jp',
		'mineo\.jp',
		'ocn\.ne\.jp',
	],
	'html_title'					=> 'GOLDEN★STARS(ゴールデン★スターズ)',
	//settlement_typesテーブルのidで決済会社をわける
	//id=1 telecom
	//id=2 axes
	//id=3 credix
	'list_pay_agency'				=> [
		'1'	=> 'TELECOM',
		'2'	=> 'AXES',
		'3'	=> 'CREDIX',
	],
	'list_agency_settlement_tpl'	=> [
											//telecom
		1 => [
			1 => 'buy_telecom_bank',
			2 => 'buy_telecom_credit',
			3 => 'buy_telecom_netbank',
		],
											//axes
		2 => [
			1 => 'buy_axes_bank',
			2 => 'buy_axes_credit',
			3 => 'buy_axes_netbank',
		],
											//credix
		3 => [
			1 => 'buy_credix_bank',
			2 => 'buy_credix_credit',
			3 => 'buy_credix_netbank',
		],
	],
	'list_title'					=> [
		'mem_top'						=> '会員-[会員トップ]',
		'mem_setting'					=> '会員-[会員情報変更]',
		'mem_setting_end'				=> '会員-[会員情報変更完了]',
		'mem_mailbox'					=> '会員-[メールボックス]',
		'mem_hit'						=> '会員-[的中実績]',
		'mem_voice'						=> '会員-[会員様の声]',
		'mem_qa'						=> '会員-[よくある質問]',
		'mem_settlement'				=> '会員-[商品購入・ポイント追加]',
		'mem_settlement_check'			=> '会員-[商品購入・ポイント追加-購入確認]',
		'mem_expectation_toll'			=> '会員-[厳選情報]',
		'mem_expectation_toll_detail'	=> '会員-[厳選情報-詳細]',
		'mem_warning_toll'				=> '会員-[厳選情報-ポイント不足]',
		'mem_error_toll'				=> '会員-[厳選情報-例外エラー]',
		'mem_expectation_list'			=> '会員-[情報公開]',
		'mem_expectation_detail'		=> '会員-[情報詳細]',
		'mem_expectation_free'			=> '会員-[無料情報]',
		'mem_privacy'					=> '会員-[プライバシーポリシー]',
		'mem_rule'						=> '会員-[利用規約]',
		'mem_guide'						=> '会員-[簡単ご利用方法]',
		'mem_outline'					=> '会員-[特定商取引に基づく表記]',
		'mem_info'						=> '会員-[お問い合わせ]',
		'mem_campaign'					=> '会員-[情報公開-キャンペーン]',
		'mem_regular'					=> '会員-[情報公開-レギュラー]',

		'forget'	=> '-[ログインID・パスワード忘れ]',
		'info'		=> '-[お問い合わせ]',
		'rule'		=> '-[利用規約]',
		'outline'	=> '-[特定商取引]',
	],
	'axes_remote_addr'				=> [
		'210.164.6.67',
		'202.221.139.50',
		'172.16.0.36',				//開発サーバーIP
		'172.16.44.102',			//開発環境でテストするとき：西沢のローカルPCのIPでテストするとき
//		'222.151.205.105',			//本番環境でテストするとき：社内IP？
		'153.142.217.3',
		'210.164.6.67',
		'202.221.139.50',
	],
	'telecom_remote_addr'				=> [
		'54.65.177.67',
		'52.196.8.0',
		'54.238.8.174',
		'54.95.89.20',
		'172.16.0.36',				//開発サーバーIP
		'172.16.44.102',			//開発環境でテストするとき：西沢のローカルPCのIPでテストするとき
//		'222.151.205.105'			//本番環境でテストするとき：社内IP？
	],
	'credix_remote_addr'				=> [
		'153.142.217.3',			//Credix社のIP
		'210.164.6.67',				//結果データ送信IP
		'202.221.139.50',			//結果データ送信IP
		'172.16.0.36',				//開発サーバーIP
		'172.16.44.102',			//開発環境でテストするとき：西沢のローカルPCのIPでテストするとき
//		'222.151.205.105'			//本番環境でテストするとき：社内IP？
	],
	'access_key'					=> '<ACCESS_KEY>',
	'melmaga_id'					=> '<MELMAGA_ID>',
	'convert_mail_from_name'		=> '-%site_name-',
	'convert_from_mail'				=> '-%info_mail-',
	'mail_from_name'				=> '【GOLDEN★STARS】',
	'user_mail_subject'				=> 'お問い合わせ',
	'public_dir_path'				=> 'public',
	'public_full_path'				=> '/data/www/siteo/public',
	'landing_dir_path'				=> 'LP',
	'landing_url_path'				=> 'LP',
	'redirect_landing_url_path'		=> 'LP',
	'top_content_images_path'		=> 'images/top_content',
	'tipster_images_path'			=> 'images/tipster',
	'images_path'					=> 'images',
	'member_bonus_url_path'			=> 'member/bonus/get',
	'member_bonusgeted_url_path'	=> 'member/bonus/suc',
	'campaign_url_path'				=> 'member/campaign/',
	'regular_url_path'				=> 'member/regular/',
	'mailbox_history_url_path'		=> 'member/mailbox/history',
	'login_id_length'				=> 6,
	'login_id_max_length'			=> 20,
	'email_length'					=> 254,
	'password_length'				=> 6,
	'password_max_length'			=> 50,
	'min_login_id'					=> 100000,
	'max_login_id'					=> 999999,
	'subject_length'				=> 50,
	'from_name_length'				=> 50,
	'contents_length'				=> 300,
	'sendid_length'					=> 25,

	/*
	 * 会員前ページのパス
	 */
	'member_top_path'			=> '/member/home',
	'nonmember_top_path'		=> '/',
	'regist_path'				=> '/regist',
	'singleopt_regist_path'		=> '/singleopt_regist',
	'comp_regist_path'			=> '/registend',
	'login_exec_path'			=> '/login',
	'forget_path'				=> '/forget',
	'forget_send_path'			=> '/forget/send',
	'about_path'				=> '/about',
	'privilege_path'			=> '/privilege',
	'hit_path'					=> '/hit',
	'voice_path'				=> '/voice',
	'qa_path'					=> '/qa',
	'info_path'					=> '/info',
	'info_comp_path'			=> '/info/send/comp',
	'sitemap_path'				=> '/sitemap',
	'qa_path'					=> '/qa',
	'privacy_path'				=> '/privacy',
	'rule_path'					=> '/rule',
	'outline_path'				=> '/outline',
	'domain_path'				=> '/domain',
	
	/*
	 * 会員ページのパス
	 */
	'member_settlement_path'			=> '/member/settlement',
	'member_settlement_list_path'		=> '/member/settlement_list',
	'member_expectation_list_path'		=> '/member/expectation/list',
	'member_expectation_free_path'		=> '/member/expectation/free',
	'member_expectation_toll_path'		=> '/member/expectation/toll',
	'member_hit_path'					=> '/member/hit',
	'member_voice_path'					=> '/member/voice',
	'member_guide_path'					=> '/member/guide',
	'member_setting_path'				=> '/member/setting',
	'member_setting_password_path'		=> '/member/setting/update/password',
	'member_setting_email_path'			=> '/member/setting/update/email',
	'member_setting_email_chg_path'		=> '/member/setting/update/email/check',
	'member_setting_end_path'			=> '/member/setting/end',
	'member_qa_path'					=> '/member/qa',
	'member_info_path'					=> '/member/info',
	'member_info_confirm_path'			=> '/member/info/confirm',
	'member_logout_path'				=> '/member/logout',
	'member_sitemap_path'				=> '/member/sitemap',
	'member_privacy_path'				=> '/member/privacy',
	'member_rule_path'					=> '/member/rule',
	'member_outline_path'				=> '/member/outline',
	'member_legal_path'					=> '/member/legal',
	'member_guide_path'					=> '/member/guide',
	
	/*
	 * パラメータ
	 */
	'setting_end_type_password'			=> 'password',
	'setting_end_type_email'			=> 'email',
	'setting_check_email'				=> 'check_email',
	'info_end_status'					=> 'end',
	'forget_end_status'					=> 'end',
	'default_ajax_timeout'				=> 10000,
	'disp_news_data_limit'				=> 5,
	'roop_limit'						=> 10,
	'disp_achievements_limit'			=> 18,
	'disp_mailbox_limit'				=> 10,
	'disp_top_achievements_limit'		=> 6,
	'disp_top_voice_limit'				=> 4,
	'settlement_result'					=> [
		'0'	=> 0,		//管理手動
		'1'	=> 1,		//未決済
		'2'	=> 2,		//入金ｴﾗｰ
		'3'	=> 3,		//決済済
		'4'	=> 4,		//返金
		'5'	=> 5,		//未決済
		'6'	=> 6,		//入金ｴﾗｰ
		'7'	=> 7,		//キャンセル
	],
	'product_order_id_cookie_name'		=> 'PRODUCT_ORDER_ID',
	'product_order_cookie_life_time'	=> 60 * 24,
	'regist_status'						=> [
		['1', '本登録'],
		['0', '仮登録'],
		['2', '退会'],
		['3', 'ブラック'],
	],
	
	//広告コードのクッキー保存期限
	'aff_cookie_expire_time'			=> 300,

	/*
	 * ページ管理
	 */
	'none_post_name'						=> '匿名',
	'none_post_title'						=> '無題',

	/*
	 * ログ関連
	 */
	'payment_log_dir_path'				=> '/data/www/siteo/storage/logs/payment_log/',
	'system_log_dir_path'				=> '/data/www/siteo/storage/logs/syslogs/',
	'client_log_dir_path'				=> '/data/www/siteo/storage/logs/client_logs/',
	'payment_log_name'					=> '決済結果ログ',
	'payment_log_credit_file_name'		=> 'credit_',
	'payment_log_netbank_file_name'		=> 'netbank_',
	'client_member_history_file_name'	=> 'client_member_history.log',
	'client_nonmember_file_name'		=> 'client_nonmember_access.log',
	'client_access_file_name'			=> 'client_access_history.log',
	'client_history_log_name'			=> '会員ログ',
	'client_nonmember_log_name'			=> '非会員ログ',
	'display_list'						=> [
		'login'						=> '通常ログイン',
		'dedicated_login'			=> '専用ログイン',
		'logout'					=> 'ログアウト',
		'mem_campaign'				=> '会員-キャンペーン情報',
		'mem_regular'				=> '会員-レギュラー情報',
		'mem_top'					=> '会員-トップページ',
		'mem_guide'					=> '会員-初心者ガイド',
		'mem_guide_play'			=> '会員-馬券の種類について',
		'mem_guide_ticket'			=> '会員-馬券の購入方法',
		'mem_guide_profit'			=> '会員-投資方法・情報の見かた',
		'mem_banner1'				=> '会員-バナー1',
		'mem_banner2'				=> '会員-バナー2',
		'mem_banner3'				=> '会員-バナー3',
		'mem_expectation_list'		=> '会員-情報公開(リスト)',
		'mem_expectation_detail'	=> '会員-情報公開-商品詳細',
		'mem_expectation'			=> '会員-情報公開(無料)',
		'mem_expectation_view'		=> '会員-情報公開-閲覧(無料、初回閲覧含む)',
		'mem_expectation_toll'		=> '会員-情報公開-(有料)',
		'mem_expectation_toll_view'	=> '会員-情報公開-閲覧(PT減算・商品購入、初回閲覧含む)',
		'mem_expectation_pt_view'	=> '会員-情報公開-初回閲覧(PT減算)',
		'mem_expectation_buy_view'	=> '会員-情報公開-初回閲覧(商品購入)',
		'mem_hit'					=> '会員-的中実績',
		'mem_voice'					=> '会員-会員様の声',
		'mem_qa'					=> '会員-よくある質問',
		'mem_info'					=> '会員-お問い合わせ',
		'mem_info_send'				=> '会員-お問い合わせ内容送信',
		'mem_buy'					=> '会員-商品購入',
		'mem_buy_list'				=> '会員-商品購入リスト',
		'mem_pay'					=> '会員-商品購入-入金済',
		'mem_pay_failed'			=> '会員-商品購入-入金結果-失敗',
		'mem_pay_err'				=> '会員-商品購入-入金結果-エラー',
		'mem_pay_except_err'		=> '会員-商品購入-入金処理-例外エラー',
		'mem_buy_bank'				=> '会員-商品購入-銀行振込',
		'mem_pay_bank'				=> '会員-商品購入-銀行振込済',
		'mem_buy_credit'			=> '会員-商品購入-クレジット',
		'mem_pay_credit'			=> '会員-商品購入-クレジット入金済',
		'mem_buy_netbank'			=> '会員-商品購入-ネットバンク',
		'mem_pay_netbank'			=> '会員-商品購入-ネットバンク入金済',
		'mem_buy_end'				=> '会員-商品購入-完了画面',
		'mem_setting'				=> '会員-情報変更',
		'mem_setting_mail'			=> '会員-情報変更-メールアドレス変更',
		'mem_setting_pass'			=> '会員-情報変更-パスワード変更',
		'mem_setting_end'			=> '会員-情報変更-完了',
		''							=> '会員-ポイント追加',
		'mem_sitemap'				=> '会員-サイトマップ',
		'mem_privacy'				=> '会員-プライバシーポリシー',
		'mem_rule'					=> '会員-利用規約',
		'mem_outline'				=> '会員-特定商取引',
		'mem_mailbox'				=> '会員-MAILBOX',
		'mem_mailbox_melmaga'		=> '会員-MAILBOX-メルマガ表示',
		'mem_mailbox_info'			=> '会員-MAILBOX-お問い合わせ表示',
		'mem_pt_buy_bank'			=> '会員-ポイント決済-銀行振込',
		'mem_pt_buy_credit'			=> '会員-ポイント決済-クレジット',
		'mem_pt_buy_netbank'		=> '会員-ポイント決済-ネットバンク',
		'mem_product_buy_bank'		=> '会員-商品決済-銀行振込',
		'mem_product_buy_credit'	=> '会員-商品決済-クレジット',
		'mem_product_buy_netbank'	=> '会員-商品決済-ネットバンク',
		'mem_outline'				=> '会員-特定商取引',
		'mem_legal'					=> '会員-特記事項',
		'mem_guide'					=> '会員-ご利用ガイド',
		'regist'					=> '仮登録',
		'registend'					=> '本登録',
		'mobile_top'				=> 'トップページ(モバイル)',
		'pc_top'					=> 'トップページ(PC)',
		'forget'					=> 'パスワード忘れ',
		'forget_send'				=> 'パスワード忘れ-送信',
		'about'						=> '投資競馬とは',
		'privilege'					=> '無料特典',
		'hit'						=> '的中実績',
		'voice'						=> 'ご利用者の声',
		'qa'						=> 'よくある質問',
		'info'						=> 'お問い合わせ',
		'info_send'					=> 'お問い合わせ-送信',
		'sitemap'					=> 'サイトマップ',
		'privacy'					=> 'プライバシーポリシー',
		'rule'						=> '利用規約',
		'outline'					=> '特定商取引',
		'legal'						=> '特記事項',
		'domain'					=> 'ドメイン指定',
		'simple_login'				=> '簡単ログイン',
		'verified_login'			=> '認証済ログイン',
		'mobile__login'				=> 'ログイン(モバイル)',
		'mobile_regist'				=> '会員登録(モバイル)',
		'mobile_rule'				=> '利用規約(モバイル)',
		'landing_page'				=> '',
	],
	'member_access_page'			=> [
		'\/member\/home.*'							=>	'トップ',
		'\/member\/setting\/update\/password.*'		=>	'パスワード変更ボタン押下',
		'\/member\/setting\/end\/password.*'		=>	'パスワード変更完了',
		'\/member\/setting\/end\/check_email.*'		=>	'メールアドレス変更確認メール送信',
		'\/member\/setting\/update\/email\/check.*'	=>	'メールアドレス変更URLアクセス',
		'\/member\/setting\/update\/email'			=>	'メールアドレス変更ボタン押下',
		'\/member\/setting\/end\/email.*'			=>	'メールアドレス変更完了',
		'\/member\/setting'							=>	'会員情報変更',
		'\/member\/mailbox\/history.*'				=>	'メールボックス→メール閲覧',
		'\/member\/mailbox\/info\/history.*'		=>	'メールボックス→お問い合わせメール閲覧',
		'\/member\/mailbox'							=>	'メールボックス',
		'\/member\/expectation\/free\/view.*'		=>	'無料情報→詳細',
		'\/member\/expectation\/free'				=>	'無料情報',
		'\/member\/expectation\/toll\/view.*'		=>	'厳選情報→詳細',
		'\/member\/expectation\/toll'				=>	'厳選情報',
		'\/member\/hit.*'							=>	'的中実績',
		'\/member\/voice.*'							=>	'会員様の声',
		'\/member\/qa.*'							=>	'よくある質問',
		'\/member\/settlement\/buy\/point.*'		=>	'商品購入/ポイント追加→ポイント選択確認',
		'\/member\/settlement\/buy\/send.*'			=>	'商品購入/ポイント追加→SSL決済ページ',
		'\/member\/settlement\/buy'					=>	'商品購入/ポイント追加→商品選択確認',
		'\/member\/settlement'						=>	'商品購入/ポイント追加',
		'\/member\/info\/confirm.*'					=>	'お問い合わせ→送信',
		'\/member\/info\/end.*'						=>	'お問い合わせ→送信完了',
		'\/member\/info'							=>	'お問い合わせ',
		'\/member\/outline.*'						=>	'特定商取引に基づく表記',
		'\/member\/rule.*'							=>	'利用規約',
		'\/member\/privacy.*'						=>	'プライバシーポリシー',
		'\/member\/logout'							=>	'ログアウト',
	],
	
	/*
	 * メール関連
	 */
	//ユーザー用
	'mail_from_name'				=> 'GOLDEN★STARS',
	'mail_info_subject'				=> '【GOLDEN★STARS】です。',
	'mail_regist_subject'			=> '仮登録完了のご連絡',
	'mail_comp_regist_subject'		=> '【本登録】が完了致しました!',

	/*
	 * メールのテンプレート
	 */
	//ユーザー用
	'forget_regist'					=> 'emails.forget_regist',
	'forget'						=> 'emails.forget',
	'provision_regist'				=> 'emails.provision_regist',
	'reregist'						=> 'emails.reregist',
	'registered'					=> 'emails.registered',
	'email_change'					=> 'emails.email_change',
	'quited_email'					=> 'emails.quit_user',
	'black_email'					=> 'emails.black_user',
	'info_email'					=> 'emails.info',
	'product_order'					=> 'emails.product_order',
	'payment_comp'					=> 'emails.payment_comp',
]);
