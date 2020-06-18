@extends('layouts.member_base')
@section('member_content')
<section>
<h2 class="ttl_01">最新の的中実績</h2>
<ul class="list_hitresult slick_slider1">
@if( !empty($list_hit_data) )
	@foreach($list_hit_data as $lines)
		@if( !empty($lines['img']) )
		<li>
			<a href="/member/settlement">
			<p class="date">{{ $lines['date'] }}</p>
			<figure><img src="/images/top_content/{{ $lines['img'] }}" alt="無料・キャンペーン"></figure>
			<p class="pay">￥{{ number_format($lines['msg1']) }}</p>
			<p class="campaign">『{{ $lines['tipster'] }}』</p>
			<p class="race">{{ $lines['name'] }} {{ $lines['msg2'] }}</p>
			</a>
		</li>		
		@else
		<li>
			<a href="/member/settlement/{{ $lines['product_id'] }}">
			<p class="date">{{ $lines['date'] }}</p>
			<figure><img src="/images/tipster/{{ $lines['tipster_id'] }}_hit.png" alt="予想師"></figure>
			<p class="pay">￥{{ number_format($lines['msg1']) }}</p>
			<p class="campaign">『{{ $lines['tipster'] }}』</p>
			<p class="race">{{ $lines['name'] }} {{ $lines['msg2'] }}</p>
			</a>
		</li>
		@endif
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
		<li class="js_inview animated_fadeInRightBound" data-delay="{{ $lines['delay'] }}">
		<div class="cpbnr"><img src="/images/tipster/{{ $lines['tipster_id'] }}_sale.png" alt="キャンペーン"></div>
		<div class="text">
		{!! $lines['comment'] !!}
		@if( $lines['money'] != $lines['discount'] )
		<p class="price">通常価格<span class="normal">{{ $lines['money'] }}円</span>↓<br>限定価格<span class="limit">{{ $lines['discount'] }}円</span></p>
		@else
		&nbsp;<br><p class="price">&nbsp;<span class="normal">{{ $lines['money'] }}円</span>&nbsp;<span class="limit">&nbsp;</span></p>		
		@endif
		<p class="btn dtl"><a href="/member/product/detail/{{ $lines['id'] }}">詳細を見る</a></p>
		@if( $lines['is_sold_out'] === true )
		<p class="btn buy"><a tabindex="-1">完売</a></p>
		@else
		<p class="btn buy"><a href="/member/settlement/{{ $lines['id'] }}">情報を購入する</a></p>
		@endif
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
					@if( !empty($lines->img) )
						<img src="/images/top_content/{{ $lines->img }}" alt="無料・ポイント情報">
					@else
						<img src="/sample/freecpbnr_01.png" alt="無料・ポイント情報">
					@endif
				</div>
				<div class="text">
					{!! $lines->headline !!}
					@if( $lines->category == 2 )
						<p class="btn">{{ $lines->point }}pt</p>
					@else
						<p class="btn">
						@if( $lines->point > 0 )
							{{ $lines->point }}pt
						@else
							無料
						@endif
						</p>
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
@if( !empty($list_top_content_data) )
	@foreach($list_top_content_data as $id => $img)
		<li><a href="/member/regular/{{ $id }}"><img src="/images/top_content/{{ $img }}" alt="お得な情報"></a></li>
	@endforeach
@endif
</ul>
</section>
@endsection

