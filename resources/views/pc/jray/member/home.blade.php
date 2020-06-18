@extends('layouts.member_base')
@section('member_content')
<section>
<h2 class="ttl_01">最新の的中実績</h2>
<ul class="list_hitresult slick_slider1">
@if( !empty($list_hit_data) )
	@foreach($list_hit_data as $lines)
		<li>
			<a href="/member/settlement/{{ $lines['product_id'] }}">
			<p class="date">{{ $lines['date'] }}</p>
			<figure><img src="/images/tipster/{{ $lines['tipster'] }}.png" alt="予想師"></figure>
			<p class="pay">￥{{ number_format($lines['msg1']) }}</p>
			<p class="campaign">『{{  }}』</p>
			<p class="race">{{ $lines['name'] }} {{ $lines['msg2'] }}</p>
			</a>
		</li>
		<!-- 5件表示のための処理 -->
		@if( $loop->iteration == config('const.disp_top_achievements_limit') )
			@break;
		@endif
	@endforeach
@endif
</ul>
</section>

<section>
<h2 class="ttl_01 bl">最新ニュース</h2>
<div class="area_01">
<dl class="list_news">
	@if( count($list_news_data) > 0 )
		@foreach($list_news_data as $lines)
		<dt><span class="date"><?php echo preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s\d{2}:\d{2}:\d{2}/", "$1/$2/$3", $lines->send_date); ?></span>
			@if( !$lines->read_flg )
			<span class="read">未読</span>
			@endif
		</dt>
		<dd><a href="{{ $mailbox_url_url }}/{{ $lines->id }}">{{ $lines->subject }}</a></dd>
		@endforeach
	@endif
</dl>
</div>
</section>

<section>
<h2 class="ttl_01">現在販売中のキャンペーン</h2>
<ul class="list_campaign">
@if( count($list_product_data) > 0 )
	@foreach($list_product_data as $lines)
		<li class="js_inview animated_fadeInRightBound" data-delay="<?php echo ($loop->index * 500);?>">
		<div class="cpbnr"><img src="/images/tipster/{{ $lines['tipster_id'] }}.png" alt="キャンペーン"></div>
		<div class="text">
		{{ $lines['comment'] }}
		@if( $lines['money'] != $lines['discount'] )
		<p class="price">通常価格<span class="normal">{{ $lines['money'] }}円</span>↓<br>限定価格<span class="limit">{{ $lines['discount'] }}円</span></p>
		@else
		&nbsp;<br><p class="price">&nbsp;<span class="normal">{{ $lines['money'] }}円</span>&nbsp;<span class="limit">&nbsp;</span></p>		
		@endif
		<p class="btn dtl"><a href="/member/product/detail/{{ $lines['id'] }}">詳細を見る</a></p>
		<p class="btn buy"><a href="/member/settlement/{{ $lines['id'] }}">情報を購入する</a></p>
		</div>
		</li>
	@endforeach
@endif
</ul>
</section>

<section>
<h2 class="ttl_01 bl">無料情報・ポイント情報</h2>
<ul class="list_free_campaign">
	@if( !empty($list_forecast_data) )
	@foreach($list_forecast_data as $lines)
		<li class="js_inview animated_fadeInUp">
			@if( $lines->category == 1 )
			<a href="{{ config('const.member_expectation_free_path') }}/view/{{ $lines->category }}/{{ $lines->id }}">
			@else
			<a href="{{ config('const.member_expectation_toll_path') }}/view/{{ $lines->category }}/{{ $lines->id }}">
			@endif
				<div class="cpbnr">
					@if( $lines->category == 1 )
						@if( !empty($lines->img) )
							<img src="/images/top_content/{{ $lines->img }}" alt="無料・ポイント情報">
						@else
							<img src="/sample/freecpbnr_01.png" alt="無料・ポイント情報">
						@endif
					@else
						@if( !empty($lines->img) )
							<img src="/images/top_content/{{ $lines->img }}" alt="無料・ポイント情報">
						@else
							<img src="/sample/freecpbnr_01.png" alt="無料・ポイント情報">
						@endif
					@endif
				</div>
				<div class="text">
					@if( $lines->category == 1 )
						<p class="ttl">完全無料情報</p>
						<p><span class="f_L f_bold">土日2レース</span><br>全ての会員様が完全無料！まずは無料でお試しください!</p>
						<p class="btn">無料</p>
					@else
						<p class="ttl">ポイント情報</p>
						<p><span class="f_L f_bold">土日2レース</span><br>無料情報よりも的確に稼ぎたい会員様専用！</p>
						<p class="btn">{{ $lines->point }}pt</p>
					@endif
				</div>
			</a> 
		</li>
	@endforeach
	@endif
</ul><!--/.list_free_campaign-->
</section>

<section>
<h2 class="ttl_01">お得な情報</h2>
<ul class="slick_slider2">
<li><a href="./login_regular_01.html"><img src="/sample/slidebnr_security.png" alt="お得な情報1"></a></li>
<li><a href="./login_regular_02.html"><img src="/sample/slidebnr_info01.png" alt="お得な情報2"></a></li>
<li><a href="./login_regular_03.html"><img src="/sample/slidebnr_info02.png" alt="お得な情報3"></a></li>
</ul>
</section>
@endsection

