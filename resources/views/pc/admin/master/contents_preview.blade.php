<!DOCTYPE HTML>
<html lang=ja prefix="og: http://ogp.me/ns#">
<head>
<meta name="robots" content="noindex,nofollow">
<meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
<meta name=format-detection content="telephone=no">
<meta property=og:title content="{{ config('const.html_title') }}">
<meta property=og:type content=website>
<meta property=og:url content="https://jra-yosou.jp">
<title>{{ config('const.html_title') }}</title>
<link rel=canonical href="https://jra-yosou.jp">
<link rel=stylesheet href="/css/common/style.css">
<link rel=stylesheet href="/css/common/pushy.css">
<link rel=stylesheet href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.1/animate.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inview/1.0.0/jquery.inview.min.js"></script>
<script>//<![CDATA[
$(function(){$('a[href^=#]').click(function(){var speed=500;var href=$(this).attr("href");var target=$(href=="#"||href==""?'html':href);var position=target.offset().top;$("html, body").animate({scrollTop:position},speed,"swing");return false;});});$(function(){var bodyID=$('body').attr('id');if(bodyID=='state_in'){var sideView=$('.btn_pagetop').css('display');if(sideView=='block'){var sideHigh=$('#container aside').outerHeight();$('#container main').css('min-height',sideHigh);};};});
//]]></script>
<script>$(function(){$('.animated').each(function(){$('.inviewzoomIn').on('inview',function(event,isInView){if(isInView){$(this).addClass('zoomIn');$(this).css('opacity',1);}else{$(this).removeClass('zoomIn');$(this).css('opacity',0);}});});});</script>
</head>
<body id=state_out>
	<!-- 今週の注目レース -->
	@if( $id == 5 )
		<main>
		<div class="block_main">
			<p class="image">
			<picture>
			<source media="(max-width:1000px)" srcset="/images/index_main_Mbl.jpg">
			<img src="/images/index_main.jpg" alt="完全攻略！ズバリ的中 最強必勝レース分析">
			</picture>
			</p>
			<div id="main_column" class="text">
			  {!! $contents !!}
			</div>
		</div>
	<!-- 今週の注目レース以外 -->
	@else
		<main id="main_column">
		{!! $contents !!}
		</main>
	@endif
<footer>
<div class=inner>
<p class=logo><img src="/css/common/images/logo.png" alt="{{ config('const.html_title') }}"></p>
<div class=utility>
<ul class=sitemap>
<li><a href=index>TOP</a></li>
<li><a href=info>お問い合わせ</a></li>
<li><a href=privacy>プライバシーポリシー</a></li>
<li><a href=rule>利用規約</a></li>
<li><a href=outline>特定商取引法に基づく表記</a></li>
</ul>
<p class=copyright><small>Copyright(C)2018 {{ config('const.html_title') }} All Rights Reserved.</small></p>
</div>
</div>
</footer>
<p class=btn_pagetop><a href="#header"><img src="/css/common/images/btn_pagetop.png" alt="Page top"></a></p>
<div class=menu-btn><p><img src="/css/common/images/btn_menu.png" alt=MENU></p></div>
<div class=site-overlay></div>
<nav class="pushy pushy-left">
<p class=logo><a href=index.html><img src="/css/common/images/nav_logo.png" alt="{{ config('const.html_title') }}"></a></p>
<dl>
<dt>メニュー</dt>
<dd>
<ul>
<li class=index><a href=index.html>トップページ</a></li>
<li><a href=info>お問い合わせ</a></li>
<li><a href=privacy>プライバシーポリシー</a></li>
<li><a href=rule>利用規約</a></li>
<li><a href=outline>特定商取引法に基づく表記</a></li>
</ul>
</dd>
</dl>
</nav>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pushy/1.1.0/js/pushy.min.js"></script>
</body>
</html>
