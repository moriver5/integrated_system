<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('const.html_admin_title') }}</title>

    <!-- Styles -->
	<link rel="preload" href="{{ asset('css/admin/app.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link rel="stylesheet" href="{{ asset('css/admin/app.css') }}"></noscript>
	<link href="{{ asset('css/admin/allow.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/admin.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/colorbox.css') }}" rel="stylesheet" />
	<!-- グラフcss読み込み -->


	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- グラフJavascript読み込み -->
	<script defer src="https://code.highcharts.com/highcharts.src.js"></script>

	<!-- JavaScript Library -->
	<script async src="{{ asset('js/admin/utility.js') }}"></script>

	<script>
	//高速化のCSSファイルの非同期読込みのためにloadCSSを読込む
	(function(a){if(!a.loadCSS){a.loadCSS=function(){}}var b=loadCSS.relpreload={};b.support=(function(){var d;try{d=a.document.createElement("link").relList.supports("preload")}catch(f){d=false}return function(){return d}})();b.bindMediaToggle=function(e){var f=e.media||"all";function d(){e.media=f}if(e.addEventListener){e.addEventListener("load",d)}else{if(e.attachEvent){e.attachEvent("onload",d)}}setTimeout(function(){e.rel="stylesheet";e.media="only x"});setTimeout(d,3000)};b.poly=function(){if(b.support()){return}var d=a.document.getElementsByTagName("link");for(var e=0;e<d.length;e++){var f=d[e];if(f.rel==="preload"&&f.getAttribute("as")==="style"&&!f.getAttribute("data-loadcss")){f.setAttribute("data-loadcss",true);b.bindMediaToggle(f)}}};if(!b.support()){b.poly();var c=a.setInterval(b.poly,500);if(a.addEventListener){a.addEventListener("load",function(){b.poly();a.clearInterval(c)})}else{if(a.attachEvent){a.attachEvent("onload",function(){b.poly();a.clearInterval(c)})}}}if(typeof exports!=="undefined"){exports.loadCSS=loadCSS}else{a.loadCSS=loadCSS}}(typeof global!=="undefined"?global:this));

	$(document).ready(function () {
		// サイドバーの初期化
//		$(".sidebar.left").sidebar({side: 'left'});

		// サイドバーボタンのクリック
		$(".btn[data-action],.navbar-brand[data-action]").on("click", function () {
			var $this = $(this);
			var action = $this.attr("data-action");
			var side = $this.attr("data-side");
			$(".sidebar." + side).trigger("sidebar:" + action);
			return false;
		});

	});
	</script>

	<style>
	/*
		点滅
	*/
	@-webkit-keyframes pulse {
	 from {
	   opacity: 1.0;/*透明度100%*/
	 }
	 to {
	   opacity: 0.2;/*透明度80%*/
	 }
	}
	.blinking{
	-webkit-animation-name: pulse;/* 実行する名前 */
	-webkit-animation-duration: 0.4s;/* 0.3秒かけて実行 */
	-webkit-animation-iteration-count:infinite;/* 何回実行するか。infiniteで無限 */
	-webkit-animation-timing-function:ease-in-out;/* イーズインアウト */
	-webkit-animation-direction: alternate;/* alternateにするとアニメーションが反復 */
	-webkit-animation-delay: 0s; /* 実行までの待ち時間 */
	}

	/*
		サイバーメニュー
	*/
	#app{
		block:none;
	}
	.sidebar{
		height:100%;
		position: fixed;
		color: #CEE3F6;
		border-right:1px solid #c0c0c0;
	}
 
    .sidebar.left {
		top: 0;
		left: 0;
		bottom: 0;
		width: 170px;
		background: wheat;
		word-wrap: break-word;
    }
	
	.dropdown a{
		background:wheat;
		border-bottom:1px solid burlywood;
	}

	.loginuser a{
		background:wheat;
	}
	
	/* 出力文言設定-タブメニュー */
	#tab-menu {
	  list-style: none;
	}
	
	#tab-menu li {
		background:      -o-linear-gradient(top, #ECECEC 0%, #D1D1D1 100%);
		background:     -ms-linear-gradient(top, #ECECEC 50%, #D1D1D1 100%);
		background:    -moz-linear-gradient(top, #ECECEC 50%, #D1D1D1 100%);
		background: -webkit-linear-gradient(top, #ECECEC 50%, #D1D1D1 100%);
		background: linear-gradient(top, #ECECEC 50%, #D1D1D1 100%);
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.4), inset 0 1px 0 #FFF;
		text-shadow: 1px 1px #FFF;
		margin: 0 -3px;
		display: inline-block;
		padding: 5px 11px;
		background: wheat;
		font-size:12px;
	}

	#tab-menu li.active {
		font-weight:bold;
		border-bottom:1px solid white;
		background: ivory;
		color: dimgray;
	}

	/* 出力文言設定-タブの中身 */
	#tab-box {
	  padding: 25px;
	}
	#tab-box div {
	  display: none;
	}
	#tab-box div.active {
	  display: block;
	}
	</style>
</head>
<body class="drawer drawer--left">

    <div id="app">
		@if ( !empty(Auth::guard('admin')->check()) )
		<nav class="navbar navbar-default navbar-static-top sidebar left">
			<div class="collapse navbar-collapse">
				<!-- Branding Image -->
				<div class="navbar-brand" style="font-family:arial black;" data-action="toggle" data-side="left">
					<b>
					@if( !empty(Session::get('operation_db_name')) )
						{{ Session::get('operation_db_name') }}
					@else
						なし
					@endif
					</b>
				</div>
				<br />
				<br />
				<br />
					@if( !empty(Session::get('operation_db_name')) )
					<ul class="nav drawer-menu" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								サイト管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="{{ url('/admin/member/site/select') }}" class="dropdown-toggle" role="button" aria-expanded="false">サイト選択</a>
								</li>
							</ul>
						</li>
					</ul>
					<!-- 管理メニュー -->
					<ul class="nav drawer-menu" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								管理者管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="{{ url('/admin/member') }}" class="dropdown-toggle" role="button" aria-expanded="false">管理者一覧</a>
									<a href="{{ url('/admin/member/client/export/opeartion/log') }}" class="dropdown-toggle" role="button" aria-expanded="false">顧客データエクスポートログ</a>
								</li>
							</ul>
						</li>
					</ul>
					<ul class="nav drawer-menu" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								info<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="{{ url('/admin/member/info') }}" class="dropdown-toggle" role="button" aria-expanded="false">受信メール一覧</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav drawer-menu" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								顧客管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="{{ url('/admin/member/client') }}" class="dropdown-toggle" role="button" aria-expanded="false">登録者一覧</a>
									<a href="{{ url('/admin/member/client/group') }}" class="dropdown-toggle" role="button" aria-expanded="false">グループ移行</a>
									<a href="{{ url('/admin/member/client/import') }}" class="dropdown-toggle" role="button" aria-expanded="false">顧客データインポート</a>
									<a href="#" id="client_export" class="dropdown-toggle" role="button" aria-expanded="false">顧客データエクスポート</a>
									<a href="{{ url('/admin/member/client/status') }}" class="dropdown-toggle" role="button" aria-expanded="false">顧客ステータス変更/削除</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav drawer-menu" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								グループ管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="{{ url('/admin/member/group') }}" class="dropdown-toggle" role="button" aria-expanded="false">グループ一覧</a>
									<a href="{{ url('/admin/member/group/move/bulk') }}" class="dropdown-toggle" role="button" aria-expanded="false">グループ一括移行</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								予想管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/forecast" class="dropdown-toggle" role="button" aria-expanded="false">予想一覧</a>
									<a href="/admin/member/forecast/visitor/search" class="dropdown-toggle" role="button" aria-expanded="false">閲覧者検索</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								ページ管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/page" class="dropdown-toggle" role="button" aria-expanded="false">TOPコンテンツ</a>
									<a href="/admin/member/page/product" class="dropdown-toggle" role="button" aria-expanded="false">商品設定</a>
									<a href="/admin/member/page/achievement" class="dropdown-toggle" role="button" aria-expanded="false">的中実績</a>
									<a href="/admin/member/page/voice" class="dropdown-toggle" role="button" aria-expanded="false">ご利用者の声</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								ランディングページ<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/lp" class="dropdown-toggle" role="button" aria-expanded="false">LP一覧</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								マスタ管理<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/master/sentence/setting" class="dropdown-toggle" role="button" aria-expanded="false">出力文言設定</a>
									<a href="/admin/member/master/img/upload" class="dropdown-toggle" role="button" aria-expanded="false">出力用画像設定</a>
									<a href="/admin/member/master/relayserver/setting" class="dropdown-toggle" role="button" aria-expanded="false">リレーサーバー設定</a>
									<a href="/admin/member/master/mailaddress_ng_word/setting" class="dropdown-toggle" role="button" aria-expanded="false">禁止ワード設定</a>
									<a href="/admin/member/master/mail_sentence/setting" class="dropdown-toggle" role="button" aria-expanded="false">自動メール文設定</a>
									<a href="/admin/member/master/purchase/point/category/setting" class="dropdown-toggle" role="button" aria-expanded="false">購入ポイント設定</a>
									<a href="/admin/member/master/grant/point" class="dropdown-toggle" role="button" aria-expanded="false">付与ポイント</a>
									<a href="/admin/member/master/convert/setting" class="dropdown-toggle" role="button" aria-expanded="false">%変換設定</a>
									<a href="/admin/member/master/banner/setting" class="dropdown-toggle" role="button" aria-expanded="false">バナー設定</a>
									<a href="/admin/member/master/confirm/email/setting" class="dropdown-toggle" role="button" aria-expanded="false">確認アドレス設定</a>
									<a href="/admin/member/maintenance/setting" class="dropdown-toggle" role="button" aria-expanded="false">メンテナンス設定</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								配信<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/melmaga" class="dropdown-toggle" role="button" aria-expanded="false">メルマガ</a>
									<a href="/admin/member/melmaga/mail/history" class="dropdown-toggle" role="button" aria-expanded="false">メルマガ配信履歴</a>
									<a href="/admin/member/melmaga/reserve/status" class="dropdown-toggle" role="button" aria-expanded="false">メルマガ予約状況</a>
									<a href="/admin/member/melmaga/reserve" class="dropdown-toggle" role="button" aria-expanded="false">予約メルマガ</a>
									<a href="/admin/member/melmaga/registered/mail" class="dropdown-toggle" role="button" aria-expanded="false">登録後送信メール</a>
									<a href="/admin/member/melmaga/mail/failed/list" class="dropdown-toggle" role="button" aria-expanded="false">送信失敗一覧</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								集計<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/analytics/purchasing_trends" class="dropdown-toggle" role="button" aria-expanded="false">購買動向分析</a>
									<a href="/admin/member/analytics/statistics/access" class="dropdown-toggle" role="button" aria-expanded="false">利用統計</a>
									<a href="/admin/member/analytics/sales/summary" class="dropdown-toggle" role="button" aria-expanded="false">売上集計</a>
									<a href="/admin/member/analytics/pv/access" class="dropdown-toggle" role="button" aria-expanded="false">PVログ</a>
									<a href="/admin/member/analytics/payment/structure" class="dropdown-toggle" role="button" aria-expanded="false">入金構成</a>
									<a href="/admin/member/analytics/newpayment" class="dropdown-toggle" role="button" aria-expanded="false">新規入金</a>
									<a href="/admin/member/analytics/sales/structure" class="dropdown-toggle" role="button" aria-expanded="false">売上構成</a>
									<a href="/admin/member/analytics/event" class="dropdown-toggle" role="button" aria-expanded="false">イベント効果</a>
									<a href="/admin/member/analytics/access" class="dropdown-toggle" role="button" aria-expanded="false">アクセス解析</a>
									<a href="/admin/member/analytics/melmaga/access" class="dropdown-toggle" role="button" aria-expanded="false">メルマガ解析</a>
									<a href="/admin/member/analytics/products/pay" class="dropdown-toggle" role="button" aria-expanded="false">商品解析</a>
									<a href="/admin/member/analytics/products/repeat" class="dropdown-toggle" role="button" aria-expanded="false">リピートユーザー解析</a>
									<a href="/admin/member/analytics/payment/report" class="dropdown-toggle" role="button" aria-expanded="false">決済レポート</a>
								</li>
							</ul>
						</li>
					</ul>

					<ul class="nav" style="border-top:1px solid white;border-bottom:1px solid white;">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								広告<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown">
									<a href="/admin/member/ad/adcode" class="dropdown-toggle" role="button" aria-expanded="false">広告コード</a>
									<a href="/admin/member/ad/media" class="dropdown-toggle" role="button" aria-expanded="false">媒体集計</a>
									<a href="/admin/member/ad/agency" class="dropdown-toggle" role="button" aria-expanded="false">代理店</a>
								</li>
							</ul>
						</li>
					</ul>
					@endif
				@endif
			</div>

			<div class="collapse navbar-collapse" id="app-navbar-collapse">
				<!-- Left Side Of Navbar -->
				<ul class="nav navbar-nav">
					&nbsp;
				</ul>
			</div>

			<div class="collapse navbar-collapse">
				<!-- Right Side Of Navbar -->
				<ul class="nav">
					<!-- Authentication Links -->
					@if ( empty(Auth::guard('admin')->check()) )
						<li style="float:right;"><a href="/admin/regist">アカウント登録</a></li>
						<li style="float:right;"><a href="/admin/login">ログイン</a></li>
					@else
						<li class="loginuser">
							<a href="#" class="login" data-toggle="dropdown" role="button" aria-expanded="false">
								<span style="font-family:arial black;">LOGIN USER</span><br />{{ Auth::guard('admin')->user()->email }}<span class="caret"></span>
							</a>

							<ul class="dropdown-menu" role="menu">
								<li>
									<a href="/admin/logout" style="font-family:Meiryo;"
										onclick="event.preventDefault();
												 document.getElementById('logout-form').submit();">
											 <b>LOGOUT</b>
									</a>

									<form id="logout-form" action="/admin/logout" method="POST" style="display: none;">
										{{ csrf_field() }}
									</form>
								</li>
							</ul>
						</li>

					@endif
				</ul>
			</div>
		</nav>
		@endif

		@if ( empty(Auth::guard('admin')->check()) )
			<nav class="navbar navbar-default navbar-static-top">
				<div style="float:right;" data-action="toggle" data-side="left">
					<!-- Right Side Of Navbar -->
					<ul class="nav">
						<li style="float:right;"><a href="/admin/regist">アカウント登録</a></li>
						<li style="float:right;"><a href="/admin/login">ログイン</a></li>
					</ul>
				</div>
			</nav>
		@else
			<div class="btn" style="float:left;font-family:arial black;" data-action="toggle" data-side="left">
				<b>MENU</b>
			</div>
			<div style="right:0;position:fixed;float:right;margin:10px;border:3px dotted black;background:yellow;padding:10px;font-weight:bold;">
				<span style="color:black;">操作サイト：</span><span style="color:red;font-weight:bolder;">
				@if( !empty(Session::get('operation_db_name')) )
					{{ Session::get('operation_db_name') }}
				@else
					なし
				@endif
				</span>
			</div>
		@endif

		@yield('content')

    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
	
	<!-- Javascript library -->
	<script async src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="{{ asset('js/admin/jquery.sidebar.min.js') }}"></script>

	<script async type="text/javascript">
	var search_win;
	$(document).ready(function(){
		$('#client_export').on('click', function(){
			window.location.href = '/admin/member/client';
			search_win = window.open('/admin/member/client/search/setting', 'convert_table', 'width=700, height=655');
			return false;
		});
	});
	</script>

<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>
<script async src="{{ asset('js/admin/jquery.colorbox-min.js') }}"></script>
</body>
</html>
