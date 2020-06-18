<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="csrf-token" content="{{ !empty(csrf_token()) ? csrf_token():'' }}">
<title>{{ !empty($title) ? $title:"" }}</title>
<link rel="stylesheet" href="/css/common/magnific-popup.css">
<link rel="stylesheet" href="/css/common.css">
<link rel="stylesheet" href="/css/pushy.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inview/1.0.0/jquery.inview.min.js"></script>
<script src="/js/script.js"></script>

<script>
$(function () {
  $('.slick_slider1').slick({
    autoplay: true,
    arrows  : false,
    dots    : true,
    pauseOnHover:false,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [{
      breakpoint: 999,
      settings: {
        slidesToShow: 1,
        centerMode  :true,
        centerPadding:'10%'
      }
    }]
  });
  $('.slick_slider2').slick({
    autoplay: true,
    arrows  : false,
    dots    : true,
    centerMode  :true,
    centerPadding:'10%'
  });
});
</script>
</head>
<body id="state_in">
<header id="header">
<div class="inner">
<h1 class="logo"><a href="{{ config('const.member_top_path') }}"><img src="/images/h_logo.png" alt="{{ config('const.html_title') }}"></a></h1>
<div class="member">
<ul>
<li>会員ID：{{ !empty($login_id) ? $login_id:"" }}</li>
<li>所持ポイント：{{ !empty($point) ? $point:"0" }}pt</li>
</ul>
</div>
</div><!--/.inner-->
<nav class="hnav view_sp">
<ul>
<li><a href="{{ config('const.member_settlement_path') }}">商品購入</a></li>
<li><a href="/member/expectation/list">情報公開</a></li>
<li><a href="{{ config('const.member_hit_path') }}">的中実績</a></li>
<li><a href="/member/mailbox">メールBOX</a></li>
</ul>
</nav>
</header>

<div id="container">
<main>
@yield('member_content')
</main>
<aside>
<p class="btn_cart"><a href="{{ config('const.member_settlement_path') }}"><span>商品購入</span></a></p>
<nav class="snav">
<ul>
<li class="home"><a href="{{ config('const.member_top_path') }}">HOME</a></li>
<li class="guide"><a href="/member/guide">簡単ご利用方法</a></li>
<li class="expectation"><a href="/member/expectation/list">情報公開</a></li>
<li class="hit"><a href="{{ config('const.member_hit_path') }}">的中実績</a></li>
<li class="mail"><a href="/member/mailbox">メールBOX</a></li>
<li class="voice"><a href="{{ config('const.member_voice_path') }}">会員様の声</a></li>
<li class="info"><a href="{{ config('const.member_info_path') }}">お問い合わせ</a></li>
<li class="setting"><a href="{{ config('const.member_setting_path') }}">会員情報変更</a></li>
</ul>
</nav>
<ul class="list_bnr">
<li><a href="https://www.ipat.jra.go.jp/" target="_blank" rel="noopener"><img src="/images/bnr_sokupat.png" alt="即PAT"></a></li>
<li><a href="http://www.jra.go.jp/" target="_blank" rel="noopener"><img src="/images/bnr_jra.png" alt="JRA公式"></a></li>
@if( !empty($list_banner) )
	@foreach($list_banner as $lines)
		@if( !empty($lines->banner) )
		<li>{!! $lines->banner !!}</li>
		@endif
	@endforeach
@endif
</ul>
</aside>
</div><!--/#container-->


<footer id="footer">
<div class="inner">
<nav class="view_pc">
<ul class="fnav">
<li><a href="{{ config('const.member_privacy_path') }}">プライバシーポリシー</a></li>
<li><a href="{{ config('const.member_outline_path') }}">特定商取引法に基づく表記</a></li>
<li><a href="{{ config('const.member_rule_path') }}">利用規約</a></li>
<li><a href="{{ config('const.member_legal_path') }}">競馬法に関する特記事項</a></li>
</ul>
</nav>
<div class="flogo">
<p class="logo"><img src="/images/f_logo.png" alt="ゴールデンスターズ"></p>
<p class="copyright">&copy; Golden Stars All Rights Reserved. </p>
</div>
</div><!--/.inner-->
</footer>

<!-- btn_pagetop -->
<div class="btn_pagetop"><a href="#pagetop"><img src="/images/btn_pagetop.png" alt="ページトップへ"></a></div>

<!-- pushy MenuBtn -->
<div class="menu-btn view_sp"><img src="/images/btn_menu01.png" alt="メニュー"></div>
<!-- pushy Site Overlay -->
<div class="site-overlay"></div>
<!-- pushy Menu -->
<nav class="pushy pushy-right">
<div class="pushy-link">&times;</div>
<ul>
<li><a href="{{ config('const.member_top_path') }}">HOME</a></li>
<li><a href="{{ config('const.member_guide_path') }}">簡単ご利用方法</a></li>
<li><a href="/member/expectation/list">情報公開</a></li>
<li><a href="{{ config('const.member_hit_path') }}">的中実績</a></li>
<li><a href="/member/mailbox">メールBOX</a></li>
<li><a href="{{ config('const.member_voice_path') }}">会員様の声</a></li>
<li><a href="{{ config('const.member_info_path') }}">お問い合わせ</a></li>
<li><a href="{{ config('const.member_setting_path') }}">会員情報変更</a></li>
<li><a href="{{ config('const.member_privacy_path') }}">プライバシーポリシー</a></li>
<li><a href="{{ config('const.member_rule_path') }}">利用規約</a></li>
<li><a href="{{ config('const.member_outline_path') }}">特定商取引法に基づく表記</a></li>
<li><a href="{{ config('const.member_legal_path') }}">競馬法に関する特記事項</a></li>
</ul>
</nav>
@if( isset($login_bonus_flg) && $login_bonus_flg )
	@if( !empty($login_bonus_msg) )
	{!! $login_bonus_msg !!}
	@endif
@endif
@if( !empty($userinfo) && count($userinfo) > 0 && $login_bonus_flg )
@foreach($userinfo as $index => $lines)
	@php
		if( !empty($lines->userinfo) ){
			$userinfo = preg_replace("/<!--index-->/u", $index, $lines->userinfo);
			$userinfo = preg_replace("/<!--disptime-->/u", $lines->disptime, $userinfo);
			echo $userinfo;
		}
	@endphp
@endforeach
@endif
<script async src="/js/jquery.magnific-popup.min.js"></script>
<script>
$(window).load(function(){
	var open_flg = '{{ !empty($login_bonus_flg) ? $login_bonus_flg:'' }}';
	if( open_flg ){
		var timerid;

		/*
		 * ログインボーナス表示→お知らせ表示
		 */
		if( document.getElementById('loginbonus') != null ){
			/*
			 * ログインボーナスポップアップ表示のあとにお知らせポップアップ表示
			 */
			$.magnificPopup.open({
				items:[{
					src:'#loginbonus',
					type: 'inline',
					modal: true
				}],
				callbacks:{
					close:function(){
						clearTimeout(timerid);

						/*
						 * class名から表示時間取得
						 */
						if( document.getElementById('userinfo0') != null ){
							var disptime = eval($('#userinfo0').attr('class').replace(/.+disptime_(\d+)/, "$1")) * 1000;
							openPopup(0, disptime);
						}
					}
				}
			});

			/*
			 * 指定時間経過後、ポップアップを閉じる
			 */
			timerid = setTimeout(function(){
				$.magnificPopup.close();
			}, {{ !empty($login_bonus_disptime) ? $login_bonus_disptime:5000 }});

		/*
		 * 
		 */
		}else if( document.getElementById('userinfo0') != null ){
			var disptime = eval($('#userinfo0').attr('class').replace(/.+disptime_(\d+)/, "$1")) * 1000;
			openPopup(0, disptime);
		}
	}
});

/*
 * 次のお知らせポップアップの表示
 */
function openPopup(id, closetime){
	if( document.getElementById('userinfo'+id) != null ){
		var timerid;

		/*
		 * setTimeoutを使用することで前回のポップアップを終了させ、次のポップアップ表示に切り替える
		 */
		setTimeout(function(){
			$.magnificPopup.open({
				items:[{
					src:'#userinfo'+id,
					type: 'inline',
					modal: true
				}],
				callbacks:{
					close:function(){
						clearTimeout(timerid);

						/*
						 * 次のIDの表示のためIDをインクリメント
						 */
						id++;

						/*
						 * class名から表示時間取得
						 */
						if( document.getElementById('userinfo'+id) != null ){
							var disptime = eval($('#userinfo'+id).attr('class').replace(/.+disptime_(\d+)/, "$1")) * 1000;
							openPopup(id, disptime);
						}
					}
				}
			});

			/*
			 * 指定時間経過後、ポップアップを閉じる
			 */
			timerid = setTimeout(function(){
				$.magnificPopup.close();
			}, closetime);

		}, 1);
	}
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pushy/1.1.2/js/pushy.min.js"></script>
</body>
</html>