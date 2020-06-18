<footer>
<div class="inner">
<p class="logo"><img src="/css/common/images/logo.png" alt="{{ config('const.html_title') }}"></p>

<div class="utility">
<ul class="sitemap">
<li><a href="/index">TOP</a></li>
<li><a href="/info">お問い合わせ</a></li>
<li><a href="/privacy">プライバシーポリシー</a></li>
<li><a href="/rule">利用規約</a></li>
<li><a href="/outline">特定商取引法に基づく表記</a></li>
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

<p class="logo"><a href="index.html"><img src="/css/common/images/nav_logo.png" alt="{{ config('const.html_title') }}"></a></p>
<dl>
<dt>メニュー</dt>
<dd>
<ul>
<li class="index"><a href="index.html">トップページ</a></li>
<li><a href="/info">お問い合わせ</a></li>
<li><a href="/privacy">プライバシーポリシー</a></li>
<li><a href="/rule">利用規約</a></li>
<li><a href="/outline">特定商取引法に基づく表記</a></li>
</ul>
</dd>
</dl>
</nav>
<script async src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inview/1.0.0/jquery.inview.min.js"></script><!-- 表示領域判定 -->
<script async src="/css/common/run.js"></script>
<script async>
// 表示領域でアニメーション制御（jquery.inview.min.js）
$(function() {
	$('.animated').each(function(){
		$('.inviewzoomIn').on('inview', function(event, isInView) {
			if (isInView) {
			// In
				$(this).addClass('zoomIn');
				$(this).css('opacity',1);
			} else {
			// Out
				$(this).removeClass('zoomIn');
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