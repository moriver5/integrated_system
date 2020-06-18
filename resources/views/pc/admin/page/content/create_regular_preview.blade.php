<!DOCTYPE HTML><html lang=ja><head>
<link rel=dns-prefetch href="//https:cdnjs.cloudflare.com">
<link rel=dns-prefetch href="//https:ajax.googleapis.com">
<link rel=dns-prefetch href="//https:www.ipat.jra.go.jp">
<link rel=dns-prefetch href="//http:www.jra.go.jp">
<link rel=dns-prefetch href="//https:namiboat.site">
<meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1.0">
<meta name=format-detection content="telephone=no">
<meta name=csrf-token content=eWpMe3NmpuTJN4HfwDXt2EhMOgmlckC7XAkc5f6p>
<title>会員-[情報公開-レギュラー]</title>
<link rel=stylesheet href="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}/css/common/magnific-popup.css">
<link rel=stylesheet href="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}/css/common.css">
<link rel=stylesheet href="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}/css/pushy.css">
<link rel=stylesheet href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.css">
<link rel=stylesheet href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css">
<script src=https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js></script>
<script src=https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js>
</script><script src=https://cdnjs.cloudflare.com/ajax/libs/jquery.inview/1.0.0/jquery.inview.min.js>
</script><script src=/js/script.js></script>
<script>
	$(function(){$('.slick_slider1').slick({autoplay:true,arrows:false,dots:true,pauseOnHover:false,slidesToShow:3,slidesToScroll:1,responsive:[{breakpoint:999,settings:{slidesToShow:1,centerMode:true,centerPadding:'10%'}}]});$('.slick_slider2').slick({autoplay:true,arrows:false,dots:true,centerMode:true,centerPadding:'10%'});});</script>
</head>
<body id=state_in>
<header id=header>
<div class=inner><h1 class=logo><a href="/member/home"><img src={{ config('const.list_url_const')[Session::get('operation_select_db')] }}/images/h_logo.png alt="GOLDEN★STARS(ゴールデン★スターズ)"></a></h1>
<div class=member><ul><li>会員ID：</li><li>所持ポイント：pt</li></ul></div></div>
<nav class="hnav view_sp">
	<ul>
		<li><a href="/member/settlement">商品購入</a></li>
		<li><a href="/member/expectation/list">情報公開</a></li>
		<li><a href="/member/hit">的中実績</a></li>
		<li><a href="/member/mailbox">メールBOX</a></li>
	</ul>
</nav>
</header>
	<div id=container>
		<main><h1 class=ttl_01>重賞有力情報</h1>
			<div class=area_01>
				<figure class=mb_M>
					<div id="img_preview" alt="重賞有力情報"></div>
				</figure>
				<dl class=box_02><dt>情報内容</dt>
					<div id="create_preview">
					@if( !empty($db_data->html_body) )
						{!! $db_data->html_body !!}
					@endif
					</div>
				</dl>
			</div>
		</main>
		<aside>
			<p class=btn_cart><a href="/member/settlement"><span>商品購入</span></a></p>
			<nav class=snav>
				<ul>
					<li class=home><a href="/member/home">HOME</a></li>
					<li class=guide><a href="/member/guide">簡単ご利用方法</a></li>
					<li class=expectation><a href="/member/expectation/list">情報公開</a></li>
					<li class=hit><a href="/member/hit">的中実績</a></li>
					<li class=mail><a href="/member/mailbox">メールBOX</a></li>
					<li class=voice><a href="/member/voice">会員様の声</a></li>
					<li class=info><a href="/member/info">お問い合わせ</a></li>
					<li class=setting><a href="/member/setting">会員情報変更</a></li>
				</ul>
			</nav>
		</aside>
	</div>
		<footer id=footer>
			<div class=inner>
				<nav class=view_pc>
					<ul class=fnav>
						<li><a href="/member/privacy">プライバシーポリシー</a></li>
						<li><a href="/member/outline">特定商取引法に基づく表記</a></li>
						<li><a href="/member/rule">利用規約</a></li>
						<li><a href="/member/legal">競馬法に関する特記事項</a></li>
					</ul></nav>
				<div class=flogo>
					<p class=logo><img src={{ config('const.list_url_const')[Session::get('operation_select_db')] }}/images/f_logo.png alt="ゴールデンスターズ"></p>
					<p class=copyright>&copy; Golden Stars All Rights Reserved. </p>
				</div>
			</div>
		</footer>
		<div class=btn_pagetop><a href="#pagetop">
				<img src={{ config('const.list_url_const')[Session::get('operation_select_db')] }}/images/btn_pagetop.png alt="ページトップへ"></a>
		</div>
		<div class="menu-btn view_sp"><img src={{ config('const.list_url_const')[Session::get('operation_select_db')] }}/images/btn_menu01.png alt="メニュー"></div>
		<div class=site-overlay></div>
		<nav class="pushy pushy-right">
			<div class=pushy-link>&times;</div>
			<ul>
				<li><a href="/member/home">HOME</a></li>
				<li><a href="/member/guide">簡単ご利用方法</a></li>
				<li><a href="/member/expectation/list">情報公開</a></li>
				<li><a href="/member/hit">的中実績</a></li>
				<li><a href="/member/mailbox">メールBOX</a></li>
				<li><a href="/member/voice">会員様の声</a></li>
				<li><a href="/member/info">お問い合わせ</a></li>
				<li><a href="/member/setting">会員情報変更</a></li>
				<li><a href="/member/privacy">プライバシーポリシー</a></li>
				<li><a href="/member/rule">利用規約</a></li>
				<li><a href="/member/outline">特定商取引法に基づく表記</a></li>
				<li><a href="/member/legal">競馬法に関する特記事項</a></li>
			</ul>
		</nav>
</body>
</html>