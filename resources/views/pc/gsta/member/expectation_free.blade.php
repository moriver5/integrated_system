@extends('layouts.member_base')
@section('member_content')
	<h1 class="ttl_01">無料情報</h1>
	<div class="cont">
		@if( !empty($db_data) )
			<section>
			<h2 class="ttl_03 size_M">キャンペーン情報公開</h2>
			@foreach($db_data as $lines)
				<dl class="list_info">
				<dt>無料情報<span class="colon">：</span></dt>
					<dd>{{ $lines->title }}</dd>
					<dt>内容<span class="colon">：</span></dt>
					<dd>{{ $lines->comment }}</dd>
					<dt>消費ＰＴ<span class="colon">：</span></dt>
					<dd>{{ $lines->point }}pt</dd>
				</dl>
				<p class="btn_red_02 size_S"><a href="{{ config('const.member_expectation_free_path') }}/view/{{ $lines->category }}/{{ $lines->id }}">無料情報詳細</a></p>
			@endforeach
			</section>
		@endif
	</div>
@endsection