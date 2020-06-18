@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>売上集計</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/statistics/access/{{$prev_year}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{ $year }}年&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/statistics/access/{{$next_year}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
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
							@foreach($db_data as $month => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/analytics/sales/summary/{{ $year }}/{{ $month }}">{{ $month }}月</a>
									</td>
									<td style="padding:3px;text-align:center;" class="credit_count">
										@if( $lines['credit_count'] > 0 )
										{{ number_format($lines['credit_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="credit_money">
										@if( $lines['credit_amount'] > 0 )
										{{ number_format($lines['credit_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="netbank_count">
										@if( $lines['netbank_count'] > 0 )
										{{ number_format($lines['netbank_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="netbank_money">
										@if( $lines['netbank_amount'] > 0 )
										{{ number_format($lines['netbank_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="bank_count">
										@if( $lines['bank_amount'] > 0 )
										{{ number_format($lines['bank_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="bank_money">
										@if( $lines['bank_amount'] > 0 )
										{{ number_format($lines['bank_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="hand_count">
										@if( $lines['total_count'] > 0 )
										{{ number_format($lines['hand_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="hand_money">
										@if( $lines['total_amount'] > 0 )
										{{ number_format($lines['hand_amount']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="amount_count">
										@if( $lines['total_count'] > 0 )
										{{ number_format($lines['total_count']) }}
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="amount_money">
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
								<td style="padding:3px;text-align:center;" id="credit_count">

								</td>
								<td style="padding:3px;text-align:center;" id="credit_money">

								</td>
								<td style="padding:3px;text-align:center;" id="netbank_count">

								</td>
								<td style="padding:3px;text-align:center;" id="netbank_money">

								</td>
								<td style="padding:3px;text-align:center;" id="bank_count">

								</td>
								<td style="padding:3px;text-align:center;" id="bank_money">

								</td>
								<td style="padding:3px;text-align:center;" id="hand_count">

								</td>
								<td style="padding:3px;text-align:center;" id="hand_money">

								</td>
								<td style="padding:3px;text-align:center;" id="amount_count">

								</td>
								<td style="padding:3px;text-align:center;" id="amount_money">

								</td>
							</tr>
						</table>
						</center>
					@endif
				</div>
			</div>
        </div>

        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="background:white;">
				<div id="month_pay_graph" style="height:400px;width:100%; "></div>
			</div>
		</div>

        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="background:white;">
				<div id="pay_graph" style="height:400px;width:100%; "></div>
			</div>
		</div>

    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//
	var listCredit = {name:'クレジット',data:[]};
	var listNetbank = {name:'ネットバンク',data:[]};
	var listHand = {name:'管理手動',data:[]};

	@foreach($db_data as $month => $lines)
		listCredit['data'][{{$loop->iteration - 1}}] = {{$lines['credit_amount']}};
		listNetbank['data'][{{$loop->iteration - 1}}] = {{$lines['netbank_amount']}};
		listHand['data'][{{$loop->iteration - 1}}] = {{$lines['hand_amount']}};
	@endforeach

	Highcharts.chart('month_pay_graph', {
		chart: {
			type: 'column'
		},
		title: {
			text: '月ごとの支払い方法別比'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			categories: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '売上金額'
			}
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.0f}円</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
			}
		},
		series: [listCredit, listNetbank, listHand]
	});

	//合計/平均算出のための変数
	var credit_count	 = 0;
	var credit_money	 = 0;
	var netbank_count	 = 0;
	var netbank_money	 = 0;
	var bank_count		 = 0;
	var bank_money		 = 0;
	var hand_count		 = 0;
	var hand_money		 = 0;
	var amount_count	 = 0;
	var amount_money	 = 0;
	
	$.when(
		//
		$('.credit_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			credit_count += parseInt(count);
		}),
		//
		$('.credit_money').each(function(){
			var count = $(this).text().replace(/,/g,"");
			credit_money += parseInt(count);
		}),
		//
		$('.netbank_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			netbank_count += parseInt(count);
		}),
		//
		$('.netbank_money').each(function(){
			var count = $(this).text().replace(/,/g,"");
			netbank_money += parseInt(count);
		}),
		//
		$('.bankcount').each(function(){
			var count = $(this).text().replace(/,/g,"");
			bank_count += parseInt(count);
		}),
		//
		$('.bank_money').each(function(){
			var count = $(this).text().replace(/,/g,"");
			bank_money += parseInt(count);
		}),
		//
		$('.hand_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			hand_count += parseInt(count);
		}),
		//
		$('.hand_money').each(function(){
			var count = $(this).text().replace(/,/g,"");
			hand_money += parseInt(count);
		}),
		//
		$('.amount_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count += parseInt(count);
		}),
		//
		$('.amount_money').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_money += parseInt(count);
		})

	).done(function(){
		//合計
		$('#credit_count').text(credit_count.toLocaleString());
		//合計
		$('#credit_money').text(credit_money.toLocaleString());
		//合計
		$('#netbank_count').text(netbank_count.toLocaleString());
		//合計
		$('#netbank_money').text(netbank_money.toLocaleString());
		//合計
		$('#bank_count').text(bank_count.toLocaleString());
		//合計
		$('#bank_money').text(bank_money.toLocaleString());
		//合計
		$('#hand_count').text(hand_count.toLocaleString());
		//合計
		$('#hand_money').text(hand_money.toLocaleString());
		//合計
		$('#amount_count').text(amount_count.toLocaleString());
		//合計
		$('#amount_money').text(amount_money.toLocaleString());

		Highcharts.chart('pay_graph', {
			chart: {
				type: 'pie',
				options3d: {
					enabled: true,
					alpha: 45,
					beta: 0
				}
			},
			title: {
				text: '支払い方法別比'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					depth: 35,
					dataLabels: {
						enabled: true,
						format: '{point.name}'
					}
				}
			},
			series: [{
				type: 'pie',
				name: 'share',
				data: [
					['クレジット ' + Math.round((credit_money / amount_money)*100,2) + '%', credit_money / amount_money],
					['ネットバンク ' + Math.round((netbank_money / amount_money)*100,2) + '%', netbank_money / amount_money],
					['管理手動 ' + Math.round((hand_money / amount_money)*100,2) + '%', hand_money / amount_money],
				]
			}]
		});
	});
});
</script>

@endsection
