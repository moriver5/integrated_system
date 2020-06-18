@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-11 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>入金者リスト</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/payment/structure/{{ $year }}/{{$prev_month}}/{{$prev_day}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/payment/structure/{{ $year }}">{{ $year }}</a>年{{ $month }}月{{ $day }}日&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/payment/structure/{{ $year }}/{{$next_month}}/{{$next_day}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					<center>{{ $db_data->links() }}</center>
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>注文NO</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>購入予想</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>申込日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>入金日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>顧客ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>登録日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>経過日数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>購入回数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>MAIL</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>広告コード</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>支払方法</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>金額</b>
								</td>
							</tr>
							@foreach($db_data as $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="#" onclick="openOrderWin('{{ $lines->client_id }}', '{{ $lines->order_id }}');">{{ $lines->payment_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="jan_count">
										{{ $lines->product_id }}
									</td>
									<td style="padding:3px;text-align:center;" class="jan_count">
										{{ $lines->created_at }}
									</td>
									<td style="padding:3px;text-align:center;" class="feb_count">
										{{ $lines->regist_date }}
									</td>
									<td style="padding:3px;text-align:center;" class="mar_count">
										<a href="/admin/member/client/edit/{{ $db_data->currentPage() }}/{{ $lines->client_id }}">{{ $lines->client_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="regist_date">
										{{ $lines->user_regist_date }}
									</td>
									<td style="padding:3px;text-align:center;" class="period_day">
										{{ $lines->user_regist_date }}
									</td>
									<td style="padding:3px;text-align:center;" class="apr_count">
										{{ $lines->pay_count }}
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines->email }}
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines->ad_cd }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_count">
										{{ config("const.list_pay_type")[$lines->pay_type] }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_money">
										&yen;{{ number_format($lines->money) }}
									</td>
								</tr>
							@endforeach
							<tr style="background-color:gray;">
								<td style="padding:1px;" colspan="13"></td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;" colspan="10">
									合計
								</td>
								<td style="padding:3px;text-align:center;">
									{{ $total_pay }}
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
	//合計/平均算出のための変数
	var pay_total	 = 0;
	var amount_total = 0;
	
	$.when(
		//
		$('.period_day').each(function(){
			var date = $(this).text().replace(/(\d{4})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?/,"$1-$2-$3");
			var n = new Date(date);
			var m = new Date();
			$(this).text(Math.floor((m - n)/(1000 * 60 * 60 * 24)));
		}),
		$('.regist_date').each(function(){
			var date = $(this).text().replace(/(\d{4})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?/,"$1/$2/$3");
			$(this).text(date);
		}),
		//
		$('.pay_money').each(function(){
			var total_money = $(this).text().replace(/¥/,'');
			total_money = total_money.replace(/,/g,'');
			pay_total += parseInt(total_money);
		}),
	).done(function(){
		$('#total_pay').text(pay_total);
		$('#total_amount').text('¥' + pay_total.toLocaleString());
	});
});

function openOrderWin(client_id, order_id){
	var order_detail_win = window.open('{{ config('const.base_url') }}/admin/member/client/edit/' + client_id + '/order/history/' + order_id, 'order_detail', 'width=600, height=620');
	return false;
}
</script>

@endsection
