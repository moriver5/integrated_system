@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">的中実績</h1>
	<ul class="list_pager mb_S">
		@include('vendor.pagination.achievements', $pager)
	</ul>

	<div class="area_01">
	@if( !empty($list_hit_data) )
		<ul class="list_hitresult">
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
		@endforeach
		</ul>
	@endif

	<ul class="list_pager mb_S">
		@include('vendor.pagination.achievements', $pager)
	</ul>

@endsection