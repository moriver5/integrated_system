@extends('layouts.member_base')
@section('member_content')
{{ app()->langPath() }}
<h1 class="ttl_01">クレジットカード</h1>
<p class="area_01 mb_L">各社クレジットカードにて商品の購入が24時間可能です。</p>

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

<!--		<form name="formSettlement" method="post" action="https://secure.telecomcredit.co.jp/inetcredit/adult/order.pl" ENCTYPE="x-www-form-encoded" TARGET="_top">-->
<section class="mb_LL">
<h3 class="ttl_03">2. 上記でよろしければ、SSL決済ボタンを押して認証を行ってください。</h3>
<p>※ SSL決済ページへ移動します</p>
<p class="btn_02"><button id="push_settlement" type="submit" alt="SSL簡単決済ページへ">SSL決済ページへ</button></p>
</section>

<div class="textcont">
<p>※ お客様の個人情報を守る為、ＳＳＬ（暗号化）通信を導入しております。</p>
<figure class="t_center"><img src="/images/logo_credit.png" alt=""></figure>
<p>各社クレジットカードにて商品の購入が24時間可能です。<br class="view_pc">
VISA,JCB,MasterCardのマークが付いているカードがご利用になれます。</p>
<p>■ 請求書に記載される請求名について<br>
カード会社より発行される明細書に、<br class="view_sp">「AXES Payment」名義で請求されます。<br>
<a href="http://www.axes-payment.co.jp/credituser.html" target="_blank">（株）AXES Payment - 安心・安全への取り組み</a></p>
<p>■ クレジットカード決済に関するご説明<br>
決済システムは<br class="view_sp">（株）AXES Paymentを利用しています。<br>
<a href="https://gw.axes-payment.com/cgi-bin/pc_exp.cgi?clientip=***" target="_blank">必ずお読みください</a></p>
<p>■ カード決済に関するお問い合わせ<br>
カスタマーサポート（24時間365日)<br>
TEL：0570-03-6000（03-3498-6200）<br>
<a href="mailto:creditinfo@axes-payment.co.jp">creditinfo@axes-payment.co.jp</a></p>
</div><!--/.textcont-->
<p class="btn_01"><a href="/member/settlement">商品購入ページに戻る</a></p>

<input type="hidden" name="clientip" value="{{ $payment_agency->clientip }}">
<input type="hidden" name="money" value="{{ $total_amount }}">
<input type="hidden" name="usrmail" value="{{ $email }}">
<input type="hidden" name="usrtel" value="{{ $telno }}">
<input type="hidden" name="sendid" value="{{ $sendid }}">
<input type="hidden" name="sendpoint" value="{{ $order_id }}">
<input type="hidden" name="redirect_back_url" value="{{ config('const.settlement_success_link_url') }}">
<input type="hidden" name="option" value="{{ $site }}">
</form>
</div><!--/.area_01-->

<script src="/js/ajax.js"></script>
<script>
$(document).ready(function () {
	ajax('formSettlement', 'push_settlement', 'post', '{{ csrf_token() }}', '{{ $settlement_url }}', '{{__('messages.ajax_connect_error_msg')}}');
});
</script>
@endsection
