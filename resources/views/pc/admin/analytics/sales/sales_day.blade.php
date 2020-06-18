@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>購入回数集計</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/statistics/access/{{ $year }}/{{$prev_month}}/{{$prev_day}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/statistics/access/{{ $year }}">{{ $year }}</a>年{{ $month }}月{{ $day }}日&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/statistics/access/{{ $year }}/{{$next_month}}/{{$next_day}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					<center>{{ $db_data->links() }}</center>
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;" rowspan="2">
									{{ $year }}
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>クレジット</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>ネットバンク</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>銀行振込</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>管理手動</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>売上金額</b>
								</td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
									件数
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>金額</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>件数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>金額</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>件数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>金額</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>件数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>金額</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>件数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>金額</b>
								</td>
							</tr>
							@foreach($db_data as $lines)
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/analytics/sales/summary/{{ $year }}/{{ $month }}">{{ $day }}月</a>
									</td>
									<td style="padding:3px;text-align:center;" class="jan_count">
										@if( $lines['credit_count'] > 0 )
										{{ number_format($lines['credit_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="feb_count">
										@if( $lines['credit_amount'] > 0 )
										{{ number_format($lines['credit_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="mar_count">
										@if( $lines['netbank_count'] > 0 )
										{{ number_format($lines['netbank_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="apr_count">
										@if( $lines['netbank_amount'] > 0 )
										{{ number_format($lines['netbank_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="may_count">
										@if( $lines['bank_amount'] > 0 )
										{{ number_format($lines['bank_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="jun_count">
										@if( $lines['bank_amount'] > 0 )
										{{ number_format($lines['bank_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="jul_count">
										@if( $lines['total_count'] > 0 )
										{{ number_format($lines['total_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="aug_count">
										@if( $lines['total_amount'] > 0 )
										{{ number_format($lines['total_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="sep_count">
										@if( $lines['total_count'] > 0 )
										{{ number_format($lines['total_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="oct_count">
										@if( $lines['total_amount'] > 0 )
										{{ number_format($lines['total_amount']) }}
										@else
											0
										@endif
									</td>
								</tr>
							@endforeach
							<tr style="background-color:gray;">
								<td style="padding:1px;" colspan="13"></td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									合計
								</td>
								<td style="padding:3px;text-align:center;" id="total_jan">

								</td>
								<td style="padding:3px;text-align:center;" id="total_feb">

								</td>
								<td style="padding:3px;text-align:center;" id="total_mar">

								</td>
								<td style="padding:3px;text-align:center;" id="total_apr">

								</td>
								<td style="padding:3px;text-align:center;" id="total_may">

								</td>
								<td style="padding:3px;text-align:center;" id="total_jun">

								</td>
								<td style="padding:3px;text-align:center;" id="total_jul">

								</td>
								<td style="padding:3px;text-align:center;" id="total_aug">

								</td>
								<td style="padding:3px;text-align:center;" id="total_sep">

								</td>
								<td style="padding:3px;text-align:center;" id="total_oct">

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
			var count = $(this).text().replace(/,/g,"");
			jan_count += parseInt(count);
		}),
		//
		$('.feb_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			feb_count += parseInt(count);
		}),
		//
		$('.mar_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			mar_count += parseInt(count);
		}),
		//
		$('.apr_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			apr_count += parseInt(count);
		}),
		//
		$('.may_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			may_count += parseInt(count);
		}),
		//
		$('.jun_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			jun_count += parseInt(count);
		}),
		//
		$('.jul_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			jul_count += parseInt(count);
		}),
		//
		$('.aug_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			aug_count += parseInt(count);
		}),
		//
		$('.sep_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			sep_count += parseInt(count);
		}),
		//
		$('.oct_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			oct_count += parseInt(count);
		}),
		//
		$('.nov_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			nov_count += parseInt(count);
		}),
		//
		$('.dec_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			dec_count += parseInt(count);
		})
	).done(function(){
		$('#total_pay').text(pay_total);
		$('#total_amount').text(amount_total);
	});
});

function openOrderWin(client_id, order_id){
	var order_detail_win = window.open('{{ config('const.base_url') }}/admin/member/client/edit/' + client_id + '/order/history/' + order_id, 'order_detail', 'width=600, height=620');
	return false;
}
</script>

@endsection
