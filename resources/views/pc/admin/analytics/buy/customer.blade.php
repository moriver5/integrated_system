@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>商品解析-購入者</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					全件数：{{$total }} 件
					({{$currentPage}} / {{$lastPage}}㌻)
					<center>{{ $links }}</center>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>注文ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>ログインID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>金額</b>
								</td>
							</tr>
							@foreach($db_data as $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;">
										<a href="/admin/member/client/edit/{{ $lines['id'] }}/order/history/{{ $lines['order_id'] }}" target="_blank">{{ $lines['order_id'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;">
										<a href="/admin/member/client/edit/{{$currentPage}}/{{ $lines['id'] }}" target="_blank">{{ $lines['login_id'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										{{ $lines['price'] }}円
									</td>
								</tr>
							@endforeach
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;" colspan="2">
									合計
								</td>
								<td style="padding:3px;text-align:center;" id="total_amount">

								</td>
							</tr>
						</table>
						</center>
					@endif
				</div>
			</div>
        </div>

    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){

	//合計のための変数
	var total			 = 0;
	
	$.when(
		//全体の合計
		$('.total').each(function(){
			total += parseInt($(this).text());
		})
	).done(function(){
		//全体の合計
		$('#total_amount').text(total+'円');
	});
});
</script>

@endsection
