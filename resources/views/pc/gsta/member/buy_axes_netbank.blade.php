@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">ネットバンク</h1>
<p class="area_01 mb_L">休日でも24時間お申し込み手続きと同時にお振り込みが完了します。</p>

<h2 class="ttl_02">キャンペーン情報購入</h2>
<form id="formSettlement" class="form-horizontal" method="POST" action="/member/settlement/buy/send">
{{ csrf_field() }}
<div class="area_01">
<section class="mb_L">
<h3 class="ttl_03">1. 決済金額</h3>
<table class="tbl_02">
	@if( isset($db_data) )
		<tr>
		<th rowspan="{{ $total }}">ご購入内容</th>
		@foreach($db_data as $index => $lines)
			<td>
				<!-- 商品 -->
				@if( !empty($lines['title']) )
					{{ $lines['title'] }}&nbsp;&nbsp;@php print number_format($list_money[$lines['id']]); @endphp円
				<!-- pt -->
				@else
					{{ $lines['point'] }}pt&nbsp;&nbsp;@php print number_format($lines['money']); @endphp円							
				@endif
			</td>
			</tr>
		@endforeach
		<tr>
			<th>ご購入金額合計</th>
			<td>@php print number_format($total_amount); @endphp円</td>
		</tr>
	@endif
</table>
</section>

<section>
<h3 class="ttl_03">2. 上記でよろしければ、SSL決済ボタンを押して認証を行ってください。</h3>
<p>※ SSL決済ページへ移動します</p>
<p class="btn_02"><button id="push_settlement" type="submit" alt="SSL簡単決済ページへ">SSL決済ページへ</button></p>
</section>
<div class="textcont">
<p>ジャパンネット銀行口座、住信SBIネット銀行口座からお振り込みのお客様は、休日でも24時間お申し込み手続きと同時にお振り込みが完了します。その他の銀行の場合、午後3時以降にお手続き頂いた場合のご入金確認はよく銀行営業となります事をご了承下さい。</p>
<p>■決済に関するご注意<br>
銀行振り込みでのお支払いは、（株）AXES Paymentの決済システムを利用しています。個人情報の入力に不安のある方は（株）AXES Payment-安心・安全への取り組みをお読み下さい。</p>
</div>

<span id="errmsg" style="color:red;font-weight:bold;"></span>
<input type="hidden" name="clientip" value="{{ $payment_agency->netbank_clientip }}">
<input type="hidden" name="money" value="{{ $total_amount }}">
<input type="hidden" name="email" value="{{ $email }}">
<input type="hidden" name="sendid" value="{{ $sendid }}">
<input type="hidden" name="sendpoint" value="{{ $order_id }}">
<input type="hidden" name="act" value="order">
<input type="hidden" name="siteurl" value="{{ config('const.settlement_success_link_url') }}/{{$order_id}}">
<input type="hidden" name="sitestr" value="{{ mb_convert_encoding(config('const.axes_success_link_text'), 'SJIS-win', 'UTF-8') }}">
<input type="hidden" name="pay_agency" value="{{ $pay_agency }}">
</form>
</div><!--/.area_01-->

<script src="/js/ajax.js"></script>
<script>
$(document).ready(function () {
	ajax('formSettlement', 'push_settlement', 'post', '{{ csrf_token() }}', '{{ $settlement_url }}', '{{__('messages.ajax_connect_error_msg')}}');
});
</script>
@endsection
