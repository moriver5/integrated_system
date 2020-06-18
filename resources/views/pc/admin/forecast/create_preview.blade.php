<!DOCTYPE HTML>
<html lang=ja>
<head>
<meta name="robots" content="noindex,nofollow">
<meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
<meta name=format-detection content="telephone=no">
<title>{{ config('const.html_title') }}</title>
<link rel=stylesheet href="/css/common/style.css">
<link rel=stylesheet href="/css/common/pushy.css">
<link rel=stylesheet href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.1/animate.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inview/1.0.0/jquery.inview.min.js"></script>
<script>//<![CDATA[
$(function(){$('a[href^=#]').click(function(){var speed=500;var href=$(this).attr("href");var target=$(href=="#"||href==""?'html':href);var position=target.offset().top;$("html, body").animate({scrollTop:position},speed,"swing");return false;});});$(function(){var bodyID=$('body').attr('id');if(bodyID=='state_in'){var sideView=$('.btn_pagetop').css('display');if(sideView=='block'){var sideHigh=$('#container aside').outerHeight();$('#container main').css('min-height',sideHigh);};};});
//]]></script>
<script>$(function(){$('.animated').each(function(){$('.inviewfadeInUp').css('opacity',0);$('.inviewfadeInUp').on('inview',function(event,isInView){if(isInView){$(this).addClass('fadeInUp');$(this).css('opacity',1);}else{$(this).removeClass('fadeInUp');$(this).css('opacity',0);}});});});</script>
</head>
<body id=state_in class=index>
<header id=header>
<div class=inner>
<h1 class=logo><a href="/member/home"><img src="/css/common/images/logo.png" alt="{{ config('const.html_title') }}"></a></h1>
</div>
</header>
<div id=container>
<main>
<h1 class=ttl_01>無料情報</h1>
<div class=cont>
<section>
<h2 class=ttl_03>タイトル</h2>
<p class=txt_info id="create_title"></p>
</section>
<section>
<h2 class=ttl_03>コメント</h2>
<p class=txt_info id="create_comment"></p>
</section>
<section>
<h2 class=ttl_03>内容</h2>
<p class=txt_info id="create_preview"></p>
</section>
</div>
</main>
<aside>
<div class=block>
<div class=ttl>
<p>会員情報</p>
<p class=btn_update><a href="/member/setting">変更</a></p>
</div>
<dl class=list_member>
<dt>[会員ID]:</dt>
<dd>〇×〇×〇×</dd>
<dt>[ポイント]:</dt>
<dd>〇×pt</dd>
</dl>
</div>
<div class=block>
<p class=ttl>会員メニュー</p>
<ul class=list_memberMenu>
<li class=btn_blk><a href="/member/mailbox"><span class=mailbox>メールBOX</span></a></li>
<li class=btn_gry><a href="/member/hit"><span class=hit>的中実績</span></a></li>
<li class=btn_gry><a href="/member/voice"><span class=voice>会員様の声</span></a></li>
<li class=btn_gry><a href="/member/qa"><span class=qa>よくある質問</span></a></li>
</ul>
</div>
<div class=block>
<p class=ttl>商品の購入</p>
<p class="btn_red_02 size_M"><a href="/member/settlement">情報を購入する</a></p>
</div>
<div class=block>
<p class=ttl>買い目情報</p>
<ul class=list_expcttn>
<li class=btn_gry><a href="/member/expectation/toll"><p class=slctd-info>厳選情報<span class=en>SELECTED</span></p></a></li>
<li class=btn_gry><a href="/member/expectation/free"><p class=free-info>無料情報<span class=en>FREE</span></p></a></li>
</ul>
</div>
<div class=block>
<p class=ttl>最新的中実績</p>

</div>
<ul class=list_bnr>
<li><a href="https://www.ipat.jra.go.jp/" target=_blank><img src="/css/common/images/bnr_ipat.png" alt="即PAT"></a></li>
<li><a href="http://www.japannetbank.co.jp/" target=_blank><img src="/css/common/images/bnr_japannetbank.png" alt="ジャパンネット銀行"></a></li>
<li><a href="http://www.jma.go.jp/jp/yoho/" target=_blank><img src="/css/common/images/bnr_jma.png" alt="気象庁"></a></li>
</ul>
</aside>
</div>
<footer>
<div class=inner>
<p class=logo>
<picture>
<source media="(max-width:1000px)" srcset="/css/common/images/logo.png">
<img src="/css/common/images/footer_logo.png" alt="{{ config('const.html_title') }}">
</picture>
</p>
<div class=utility>
<ul class=sitemap>
<li><a href="/member/home">TOP</a></li>
<li><a href="/member/settlement">商品購入</a></li>
<li><a href="/member/expectation/free">無料情報</a></li>
<li><a href="/member/expectation/toll">厳選情報</a></li>
<li><a href="/member/mailbox">メールボックス</a></li>
<li><a href="/member/hit">的中実績</a></li>
<li><a href="/member/voice">会員様の声</a></li>
<li><a href="/member/qa">よくある質問</a></li>
<li><a href="/member/privacy">プライバシーポリシー</a></li>
<li><a href="/member/rule">利用規約</a></li>
<li><a href="/member/outline">特定商取引に基づく表記</a></li>
<li><a href="/member/setting">会員情報変更</a></li>
</ul>
<p class=copyright><small>Copyright(C)2018 {{ config('const.html_title') }} All Rights Reserved.</small></p>
</div>
</div>
</footer>
<p class=btn_pagetop><a href="#header"><img src="/css/common/images/btn_pagetop.png" alt="Page top"></a></p>
<div class=menu-btn><p><img src="/css/common/images/btn_menu.png" alt=MENU></p></div>
<div class=site-overlay></div>
<nav class="pushy pushy-left">
<p class=logo><a href="/member/home"><img src="/css/common/images/nav_logo.png" alt="{{ config('const.html_title') }}"></a></p>
<dl>
<dt>会員メニュー</dt>
<dd>
<ul>
<li class=index><a href="/member/home">トップページ</a></li>
<li class=sttlmnt><a href="/member/settlement">商品購入</a></li>
<li class=expcttn1><a href="/member/expectation/free">無料情報</a></li>
<li class=expcttn2><a href="/member/expectation/toll">厳選情報</a></li>
<li class=mailbox><a href="/member/mailbox">メールボックス</a></li>
<li class=hit><a href="/member/hit">的中実績</a></li>
<li class=voice><a href="/member/voice">会員様の声</a></li>
<li class=qa><a href="/member/qa">よくある質問</a></li>
<li><a href="/member/privacy">プライバシーポリシー</a></li>
<li><a href="/member/rule">利用規約</a></li>
<li><a href="/member/outline">特定商取引に基づく表記</a></li>
<li><a href="/member/setting">会員情報変更</a></li>
</ul>
</dd>
</dl>
</nav>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pushy/1.1.0/js/pushy.min.js"></script>
</body>
</html>
