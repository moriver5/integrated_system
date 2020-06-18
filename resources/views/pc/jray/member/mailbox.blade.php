@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">メールBOX</h1>
@if( $links != '' )
<ul class="list_pager mb_S">{{ $links }}</ul>
@endif
<div class="area_01">
<ul>
	@if( count($db_data) > 0 )
		@foreach($db_data as $lines)
			<li>
				<dl class="box_02 mailList">
					<dt><span class="date">{{ $lines->send_date }}</span>
						@if( empty($lines->read_flg) )
							<span class="read">未読</span>
						@endif
					</dt>
					<dd>
						<p class="text">{{ $lines->subject }}</p>
						<p class="btn"><a href="/member/mailbox/history/{{ $lines->id }}">詳細を見る</a></p>
					</dd>
				</dl>
			</li>
		@endforeach
	@endif
</ul>

<h2 class="ttl_01">お問い合わせ</h2>
<ul>
	@if( count($db_info) > 0 )
		@foreach($db_info as $lines)
			<li>
				<dl class="box_02 mailList">
					<dt><span class="date">
						<!-- 運営側からの送信 -->
						@if( empty($lines->created_at) )
						{!! $lines->reply_date !!}

						<!-- ユーザー側からの送信 -->
						@else
						{!! $lines->created_at !!}
						@endif
						</span>
						@if( empty($lines->read_flg) )
							<span class="read">未読</span>
						@endif
					</dt>
					<dd>
						<p class="text">{{ $lines->subject }}</p>
						<p class="btn"><a href="/member/mailbox/info/history/{{ $lines->id }}">詳細を見る</a></p>
					</dd>
				</dl>
			</li>
		@endforeach
	@endif
</ul>
@if( $links != '' )
<ul class="list_pager mb_S">{{ $links }}</ul>
@endif
</div><!--/.area_01-->

@endsection