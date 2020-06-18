@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>注文集計</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/statistics/access/order/status/{{ $year }}/{{$prev_month}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/statistics/access/{{ $year }}">{{ $year }}</a>年{{ $month }}月&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/statistics/access/order/status/{{ $year }}/{{$next_month}}">NEXT</a></b>
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
									<b>決済日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>MAIL</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>決済</b>
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
									<td style="padding:3px;text-align:center;" class="order_no">
										<a href="#" onclick="openOrderWin('{{ $lines->client_id }}', '{{ $lines->order_id }}');">{{ $lines->order_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="settlement_date">
										{{ $lines->regist_date }}
									</td>
									<td style="padding:3px;text-align:center;">
										<a href="/admin/member/client/edit/{{ $db_data->currentPage() }}/{{ $lines->client_id }}">{{ $lines->mail_address }}</a>
									</td>
									<td style="padding:3px;text-align:center;">
										{{ config('const.list_history_pay_status')[$lines->status] }}
									</td>
									<td style="padding:3px;text-align:center;">
										{{ config("const.list_pay_type")[$lines->pay_type] }}
									</td>
									<td style="padding:3px;text-align:right;" class="pay_money">
										&yen;{{ number_format($lines->money) }}
									</td>
								</tr>
							@endforeach
							<tr style="background-color:gray;">
								<td style="padding:1px;" colspan="6"></td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;" colspan="4">
									合計
								</td>
								<td style="padding:3px;text-align:center;" id="total_pay">

								</td>
								<td style="padding:3px;text-align:right;" id="total_amount">

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
		$('.regist_date').each(function(){
			$(this).text($(this).text().replace(/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/,"$1-$2-$3 $4:$5:$6"));
		}),
		//
		$('.pay_money').each(function(){
			pay_total++;
			amount_total += parseInt($(this).text().replace(/¥/,'').replace(/,/g,''));
		})
	).done(function(){
		$('#total_pay').text(pay_total+'件');
		$('#total_amount').text('¥'+amount_total.toLocaleString());
	});
});

function openOrderWin(client_id, order_id){
	var order_detail_win = window.open('{{ config('const.base_url') }}/admin/member/client/edit/' + client_id + '/order/history/' + order_id, 'order_detail', 'width=600, height=620');
	return false;
}
</script>

@endsection
