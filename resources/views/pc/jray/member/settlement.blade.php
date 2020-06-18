@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">商品購入/ポイント追加</h1>
<p class="area_01 mb_L">決済方法は「銀行振込」「クレジット決済」「ネットバンク」からお選びいただけます。まずはご希望の商品をお選びください。</p>
<form action="/member/settlement/buy" method="post">
{{csrf_field()}}
<section>
<h2 class="ttl_02">キャンペーン情報購入</h2>
@if( !empty($errors->all()) )
	<ul>
	@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
	@endforeach
	</ul>
@endif
<section class="area_01">
<h3 class="ttl_03">STEP.01 購入する商品を選択してください。</h3>
@if( !empty($db_data) )
	<ul class="list_check">
	@foreach($db_data as $lines)
		<!-- ユーザーと同じグループの商品を表示 -->
		@if( empty($lines['groups']) || in_array($group_id, explode(",", $lines['groups'])) )
			<li>
			<input type="checkbox" id="id{{ $lines['id'] }}" name="product_id[]" value="{{ $lines['id'] }}" class="btn">
			<label for="id{{ $lines['id'] }}">
			<span class="text">{{ $lines['tipster'] }}</span>
			<span class="price">@php print number_format($lines['discount']); @endphp円</span>
			</label>
			<input type="hidden" name="money[]" value="{{ $lines['id'] }}_{{ $lines['discount'] }}">
			</li>
		@endif
	@endforeach
	</ul><!--/.list_check-->
@endif

</section><!--/.area_01-->
<section class="area_01 mb_L">
<h3 class="ttl_03">STEP.02 お支払い方法を下記よりお選びください。</h3>
<ul class="list_btn clm3">
<li class="btn_02"><button type="submit" name="buy_method" value="1">銀行振込</button></li>
<li class="btn_02"><button type="submit" name="buy_method" value="2">クレジットカード</button></li>
<li class="btn_02"><button type="submit" name="buy_method" value="3">ネットバンク</button></li>
</ul>
</section><!--/.area_01-->
</section>
</form>
<section>
<h2 class="ttl_02">ポイント購入</h2>
<form action="/member/settlement/buy/point" method="post">
{{csrf_field()}}
<section class="area_01">
<h3 class="ttl_03">STEP.01 購入するポイントを選択してください。</h3>
@if( !empty($db_pt_data) )
<ul class="list_radio">
	@foreach($db_pt_data as $lines)
	<li>
	<input type="radio" id="p{{ $lines->id }}" name="product_id" value="{{ $lines->id }}" class="btn">
	<label for="p{{ $lines->id }}">
	<span class="text">{{ $lines->point }}pt</span>
	<span class="price">{{ number_format($lines->money) }}円</span>
	</label>
	</li>
	@endforeach
</ul>
@endif
</section><!--/.area_01-->
<section class="area_01 mb_L">
<h3 class="ttl_03">STEP.02 お支払い方法を下記よりお選びください。</h3>
<ul class="list_btn clm3">
<li class="btn_02"><button type="submit" name="buy_method" value="1">銀行振込</button></li>
<li class="btn_02"><button type="submit" name="buy_method" value="2">クレジットカード</button></li>
<li class="btn_02"><button type="submit" name="buy_method" value="3">ネットバンク</button></li>
</ul>
</section><!--/.area_01-->
</form>
<section class="area_01">
<h3 class="ttl_03">【返品/返金について】</h3>
<p>弊社が会員様へご提供させて頂きます商品は競馬における情報（買い目）であり、いかなる場合でも決済が完了した時点で返品・返金には応じませんので、ご了承頂いた会員様のみ下記から購入される予約商品をご選択の上、「お申込」ボタンを押して下さい。</p>
</section>
</section>
@endsection