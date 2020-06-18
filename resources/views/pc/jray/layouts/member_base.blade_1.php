<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta name="robots" content="noindex,nofollow">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }}</title>
<link rel="stylesheet" href="/css/common/magnific-popup.css">
<link rel="stylesheet" href="/css/common/style.css">
<link rel="stylesheet" href="/css/common/pushy.css"><!-- スライドメニュー -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.1/animate.min.css"><!-- アニメーション -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
@include('layouts.tags.piwik_code')
</head>

<body id="state_in" class="index">

<header id="header">
<div class="inner">
<h1 class="logo"><a href="{{ config('const.member_top_path') }}"><img src="/css/common/images/logo.png" alt="{{ config('const.html_title') }}"></a></h1>


<!-- /.inner --></div>
</header>


<div id="container">
<main>
	@yield('member_content')
</main>

<aside>
<div class="block">
<div class="ttl">
<p>会員情報</p>
<p class="btn_update"><a href="{{ config('const.member_setting_path') }}">変更</a></p>
<!-- /.ttl --></div>
<dl class="list_member">
<dt>[会員ID]:</dt>
<dd>{{ $login_id }}</dd>
<dt>[ポイント]:</dt>
<dd>{{ $point }}pt</dd>
</dl>
<!-- /.block --></div>

<div class="block">
<p class="ttl">会員メニュー</p>
<ul class="list_memberMenu">
<li class="btn_blk"><a href="/member/mailbox"><span class="mailbox">メールBOX</span></a></li>
<li class="btn_gry"><a href="{{ config('const.member_hit_path') }}"><span class="hit">的中実績</span></a></li>
<li class="btn_gry"><a href="{{ config('const.member_voice_path') }}"><span class="voice">会員様の声</span></a></li>
<li class="btn_gry"><a href="{{ config('const.member_qa_path') }}"><span class="qa">よくある質問</span></a></li>
</ul>
<!-- /.block --></div>

<div class="block">
<p class="ttl">商品の購入</p>
<p class="btn_red_02 size_M"><a href="{{ config('const.member_settlement_path') }}">情報を購入する</a></p>
<!-- /.block --></div>

<div class="block">
<p class="ttl">買い目情報</p>
<ul class="list_expcttn">
<li class="btn_gry"><a href="/member/expectation/toll"><p class="slctd-info">厳選情報<span class="en">SELECTED</span></p></a></li>
<li class="btn_gry"><a href="/member/expectation/free"><p class="free-info">無料情報<span class="en">FREE</span></p></a></li>
</ul>
<!-- /.block --></div>

<div class="block">
<p class="ttl">最新的中実績</p>
@if( !empty($list_hit_data) )
	<ul class="list_hit">
		@foreach($list_hit_data as $lines)
			<li>
				<dl class="box_hit">
					<dt><span class="date">{{ $lines['date'] }}</span><br>
					<a href="/member/settlement/{{ $lines['product_id'] }}">{{ $lines['name'] }} {{ $lines['msg1'] }}</a></dt>
					<dd class="item">{{ $lines['msg2'] }}</dd>
					@if( preg_match("/^\d+$/", $lines['msg3']) > 0 )
						<dd class="dvdnds">{{ number_format($lines['msg3']) }}円</dd>
					@else
						<dd class="dvdnds">{{ $lines['msg3'] }}</dd>
					@endif
					<dd class="badge">的中</dd>
				</dl>
			</li>
			<!-- 5件表示のための処理 -->
			@if( $loop->iteration == config('const.disp_top_achievements_limit') )
				@break;
			@endif
		@endforeach
	</ul>
@endif
<!-- /.block --></div>

<ul class="list_bnr">
@if( !empty($list_banner) )
	@foreach($list_banner as $lines)
	<li>{!! $lines->banner !!}</li>
	@endforeach
@endif
</ul>

</aside>
<!-- /#container --></div>

<footer>
<div class="inner">
<p class="logo">
<picture>
<source media="(max-width:1000px)" srcset="/css/common/images/logo.png">
<img src="/css/common/images/footer_logo.png" alt="{{ config('const.html_title') }}">
</picture>
</p>

<div class="utility">
<ul class="sitemap">
<li><a href="{{ config('const.member_top_path') }}">TOP</a></li>
<li><a href="{{ config('const.member_settlement_path') }}">商品購入</a></li>
<li><a href="/member/expectation/free">無料情報</a></li>
<li><a href="/member/expectation/toll">厳選情報</a></li>
<li><a href="/member/mailbox">メールボックス</a></li>
<li><a href="{{ config('const.member_hit_path') }}">的中実績</a></li>
<li><a href="{{ config('const.member_voice_path') }}">会員様の声</a></li>
<li><a href="{{ config('const.member_qa_path') }}">よくある質問</a></li>
<li><a href="{{ config('const.member_privacy_path') }}">プライバシーポリシー</a></li>
<li><a href="{{ config('const.member_rule_path') }}">利用規約</a></li>
<li><a href="{{ config('const.member_outline_path') }}">特定商取引に基づく表記</a></li>
<li><a href="{{ config('const.member_setting_path') }}">会員情報変更</a></li>
<li><a href="{{ config('const.member_info_path') }}">お問い合わせ</a></li>
</ul>

<p class="copyright"><small>Copyright(C)2018 {{ config('const.html_title') }} All Rights Reserved.</small></p>
<!-- /.utility --></div>
<!-- /.inner --></div>
</footer>


<p class="btn_pagetop"><a href="#header"><img src="/css/common/images/btn_pagetop.png" alt="Page top"></a></p>



<!-- スライドメニュー -->
<!-- ボタン -->
<div class="menu-btn"><p><img src="/css/common/images/btn_menu.png" alt="MENU"></p></div>

<!-- オーバーレイ -->
<div class="site-overlay"></div>

<!-- メニュー -->
<nav class="pushy pushy-left">

<p class="logo"><a href="{{ config('const.member_top_path') }}"><img src="/css/common/images/nav_logo.png" alt="{{ config('const.html_title') }}"></a></p>
<dl>
<dt>会員メニュー</dt>
<dd>
<ul>
<li class="index"><a href="{{ config('const.member_top_path') }}">トップページ</a></li>
<li class="sttlmnt"><a href="{{ config('const.member_settlement_path') }}">商品購入</a></li>
<li class="expcttn1"><a href="/member/expectation/free">無料情報</a></li>
<li class="expcttn2"><a href="/member/expectation/toll">厳選情報</a></li>
<li class="mailbox"><a href="/member/mailbox">メールボックス</a></li>
<li class="hit"><a href="{{ config('const.member_hit_path') }}">的中実績</a></li>
<li class="voice"><a href="{{ config('const.member_voice_path') }}">会員様の声</a></li>
<li class="qa"><a href="{{ config('const.member_qa_path') }}">よくある質問</a></li>
<li><a href="{{ config('const.member_privacy_path') }}">プライバシーポリシー</a></li>
<li><a href="{{ config('const.member_rule_path') }}">利用規約</a></li>
<li><a href="{{ config('const.member_outline_path') }}">特定商取引に基づく表記</a></li>
<li><a href="{{ config('const.member_setting_path') }}">会員情報変更</a></li>
<li><a href="{{ config('const.member_info_path') }}">お問い合わせ</a></li>
</ul>
</dd>
</dl>
</nav>
@if( isset($login_bonus_flg) )
<div id="loginbonus" class="white-popup">
	<h1>ログインボーナス</h1>
	<p>{{ $login_bonus_pt }}ptを付与しました！！！</p>
</div>
@if( count($userinfo) > 0 )
	@foreach($userinfo as $index => $lines)
		<div id="userinfo{{ $index }}" class="white-popup">
			{!! $lines->userinfo !!}
		</div>
	@endforeach

@endif
<script async src="/js/jquery.magnific-popup.min.js"></script>
<script>
$(window).load(function(e){
	var open_flg = '{{ $login_bonus_flg }}';
	if( open_flg ){
		/*
		 * ログインボーナスポップアップ表示
		 */
		var basemp = $.magnificPopup.instance;
		basemp.open({
			items:[{
				src:'#loginbonus',
				type: 'inline',
				modal: true
			}],
			callbacks:{
				close:function(){
					@if( count($userinfo) > 0 )
						alert("1");
						$('button.mfp-close').on('click',function(event){
							event.preventDefault();
							openPopup(0,basemp,e);
						});
						alert("2");
					@else
						setTimeout(function(){
							basemp.close();
						}, 3000);
					@endif
				}
			}
		});

		$('button.mfp-close').on('click',function(event){

			event.preventDefault();
			basemp.close();
			openPopup(0,basemp,e);
		});
		/*
		 * 時間差でお知らせポップアップ表示
		 */

	}
});
function openPopup(id,basemp,e){
	if( document.getElementById('userinfo'+id) != null ){
		alert("func 1");
		var mp = $.magnificPopup.instance;

		mp.open({
			items:[{
				src:'#userinfo'+id,
				type: 'inline',
				modal: true
			}],
			callbacks:{
				close:function(){
					id++;
					if( document.getElementById('userinfo'+id) != null ){
						alert("func 2");
						$('button.mfp-close').on('click',function(event){
							event.preventDefault();
							openPopup(id,mp,e);
						});
					}else{
						setTimeout(function(){
							mp.close();
						}, 3000);
					}
				}
			}
		});
		alert("func 3");

	}else{
		setTimeout(function(){
			basemp.close();
		}, 3000);
	}
}
</script>
@endif

<script async src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inview/1.0.0/jquery.inview.min.js"></script><!-- 表示領域判定 -->
<script async src="/css/common/run.js"></script>
<script>
// 表示領域でアニメーション制御（jquery.inview.min.js）
$(function() {
	$('.animated').each(function(){
		$('.inviewfadeInUp').css('opacity',0);
		$('.inviewfadeInUp').on('inview', function(event, isInView) {
			if (isInView) {
			// In
				$(this).addClass('fadeInUp');
				$(this).css('opacity',1);
			} else {
			// Out
				$(this).removeClass('fadeInUp');
				$(this).css('opacity',0);
			}
		});
	});

});
</script>
<!-- /スライドメニュー -->
<script async src="https://cdnjs.cloudflare.com/ajax/libs/pushy/1.1.0/js/pushy.min.js"></script><!-- スライドメニュー -->
</body>
</html>
