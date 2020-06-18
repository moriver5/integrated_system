@extends('layouts.member_base')
@section('member_content')
	<h1 class="ttl_01">厳選情報</h1>

	<div class="cont">
		<section>
		<p class="txt_info">消費ポイント&nbsp;：{{ $use_point }}pt</p><br />
		ポイントが足りません。<br />
		下記から商品またはポイントをご購入の上、再度、お試し頂きますようよろしくお願い致します。<br />
		<br />
		<a href="/member/settlement">商品・ポイントの追加はこちら</a><br />
		<br />
		<a href="/member/expectation/toll">厳選情報一覧に戻る</a>
		</section>
	</div>
@endsection