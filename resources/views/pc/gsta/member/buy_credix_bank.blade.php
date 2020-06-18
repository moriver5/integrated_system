@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">銀行振込</h1>
<p class="area_01 mb_L">先ほどご選択頂いた商品内容を、ご登録のメールアドレスに送信しました。<br>
以下内容と併せてご覧になり、お手続きを進めてください。</p>

<h2 class="ttl_02">キャンペーン情報購入</h2>
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
						{{ $lines['title'] }}&nbsp;&nbsp;@php print number_format($lines['discount']); @endphp円
					<!-- pt -->
					@else
						{{ $lines['point'] }}pt&nbsp;&nbsp;@php print number_format($lines['discount']); @endphp円							
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

<section class="mb_L">
<h3 class="ttl_03">2. 振込人欄に、お名前ではなく下記の【お振込番号】をご入力の上、<br class="view_pc">電信扱いにてお振り込みください。</h3>
<table class="tbl_02 mb_S">
<tr>
<th>お振込み番号</th>
<td>344378</td>
</tr>
</table>
<p>※ 上記のお振り込み番号を振り込み人名義にお書きください。</p>
</section>
<section class="mb_L">
<h3 class="ttl_03">3.振込口座</h3>
<table class="tbl_02 mb_S">
<tr>
<th>銀行名</th>
<td>三井住友銀行</td>
</tr>
<tr>
<th>支店名</th>
<td>世田谷支店</td>
</tr>
<tr>
<th>口座番号</th>
<td>普通 5566552</td>
</tr>
<tr>
<th>振込み先名義</th>
<td>有限会社ルーツ</td>
</tr>
</table>
<p>※ お振込み手数料は、お客様のご負担となります。ご了承下さい。午後3時以降にお手続き頂いたご入金の確認は翌銀行営業日となります事をご了承ください。</p>
</section>
</div><!--/.area_01-->
@endsection
