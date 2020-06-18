@extends('layouts.member_base')
@section('member_content')
<section>
<h1 class="ttl_01">メールボックス</h1>

<div class="cont message">
<p>お客様に送信されたメールは、こちらのページでご確認できます。</p>
</div>

<div class="cont">
<section>
<h2 class="ttl_03 size_M">受信メール一覧</h2>

	@if( count($db_data) > 0 )
		<table class="table_gry mail_01">
		<colgroup style="width:15%;"></colgroup>
		<colgroup style="width:18%;"></colgroup>
		<colgroup style="width:67%;"></colgroup>
		<tbody>
		@foreach($db_data as $lines)
			<tr>
				<td>
					@if( empty($lines->read_flg) )
						未読
					@else
						既読
					@endif
				</td>
				<td>{!! $lines->send_date !!}</td>
				<td class="subject"><a href="/member/mailbox/history/{{ $lines->id }}">{{ $lines->subject }}</a></td>
			</tr>
		@endforeach
		</tbody>
		</table>
	@endif
</section>
</div>


<div class="cont">
<section>
<h2 class="ttl_03 size_M">お問い合わせメール一覧</h2>

	@if( count($db_info) > 0 )
		<table class="table_gry mail_01">
		<colgroup style="width:15%;"></colgroup>
		<colgroup style="width:18%;"></colgroup>
		<colgroup style="width:67%;"></colgroup>
		<tbody>
		@foreach($db_info as $lines)
			<tr>
				<td>
					@if( empty($lines->read_flg) )
						未読
					@else
						既読
					@endif
				</td>
				<td>
					<!-- 運営側からの送信 -->
					@if( empty($lines->created_at) )
					{!! $lines->reply_date !!}

					<!-- ユーザー側からの送信 -->
					@else
					{!! $lines->created_at !!}
					@endif
				</td>
				<td class="subject"><a href="/member/mailbox/info/history/{{ $lines->id }}">{{ $lines->subject }}</a></td>
			</tr>
		@endforeach
		</tbody>
		</table>
	@endif
</section>
</div>

</section>
@endsection