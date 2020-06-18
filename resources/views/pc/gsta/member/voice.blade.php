@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">会員様の声</h1>
<ul class="list_pager mb_S">{{ $page_link }}</ul>

@if( !empty($db_data) )
	<div class="area_01">
	<ul>
	@foreach($db_data as $lines)
		<li><dl class="box_02">
			<dt><span class="date">{{ $lines['post_date'] }}</span><span class="name">{{ $lines['name'] }}</span></dt>
			<dd>
				{!! nl2br(e(trans($lines['msg']))) !!}
				@if( !empty($lines['img']) )
				<br><img src="/images/voice/{{ $lines['img'] }}">
				@endif
			</dd>
			</dl>
		</li>
	@endforeach
	</ul>
	</div><!--/.area_01-->
@endif
<ul class="list_pager mb_S">{{ $page_link }}</ul>

@endsection