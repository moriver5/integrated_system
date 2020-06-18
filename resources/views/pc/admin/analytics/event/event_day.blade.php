@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-11 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>イベント効果 詳細</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/event/{{ $year }}/{{$prev_month}}/{{$prev_day}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/event/{{ $year }}/{{ $month }}">{{ $year }}年{{ $month }}月</a>{{ $day }}日&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/event/{{ $year }}/{{$next_month}}/{{$next_day}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
									<b>予想ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>カテゴリ</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>タイトル</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>表示範囲</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>{{ $day }}日のアクセス</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>公開開始</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>公開終了</b>
								</td>
							</tr>
							@foreach($db_data as $forecast_id => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/forecast/edit/{{ $currentPage }}/{{ $forecast_id }}" target="_blank">{{ $forecast_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="credit_count">
										{{ $lines['category'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="credit_money">
										{{ $lines['title'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="netbank_count">
										{{ $lines['groups'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="bank_money">
										<a href="/admin/member/analytics/event/{{ $year }}/{{ $month }}/{{ $day }}/{{ $forecast_id }}" target="_blank">{{ $lines['access'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="hand_count">
										{{ $lines['sdate'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="hand_money">
										{{ $lines['edate'] }}
									</td>
								</tr>
							@endforeach
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
/*
	//合計/平均算出のための変数
	var jan_count	 = 0;
	var feb_count	 = 0;
	var mar_count	 = 0;
	var apr_count	 = 0;
	var may_count	 = 0;
	var jun_count	 = 0;
	var jul_count	 = 0;
	var aug_count	 = 0;
	var sep_count	 = 0;
	var oct_count	 = 0;
	var nov_count	 = 0;
	var dec_count	 = 0;
	
	$.when(
		//
		$('.jan_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			jan_count += parseInt(count);
		}),
		//
		$('.feb_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			feb_count += parseInt(count);
		}),
		//
		$('.mar_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			mar_count += parseInt(count);
		}),
		//
		$('.apr_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			apr_count += parseInt(count);
		}),
		//
		$('.may_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			may_count += parseInt(count);
		}),
		//
		$('.jun_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			jun_count += parseInt(count);
		}),
		//
		$('.jul_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			jul_count += parseInt(count);
		}),
		//
		$('.aug_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			aug_count += parseInt(count);
		}),
		//
		$('.sep_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			sep_count += parseInt(count);
		}),
		//
		$('.oct_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			oct_count += parseInt(count);
		}),
		//
		$('.nov_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			nov_count += parseInt(count);
		}),
		//
		$('.dec_count').each(function(){
			var count = $(this).text().replace(/,/,"");
			dec_count += parseInt(count);
		})
	).done(function(){
		$('#total_pay').text(pay_total);
		$('#total_amount').text(amount_total);
	});
*/
});

function openOrderWin(client_id, order_id){
	var order_detail_win = window.open('{{ config('const.base_url') }}/admin/member/client/edit/' + client_id + '/order/history/' + order_id, 'order_detail', 'width=600, height=620');
	return false;
}
</script>

@endsection
