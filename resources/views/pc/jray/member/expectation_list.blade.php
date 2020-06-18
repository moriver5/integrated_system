@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">現在参加中のキャンペーン情報</h1>
<div class="area_01 mb_L">
@if( count($list_buy_history_data) > 0 )
<ul class="list_campaign_simple">
@foreach($list_buy_history_data as $lines)
<li><a href="{{ config('const.member_expectation_toll_path') }}/view/2/{{ $lines['forecast_id']}}"><p class="cpname">{{ $lines['title'] }}</p><p class="btn">購入した情報を見る</p></a></li>
@endforeach
</ul><!--/.list_campaign_simple-->
@endif
</div><!--/.area_01-->

<section>
<h2 class="ttl_01 bl">現在販売中のキャンペーン情報</h2>
<ul class="list_campaign">
@if( count($list_product_data) > 0 )
	@foreach($list_product_data as $lines)
		<li>
		<div class="cpbnr"><img src="/images/tipster/{{ $lines['tipster_id'] }}.png" alt="キャンペーン"></div>
		<div class="text">
		<p>先週500万円的中<br>オススメ予想師</p>
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
		<li>
			<a href="{{ config('const.member_expectation_free_path') }}/view/{{ $lines->category }}/{{ $lines->id }}">
				<div class="cpbnr">
					@if( $lines->category == 1 )
					<img src="/sample/freecpbnr_01.png" alt="無料・ポイント情報">
					@else
					<img src="/sample/freecpbnr_03.png" alt="無料・ポイント情報">
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
@endsection