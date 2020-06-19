<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

	//テスト環境
	if( $_SERVER['HOSTNAME'] == 'dev-php01' ){
		$const = [
			'mail_from'						=> 'info@test.admin-k.work',
			'replay_to_mail'				=> 'info@test.admin-k.work',
			'replay_to_mail_account'		=> 'info@',
			'return_path_to_mail'			=> 'failmail@test.admin-k.work',
			'return_path_to_mail_account'	=> 'failmail',
			'base_url'						=> 'https://test.admin-k.work',
			'base_admin_url'				=> 'https://test.admin-k.work/admin',
			'base_agency_url'				=> 'https://test.admin-k.work/agency',
			'list_site_const'				=> [
				'test.g-stars.net'			=> 'gsta',
				'dev.jra-yosou.net'			=> 'jray',
				'dev.keirin-yosou.com'		=> 'kriy',
				'dev.tcl-boat.com'			=> 'tclb',
			],
			'list_domain_const'	=> [
				'gsta'		=> 'test.g-stars.net',
				'jray'		=> 'dev.jra-yosou.net',
				'kriy'		=> 'dev.keirin-yosou.com',
				'tclb'		=> 'dev.tcl-boat.com',
			],
			'list_url_const'	=> [
				'gsta'		=> 'https://test.g-stars.net',
				'jray'		=> 'https://dev.jra-yosou.net',
				'kriy'		=> 'https://dev.keirin-yosou.com',
				'tclb'		=> 'https://dev.tcl-boat.com',
			],
		];

	//本番環境
	}else{
		$const = [
			'mail_from'						=> 'info@admin-k.work',
			'replay_to_mail'				=> 'info@admin-k.work',
			'replay_to_mail_account'		=> 'info@',
			'return_path_to_mail'			=> 'failmail@admin-k.work',
			'return_path_to_mail_account'	=> 'failmail',
			'base_url'						=> 'https://admin-k.work',
			'base_admin_url'				=> 'https://admin-k.work/admin',
			'base_agency_url'				=> 'https://admin-k.work/agency',
			'list_site_const'				=> [
				'g-stars.net'				=> 'gsta',
				'dev.jra-yosou.net'			=> 'jray',
				'dev.keirin-yosou.com'		=> 'kriy',
				'dev.tcl-boat.com'			=> 'tclb',
			],
			'list_domain_const'	=> [
				'gsta'		=> 'g-stars.net',
				'jray'		=> 'jra-yosou.net',
				'kriy'		=> 'keirin-yosou.com',
				'tclb'		=> 'tcl-boat.com',
			],
			'list_url_const'	=> [
				'gsta'		=> 'https://g-stars.net',
				'jray'		=> 'https://jra-yosou.net',
				'kriy'		=> 'https://keirin-yosou.com',
				'tclb'		=> 'https://tcl-boat.com',
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
	'html_title'					=> '統合サイト',
	'html_admin_title'				=> '統合サイト管理',
	'disp_mobile_domain'			=> [
		'docomo.ne.jp',
		'ezweb.ne.jp',
		'i.softbank.jp',
		'softbank.ne.jp',
		'd.vodafone.ne.jp',
		'h.vodafone.ne.jp',
		't.vodafone.ne.jp',
		'r.vodafone.ne.jp',
		'c.vodafone.ne.jp',
		'k.vodafone.ne.jp',
		'n.vodafone.ne.jp',
		's.vodafone.ne.jp',
		'q.vodafone.ne.jp',
		'disney.ne.jp',
		'pdx.ne.jp',
		'di.pdx.ne.jp',
		'dj.pdx.ne.jp',
		'dk.pdx.ne.jp',
		'wm.pdx.ne.jp',
		'willcom.com',
		'ymobile.ne.jp',
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
		'mem_expectation_free'			=> '会員-[無料情報]',
		'mem_privacy'					=> '会員-[プライバシーポリシー]',
		'mem_rule'						=> '会員-[利用規約]',
		'mem_outline'					=> '会員-[特定商取引に基づく表記]',
		'mem_info'						=> '会員-[お問い合わせ]',
		'mem_campaign'					=> '会員-[情報公開-キャンペーン]',
		'mem_regular'					=> '会員-[情報公開-レギュラー]',

		'forget'	=> '-[ログインID・パスワード忘れ]',
		'info'		=> '-[お問い合わせ]',
		'rule'		=> '-[利用規約]',
		'outline'	=> '-[特定商取引]',
	],
	'add_mail_header'				=> [
		'X-SendGroup'	=> 'mmaga',
	],
	'my_domain'						=> 'admin-k\.work',
	'axes_credit_settlement_url'	=> 'https://gw.axes-payment.com/cgi-bin/credit/order.cgi',
	'axes_netbank_settlement_url'	=> 'https://gw.axes-payment.com/cgi-bin/ebank.cgi',
	'axes_remote_addr'				=> [
		'210.164.6.67',
		'202.221.139.50',
		'172.16.0.36',				//開発サーバーIP
		'172.16.44.102',			//開発環境でテストするとき：西沢のローカルPCのIPでテストするとき
//		'222.151.205.105'			//本番環境でテストするとき：社内IP？
									//ここから株式会社CREDIXのアクセス元IP
		'153.142.217.3',
		'210.164.6.67',
		'202.221.139.50',
	],
	'not_set_group'					=> [0, '未設定'],
	'access_key'					=> '<ACCESS_KEY>',
	'melmaga_id'					=> '<MELMAGA_ID>',
	'settlement_success_link_url'	=> 'https://admin-k.work/member/settlement/done/',
	'axes_success_link_text'		=> '購入ページへ戻る',
	'axes_failure_link_url'			=> 'https://admin-k.work/member',
	'axes_failure_link_text'		=> 'サイトへ戻る',
	'convert_mail_from_name'		=> '-%site_name-',
	'convert_from_mail'				=> '-%info_mail-',
	'mail_from_name'				=> '【統合サイト】',
	'admin_access_allow_ip'			=> [
		'192.168.8.102',					//西沢PCのIP
		'192.168.8.103',					//西沢PCのIP
		'169.254.255.137',
		'172.16.44.102',					//西沢PCのIP
		'172.16.32.102',					//西沢PCのIP
		'172.16.44.120',					//西沢ノートPCのIP
		'222.151.205.105',					//www.sea-soft.net
		'221.186.144.218',					//www.gld-sys.com
		'133.242.176.26',					//SDI
		'153.156.45.88',					//SDI 4F
		'153.156.46.22',					//KB,
		'150.249.200.252',
		'221.186.144.219',					//社内Wi-Fi
		'150.249.206.7',					//Wakka?
		'1.33.207.130',
	],
	'list_career_lp_dir'			=> [
		0 => 'pc',
		2 => 'sp',
	],
	'artisan_command_path'			=> 'php /data/www/siteo/artisan',
	'view_file_extension'			=> '.blade.php',
	'project_storage_home_path'		=> '/data/www/storage/siteo',
	'project_home_path'				=> '/data/www/siteo',
	'project_home_match'			=> '\/data\/www\/siteo',
	'storage_public_dir_path'		=> 'storage/app/public',
	'public_dir_path'				=> 'public',
	'landing_dir_path'				=> 'LP',
	'landing_url_path'				=> 'LP',
	'redirect_landing_url_path'		=> 'LP',
	'upload_img_path'				=> 'images/upload_images',
	'public_full_path'				=> '/data/www/siteo/public/',
	'top_content_images_full_path'	=> '/data/www/siteo/public/images/top_content',
	'top_content_images_path'		=> 'images/top_content',
	'tipster_images_path'			=> 'images/tipster',
	'images_path'					=> 'images',
	'voice_images_path'				=> 'images/voice',
	'group_url_path'				=> 'member/group',
	'member_home_url_path'			=> 'member/home',
	'member_bonus_url_path'			=> 'member/bonus/get',
	'client_url_path'				=> 'member/client',
	'visitor_url_path'				=> 'member/forecast/visitor/search',
	'campaign_url_path'				=> 'member/campaign/',
	'regular_url_path'				=> 'member/regular/',
	'top_content_url_path'			=> 'member/page',
	'top_content_create_url_path'	=> 'member/page/create',
	'product_url_path'				=> 'member/page/product/edit',
	'voice_url_path'				=> 'member/page/voice/create',
	'lp_create_img_path'			=> 'member/lp/create/img',
	'reserve_status_url_path'		=> 'member/melmaga/reserve/status',
	'tipster_top'					=> 'member/master/tipster/setting',
	'tipster_create_url_path'		=> 'member/master/tipster/setting/create',
	'tipster_edit_url_path'			=> 'member/master/tipster/setting/edit',
	'convert_week'					=> ['日','月','火','水','木','金','土',],
	'agency_login_id_max_length'	=> 20,
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
	'group_name_max_length'			=> 100,
	'group_memo_max_length'			=> 100,
	'forecast_title_max_length'		=> 100,
	'forecast_comment_max_length'	=> 250,
	'top_content_title_max_length'	=> 250,
	'top_product_title_max_length'	=> 250,
	'achievement_race_max_length'	=> 100,
	'achievement_msg1_max_length'	=> 250,
	'achievement_msg2_max_length'	=> 250,
	'achievement_msg3_max_length'	=> 250,
	'achievement_memo_max_length'	=> 250,
	'lp_memo_max_length'			=> 250,
	'contents_title_max_length'		=> 100,
	'relay_server_max_length'		=> 15,
	'pt_category_name_max_length'	=> 50,
	'pt_category_remarks_max_length'=> 100,
	'pt_setting_text_max_length'	=> 100,
	'pt_setting_remarks_max_length'	=> 100,
	'convert_key_max_length'		=> 100,
	'convert_value_max_length'		=> 255,
	'convert_memo_max_length'		=> 250,
	'ad_code_max_length'			=> 50,
	'ad_name_max_length'			=> 100,
	'ad_code_memo_max_length'		=> 250,
	'voice_title_max_length'		=> 100,
	'voice_writer_max_length'		=> 20,
	'voice_comment_max_length'		=> 250,
	'num_digits_max_length'			=> 2,
	'item_value_max_length'			=> 250,
	'credit_client_ip'				=> 1011004040,
	'netbank_client_ip'				=> 1081001719,
	'sendid_length'					=> 25,

	/*
	 * DB関連
	 */
	'deadrock_max_retry'		=> 5,
	
	/*
	 * 管理画面ページのパス
	 */
	'admin_base_path'			=> '/admin',
	'admin_member_top_path'		=> '/member/home',
	'admin_edit_path'			=> '/member/edit',
	'admin_create_path'			=> '/member/create',
	'admin_create_send_path'	=> '/member/create/send',
	'admin_create_end_path'		=> '/member/create/send/end',
	'admin_client_path'			=> '/member/client',
	'admin_client_import_path'	=> '/member/client/import',
	'admin_agency_path'			=> '/member/ad/agency',
	'admin_adcode_path'			=> '/member/ad/adcode',
	'admin_page_path'			=> '/member/page',
	'admin_page_product_path'	=> '/member/page/product',
	'admin_page_voice_path'		=> '/member/page/voice',
	'admin_content_preview'		=> '/member/page/preview',
	'admin_forecast_path'		=> '/member/forecast',
	'admin_registered_mail_path'=> '/member/melmaga/registered/mail',
	'admin_lp_path'				=> '/member/lp',
	'admin_login_path'			=> '/login',
	'admin_regist_path'			=> '/regist',
	'admin_password_set_path'	=> '/password/setting',
	'admin_password_reset_path'	=> '/password/resetting',
	'admin_nonmember_top_path'	=> '/',
	
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
	
	/*
	 * パラメータ
	 */
	'setting_end_type_password'			=> 'password',
	'setting_end_type_email'			=> 'email',
	'setting_check_email'				=> 'check_email',
	'info_end_status'					=> 'end',
	'forget_end_status'					=> 'end',
	'admin_system_auth_id'				=> 4,
	'admin_default_ajax_timeout'		=> 10000,
	'admin_member_domain'				=> [
		'i\-sac\.jp',
		's\-vc\.jp',
		'gmail\.com',
		'wakka\-inc\.com',
		'y\-n\-s\.co\.jp',
	],
	'admin_member_list_limit'			=> 100,
	'admin_member_ua_list_limit'		=> 100,
	'admin_key_list_limit'				=> 100,
	'admin_client_list_limit'			=> 100,
	'admin_auth_list'					=> [
		'0' => '無効',
		'1'	=> '制作',
		'2'	=> 'オペレーター',
		'3'	=> '管理権限',
		'4'	=> 'システム管理者'
	],
	'list_convert_type'					=> [
		'0'		=> '通常',
		'1'		=> '顧客データ依存'
	],
	'roop_limit'						=> 10,
	'disp_achievements_limit'			=> 12,
	'disp_top_achievements_limit'		=> 6,
	'disp_top_voice_limit'				=> 4,
	'admin_open_type'					=> [
		['0', 'しない'],
		['1', 'する'],
	],
	'list_pay_type'						=> [
		'0'	=> '管理手動',
		'1' => '銀行振込',
		'2' => 'クレジット',
		'3' => 'ネットバンク',
		'4' => '不明',
		'5' => '不明',
	],
	'list_premium_convert_pay_type'		=> [
		'0'	=> '0',								//手動管理
		'1' => '2',								//クレジット
		'2' => '3',								//ネットバンク
		'3' => '1',								//銀行振込
		'4' => '4',								//
		'5' => '5',								//
	],	
	'list_pay_status'					=> [
		'1'	=> '入金待ち',		
		'2'	=> 'クレジット入金エラー',
		'6'	=> 'ネットバンク入金エラー',
		'0'	=> '入金済み(管理手動)',
		'3'	=> '入金済み',
		'4'	=> '返金',
		'5'	=> '注文未完了',
		'7'	=> 'キャンセル'
	],
	'list_history_pay_status'					=> [
		'1'	=> '入金待ち',		
		'2'	=> 'クレジット入金エラー',
		'6'	=> 'ネットバンク入金エラー',
		'0'	=> '入金済み',
		'3'	=> '入金済み',
		'4'	=> '返金',
		'5'	=> '注文未完了',
		'7'	=> 'キャンセル'
	],
	'settlement_status'					=> [
		'1'	=> '未',
		'2'	=> '入金ｴﾗｰ',
		'6'	=> '入金ｴﾗｰ',
		'0'	=> '済',
		'3'	=> '済',
		'4'	=> '返金',
		'5'	=> '未',
		'7'	=> 'ｷｬﾝｾﾙ'
	],
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
	'paymentlogs_credit_expire_minute'	=> 10080,				//7日間を分
	'paymentlogs_netbank_expire_minute'	=> 20160,				//14日間を分
	'personal_access_logs_expire_minute'=> 259200,				//180日間を分

	/*
	 * 管理画面-検索画面関連
	 */
	'search_exec_type_count_key'		=> 'count',
	'search_exec_type_export_key'		=> 'export',
	'search_exec_type_unexecuted_key'	=> 'unexecuted',
	'client_search_item'				=> [
		'login_id'		=> 'ログインID',
		'mail_address'	=> 'メールアドレス',
		'id'			=> '顧客ID',
		'ad_cd'			=> '広告ID',
	],
	'search_like_type'					=> [
		["=", "%s", '完全一致'],
		["like", "%s%%", '前方一致'],
		["like", "%%%s", '後方一致'],
		["like", "%%%s%%", '部分一致'],
	],
	'db_regist_status'					=> [
		'1' => '1',
		'0' => '0',
		'2' => '2',
		'3' => '3',
	],
	'migration_regist_status'			=> [
		'1' => '1',
		'2' => '0',
		'3' => '2',
		'4' => '3',
	],
	'regist_status'						=> [
		['1', '本登録'],
		['0', '仮登録'],
		['2', '退会'],
		['3', 'ブラック'],
	],
	'disp_regist_status'				=> [
		'1' => '本登録',
		'0' => '仮登録',
		'2' => '退会',
		'3' => 'ブラック',
	],
	'dm_status'							=> [
		['0,1', '関係なく'],
		['0', '受け取らない'],
		['1', '受け取る'],
	],
	'search_disp_num'					=> [
		'10',
		'20',
		'30',
		'40',
		'50',
		'60',
		'70',
		'80',
		'90',
		'100',
	],
	'sort_list'							=> [
		["id,asc", 'ID順'],
		["created_at,asc", '登録日時の古い順'],
		["created_at,desc", '登録日時の新しい順'],
	],
	'sort_ad_list'							=> [
		["", '指定なし'],
		["pv,desc", 'アクセス数：降順'],
		["pv,asc", 'アクセス数：昇順'],
		["amount,desc", '売上金額：降順'],
		["amount,asc", '売上金額：昇順'],
		["reg,desc", '登録者数：降順'],
		["reg,asc", '登録者数：昇順'],
		["temp_reg,desc", '仮登録者数：降順'],
		["temp_reg,asc", '仮登録者数：昇順'],
		["quit,desc", '退会者数：降順'],
		["quit,asc", '退会者数：昇順'],
		["active,desc", 'アクティブ数：降順'],
		["active,asc", 'アクティブ数：昇順'],
		["order_num,desc", '注文数：降順'],
		["order_num,asc", '注文数：昇順'],
		["pay,desc", '購入数：降順'],
		["pay,asc", '購入数：昇順'],
	],
	'excel_seet_name'					=> '顧客データ',
	'excel_file_name'					=> '顧客検索データ',

	/*
	 * 顧客検索-エクスポート関連
	 */
	//エクスポートファイルの先頭行の項目
	'export_file_header_column'			=> [
		'顧客ID',
		'ログインID',
//		'パスワード',
		'メールアドレス',
//		'携帯メールアドレス',
//		'identify_id',
//		'session_id',
//		'remember_token',
		'登録状態',
		'DM購読',
		'ポイント',
		'アクション回数',
		'入金回数',
		'入金金額',
		'グループ',
		'広告ID',
		'クレジット電話番号',
		'最終入金日時',
		'仮登録日時',
		'登録日時',
		'最終アクセス日時',
		'更新日時',
		'退会日時',
		'メモ'
	],
	'list_export_sort'					=> ['desc', 'asc'],
	
	/*
	 * 
	 */
	'save_upload_img_file_dir'			=> '/data/www/siteo/public/images/',
	
	
	/*
	 * 顧客データのインポート
	 */
	'save_import_file_dir'				=> '/data/www/siteo/storage/upload/',
	
	/*
	 * 顧客ステータス変更/削除
	 */
	'status_list'						=> [
		['id','顧客iD'],
		['ad_cd','広告コード'],
		['mail_address','メールアドレス']
	],
	'status_list_limit'					=> 10,
	
	/*
	 * 管理画面-編集関連
	 */
	'edit_dm_status'					=> [
		['0', '受け取らない'],
		['1', '受け取る'],
		['2', '強制停止'],
	],
	
	//広告コードのクッキー保存期限
	'aff_cookie_expire_time'			=> 300,

	/*
	 * ページ管理
	 */
	'page_search_item'					=> [
		['title', 'タイトル'],
		['id', 'ID']
	],
	'page_create_type'					=> [
		['2', 'レギュラー'],
		['1', 'キャンペーン'],
	],
	'page_type'							=> [
		['1,2', '関係なく'],
		['1', 'キャンペーン'],
		['2', 'レギュラー'],
	],
	'view_num'							=> [
		['0', 'しない'],
		['1', 'する'],
	],
	'page_sort_list'					=> [
		["sort_date,asc", '公開開始日時の古い順'],
		["sort_date,desc", '公開開始日時の新しい順'],
	],
	'page_link'								=> [
		['1', 'する'],
		['0', 'しない'],
	],
	'page_order_num'						=> [
		'1',
		'2',
		'3',
		'4',
		'5',
		'6',
		'7',
		'8',
		'9',
		'10',
		'11',
		'12',
		'13',
		'14',
		'15',
		'16',
		'17',
		'18',
		'19',
		'20',
		'21',
		'22',
		'23',
		'24',
		'25',
		'26',
		'27',
		'28',
		'29',
		'30'
	],
	'page_race_search_item'					=> [
		['id', 'ID'],
		['race_name', 'レース名']
	],
	'page_race_disp_type'					=> [
		['', '関係なく'],
		['1', '表示'],
		['0', '非表示'],
	],
	'page_race_sort_list'					=> [
		["id,asc", '作成日時の古い順'],
		["id,desc", '作成日時の新しい順'],
	],
	'page_voice_search_item'				=> [
		['id', 'ID'],
		['title', 'タイトル'],
		['name', '投稿者']
	],
	'none_post_name'						=> '匿名',
	'none_post_title'						=> '無題',

	/*
	 * 予想管理
	 */
	'forecast_search_item'					=> [
		['id', 'ID'],
		['title', 'タイトル'],
		['comment', 'コメント'],
		['detail', '内容']
	],
	'forecast_disp_type'					=> [
		['', '関係なく'],
		['1', 'する'],
		['0', 'しない'],
	],
	'forecast_sort_list'					=> [
		["disp_sdate,asc", '表示開始日時の古い順'],
		["disp_sdate,desc", '表示開始日時の新しい順'],
	],
	'forecast_category'						=> [
		['1', '無料'],
		['2', 'キャンペーン']
	],
	'forecast_disp_category'				=> [
		'1' => '無料',
		'2' => 'キャンペーン'
	],
	'forecast_search_category'				=> [
		['', '関係なく'],
		['1', '無料'],
		['2', 'キャンペーン']
	],

	/*
	 * ランディングページ
	 */
	'lp_default_page'						=> [
		'index',
		'rule',
		'privacy',
		'company',
		'done',
		'css.css',
		'js.js'
	],
	'lp_search_item'					=> [
		['landing_pages.id', 'LP ID'],
		['landing_pages.memo', 'MEMO'],
		['landing_pages_contents.content', 'Content(html)']
	],
	'lp_disp_type'					=> [
		['', '関係なく'],
		['1', '表示'],
		['0', '非表示'],
	],
	'lp_sort_list'					=> [
		["id,asc", '古い順'],
		["id,desc", '新しい順'],
	],

	/*
	 * メルマガ関連
	 */
	'melmaga_send_method'					=> [
		'0' => 'メールサーバー',
		'1' => 'リレーサーバー'
	],
	'melmaga_search_item'					=> [
		['users.id', '顧客ID'],
		['users.mail_address', 'E-Mail'],
		['users.ad_cd', '広告コード'],
	],
	'melmaga_search_type'					=> [
		['0', 'を含む'],
		['1', 'を含まない'],
	],
	'melmaga_settlement_status'				=> [
		['', '関係なく'],
		['0', 'キャンペーン'],
		['1', 'ポイント']
	],
	'melmaga_regist_career'					=> [
		['', '関係なく'],
		['1', 'PC'],
		['2', 'docomo'],
		['3', 'au'],
		['4', 'softbank'],
		['5', 'WILLCOM'],
		['6', 'iPhone'],
		['7', 'android']
	],
	'melmaga_device'					=> [
		['1', 'MB'],
		['2', 'PC'],
	],
	'registered_search_item'			=> [
		['registered_mails.id', 'ID'],
		['registered_mails.title', 'タイトル'],
		['registered_mails.remarks', '備考']
	],
	'registered_send_item'				=> [
		'client_id',
		'mail',
		'ad_cd'
	],
	'registered_like_type'				=> [
		'in',
		'not in'
	],
	'registered_specified_time'			=> [15,30,45,60,75,90,105,120],
	'registered_enable_disable'			=> [
		['', '関係なく'],
		['1', '有効'],
		['0', '無効']
	],
	'registered_disp_enable_disable'	=> [
		'1' => '○',
		'0' => '×'
	],
	'registered_sort_list'				=> [
		["created_at,asc", '古い順'],
		["created_at,desc", '新しい順'],
	],

	/*
	 * 
	 */
	'trends_client_excel_seet_name'		=> '顧客別データ',
	'trends_pay_excel_seet_name'		=> '購入回数別データ',
	'trends_client_excel_file_name'		=> '顧客別データ',
	'trends_pay_excel_file_name'		=> '購入回数別データ',

	/*
	 * 広告関連
	 */
	'ad_category'						=> [
		'1'	=> 'WEB純稿',
		'2'	=> '自社',
		'3'	=> 'アプリ',
		'4'	=> '通知バー',
		'5'	=> 'SSP',
		'6'	=> 'CSV',
		'7'	=> '雑誌',
		'8'	=> 'その他',
	],
	'ad_search_item'					=> [
		['ad_cd', 'コード'],
		['name', '名称'],
		['url', 'URL']
	],
	'ad_media_search_item'					=> [
		['result_ad_logs.ad_cd', '広告コード'],
		['name', '広告名称'],
	],

	/*
	 * ログ関連
	 */
	'payment_log_dir_path'				=> '/data/www/siteo/storage/logs/payment_log/',
	'system_log_dir_path'				=> '/data/www/siteo/storage/logs/syslogs/',
	'client_log_dir_path'				=> '/data/www/siteo/storage/logs/client_logs/',
	'payment_log_name'					=> '決済結果ログ',
	'payment_log_credit_file_name'		=> 'credit_',
	'payment_log_netbank_file_name'		=> 'netbank_',
	'operation_export_log_name'			=> '操作ログ',
	'operation_export_file_name'		=> 'excel_export.log',
	'operation_trends_export_file_name'	=> 'trends_excel_export.log',
	'operation_history_file_name'		=> 'operation_history.log',
	'operation_point_history_file_name'	=> 'operation_point_history.log',
	'operation_point_log_name'			=> 'ポイント操作ログ',
	'client_member_history_file_name'	=> 'client_member_history.log',
	'client_nonmember_file_name'		=> 'client_nonmember_access.log',
	'client_access_file_name'			=> 'client_access_history.log',
	'client_history_log_name'			=> '会員ログ',
	'client_nonmember_log_name'			=> '非会員ログ',
	'import_error_log_name'				=> '重複登録',
	'import_mx_domain_error_log_name'	=> '不明メールドメイン',
	'import_failed_log_name'			=> '登録失敗',
	'system_error_log_name'				=> 'システムエラー',
	'import_mx_domain_error_file_name'	=> 'mx_domain_entry.log',
	'import_error_file_name'			=> 'duplicate_entry.log',
	'import_error_email_file_name'		=> 'bad_email_entry.log',
	'import_failed_create_id_file_name'	=> 'failed_entry.log',
	'system_error_file_name'			=> 'system_error.log',
	'dl_unknown_mx_domain_file_name'	=> '不明ドメインリスト.csv',
	'dl_duplicate_file_name'			=> '重複登録リスト.csv',
	'admin_display_list'				=> [
		'client_bulk_delete'	=> 'クライアント一覧-一括削除処理',
		'client_search'			=> 'クライアント検索処理',
		'client_edit_update'	=> 'クライアント編集-更新処理',
		'client_edit_update_err'=> 'クライアント編集-同時更新処理で失敗',
		'client_edit_delete'	=> 'クライアント編集-削除処理',
		'client_point_add'		=> 'クライアント編集-ポイント追加',
		'client_point_add_err'	=> 'クライアント編集-ポイント追加失敗',
		'client_mail'			=> 'クライアント編集-個別メール送信',
		'client_mail_history'	=> 'クライアント編集-送信履歴',
		'client_order_add'		=> 'クライアント編集-注文追加',
		'client_order_point_add'=> 'クライアント編集-ポイント注文追加',
		'client_order_add_err'	=> 'クライアント編集-注文追加失敗',
		'client_order_history'	=> 'クライアント編集-注文履歴',
		'client_status_change'	=> '顧客ステータス変更-検索処理',
		'client_import'			=> '顧客データインポート処理',
		'client_export'			=> '顧客データエクスポート処理',
		'trends_client_export'	=> '購買動向分析-顧客別エクスポート処理',
		'trends_pay_export'		=> '購買動向分析-購入回数別エクスポート処理',
		'trends_search'			=> '購買動向分析-検索処理',
		'info_mail_delete'		=> '受信メール-削除',
		'info_mail_reply'		=> '受信メール-返信',
		'info_mail_search'		=> '受信メール-検索処理',
		'top_content_search'	=> 'TOPコンテンツ-検索処理',
		'top_content_create'	=> 'TOPコンテンツ-作成処理',
		'top_content_edit'		=> 'TOPコンテンツ-更新処理',
		'top_content_delete'	=> 'TOPコンテンツ-削除処理',
		'top_content_update'	=> 'TOPコンテンツ-一括更新',
		'top_banner_set'		=> 'TOPバナー設定-設定',
		'top_banner_upload'		=> 'TOPバナー設定-画像アップロード',
		'group_bulk_move'		=> 'グループ-グループ一括移行',
		'group_add'				=> 'グループ-追加処理',
		'group_update'			=> 'グループ-更新処理',
		'group_delete'			=> 'グループ-削除処理',
		'group_search'			=> 'グループ-検索処理',
		'group_move'			=> 'グループ-移行',
		'group_move_err'		=> 'グループ-移行失敗',
		'voice_search'			=> 'ご利用者の声-検索処理',
		'voice_create'			=> 'ご利用者の声-作成処理',
		'voice_edit'			=> 'ご利用者の声-更新処理',
		'voice_delete'			=> 'ご利用者の声-削除処理',
		'voice_update'			=> 'ご利用者の声-一括更新処理',
		'voice_banner_upload'	=> 'ご利用者の声-画像アップロード',
		'achievement_search'	=> '的中実績-検索処理',
		'achievement_create'	=> '的中実績-作成処理',
		'achievement_edit'		=> '的中実績-更新処理',
		'achievement_delete'	=> '的中実績-削除処理',
		'achievement_update'	=> '的中実績-一括更新処理',
		'top_product_create'	=> '商品設定-作成処理',
		'top_product_search'	=> '商品設定-検索処理',
		'top_product_edit'		=> '商品設定-編集処理',
		'top_product_delete'	=> '商品設定-削除処理',
		'top_product_update'	=> '商品設定-一括更新処理',
		'forecasts_create'		=> '予想-作成処理',
		'forecasts_search'		=> '予想-検索処理',
		'forecasts_edit'		=> '予想-更新処理',
		'forecasts_delete'		=> '予想-削除処理',
		'forecasts_update'		=> '予想-一括削除処理',
		'forecasts_visitor'		=> '閲覧者検索-検索処理',
		'lp_create'				=> 'ランディング-作成処理',
		'lp_search'				=> 'ランディング-検索処理',
		'lp_edit'				=> 'ランディング-編集',
		'lp_update'				=> 'ランディング-更新処理',
		'lp_delete'				=> 'ランディング-削除処理',
		'lp_img_upload'			=> 'ランディング-画像アップロード処理',	
		'lp_img_delete'			=> 'ランディング-アップロード画像削除処理',	
		'lp_add_page'			=> 'ランディング-ページ追加処理',
		'lp_update_page'		=> 'ランディング-ページ更新処理',
		'lp_delete_page'		=> 'ランディング-ページ削除処理',
		'point_add'				=> 'ポイント加算',
		'point_sub'				=> 'ポイント減算',
		'point_except_err'		=> 'ポイント加算/減算処理-例外エラー',
		'point_grant'			=> '付与ポイント設定',
		'point_category_add'	=> 'ポイントカテゴリ追加処理',
		'point_category_update'	=> 'ポイントカテゴリ更新処理',
		'point_setting_add'		=> 'ポイント設定追加処理',
		'point_setting_update'	=> 'ポイント設定更新処理',
		'magnification_update'	=> '倍率設定更新処理',
		'client_create'			=> 'クライアント新規作成-作成処理',
		'client_create_err'		=> 'クライアント新規作成-作成処理失敗',
		'convert_list_update'	=> '%変換設定-更新処理',
		'convert_key_add'		=> '%変換設定-変換キー追加処理',
		'auto_mail_update'		=> '自動メール文設定-更新処理',
		'sentence_update'		=> '出力文言設定-更新処理',
		'img_upload'			=> '出力用画像設定-アップロード',
		'img_upload_delete'		=> '出力用画像設定-画像削除',
		'banner_add'			=> 'バナー設定-バナー追加',
		'banner_update'			=> 'バナー設定-バナー更新',
		'banner_delete'			=> 'バナー設定-バナー削除',
		'relay_server_update'	=> 'リレーサーバー設定-更新処理',
		'ng_word_update'		=> 'メールアドレス禁止ワード設定-更新処理',
		'account_create'		=> '管理アカウント新規作成-新規作成処理',
		'account_edit'			=> '管理アカウント更新処理',
		'account_delete'		=> '管理アカウント削除処理',
		'login'					=> 'ログイン',
		'logout'				=> 'ログアウト',
		'password_update'		=> 'パスワード変更処理',
		'analysis_top'			=> 'アクセス解析-年',
		'analysis_month'		=> 'アクセス解析-月',
		'analysis_day'			=> 'アクセス解析-日',
		'pv_log_top'			=> 'PVログ-年',
		'pv_log_month'			=> 'PVログ-月',
		'sales_summary_top'		=> '売上集計-年',
		'sales_summary_month'	=> '売上集計-月',
		'sales_summary_day'		=> '売上集計-日',
		'payment_struct_top'	=> '入金構成-年',
		'payment_struct_month'	=> '入金構成-月',
		'payment_struct_day'	=> '入金構成-日',
		'newpayment_top'		=> '新規入金-年',
		'newpayment_month'		=> '新規入金-月',
		'newpayment_day'		=> '新規入金-日',
		'sales_struct'			=> '売上構成',
		'adcode_top'			=> '広告コード-トップ',
		'adcode_create'			=> '広告コード-新規作成処理',
		'adcode_search'			=> '広告コード-検索処理',
		'adcode_edit'			=> '広告コード-編集処理',
		'adcode_del'			=> '広告コード-削除処理',
		'agency_top'			=> '代理店-トップ',
		'agency_create'			=> '代理店-新規作成処理',
		'agency_edit'			=> '代理店-編集処理',
		'agency_del'			=> '代理店-削除処理',
		'user_statistics_top'	=> '利用統計-年',
		'user_statistics_month'	=> '利用統計-月',
		'user_statistics_day'	=> '利用統計-日',
		'media_top'				=> '媒体集計-トップ',
		'media_search'			=> '媒体集計-検索処理',
		'melmaga_search'		=> 'メルマガ-即時配信-配信先抽出',
		'melmaga_send_immediate'=> 'メルマガ-即時配信-メルマガ送信ボタン押下',
		'melmaga_history'		=> 'メルマガ配信履歴表示',
		'melmaga_history_view'	=> 'メルマガ配信履歴-メルマガ確認画面表示',
		'melmaga_failed'		=> 'メルマガ送信失敗画面表示',
		'melmaga_failed_list'	=> 'メルマガ送信失敗画面-失敗送信リスト画面表示',
		'melmaga_reserve'		=> 'メルマガ予約画面表示',
		'melmaga_reserve_search'=> 'メルマガ-予約配信-配信先抽出',
		'melmaga_reserve_send'	=> 'メルマガ-予約配信-配信予約するボタン押下',
		'melmaga_reserve_status'=> 'メルマガ-予約状況',
		'melmaga_reserve_cancel'=> 'メルマガ-予約キャンセル',
		'melmaga_reserve_update'=> 'メルマガ-予約メルマガ更新処理',
		'registered_top'		=> 'メルマガ-登録後送信メール画面表示',
		'registered_create'		=> 'メルマガ-登録後送信メール-新規作成処理',
		'registered_update'		=> 'メルマガ-登録後送信メール-更新処理',
		'registered_search'		=> 'メルマガ-登録後送信メール-検索処理',
		'registered_delete'		=> 'メルマガ-登録後送信メール-一括削除処理',
		'confirm_add'			=> '確認アドレス設定-追加処理',
		'confirm_delete'		=> '確認アドレス設定-削除処理',
		'history_status_change'	=> '注文情報詳細-ステータス変更',
	],
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
	'mail_from_name'				=> '統合サイト',
	'mail_info_subject'				=> '【統合サイト】です。',
	'mail_regist_subject'			=> '仮登録完了のご連絡',
	'mail_comp_regist_subject'		=> '【本登録】が完了致しました!',
	
	//管理用
	'mail_admin_from_name'			=> '統合サイト 運営管理',
	'mail_admin_info_subject'		=> '【統合サイト 運営管理】です。',
	'mail_admin_regist_subject'		=> '【管理画面 アカウント登録】が完了致しました',
	'mail_admin_provision_subject'	=> '【管理画面】へのパスワードを設定してください',
	'mail_admin_password_subject'	=> '【管理画面】へのパスワード設定が完了しました',
	
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
	'product_order'					=> 'emails.product_order',
	'payment_comp'					=> 'emails.payment_comp',

	//管理用
	'admin_regist'					=> 'emails.admin_regist',
	'admin_provision_regist'		=> 'emails.admin_provision_regist',
	'admin_forget'					=> 'emails.admin_forget',
	'admin_password_setting'		=> 'emails.admin_password_setting',
	'admin_password_change'			=> 'emails.admin_password_change',
	'admin_edit_mail'				=> 'emails.edit_mail',
	'admin_edit_html_mail'			=> 'emails.edit_html_mail',
]);
