@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>アクセス解析</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/access/{{$prev_year}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{ $year }}年&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/access/{{$next_year}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
									<b>{{ $year }}年</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>入無し</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>入有り</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>全体</b>
								</td>
							</tr>
							@foreach($db_data as $month => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;">
										<a href="/admin/member/analytics/access/{{ $year }}/{{ $month }}">{{ $month }}月</a>
									</td>
									<td style="padding:3px;text-align:center;" class="no_pay">
										{{ $lines['no_pay'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay">
										{{ $lines['pay'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										{{ $lines['total'] }}
									</td>
								</tr>
							@endforeach
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									合計
								</td>
								<td style="padding:3px;text-align:center;" id="total_no_pay">

								</td>
								<td style="padding:3px;text-align:center;" id="total_pay">

								</td>
								<td style="padding:3px;text-align:center;" id="total_amount">

								</td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffffc6;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									平均
								</td>
								<td style="padding:3px;text-align:center;" id="no_pay_average">

								</td>
								<td style="padding:3px;text-align:center;" id="pay_average">

								</td>
								<td style="padding:3px;text-align:center;" id="total_average">

								</td>
							</tr>
						</table>
						</center>
					@endif
				</div>
			</div>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default" style="background:white;">
				<div id="access_graph" style="height:400px;width:100%; "></div>
			</div>
		</div>

    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//グラフ用
	var listMonth = [];
	var listPay = {name:'入金あり',data:[]};
	var listNoPay = {name:'入金なし',data:[]};

	@foreach($db_data as $month => $lines)
		listMonth[listMonth.length] = '{{ $month }}月';
		listPay['data'][listPay['data'].length] = {{ $lines['pay'] }};
		listNoPay['data'][listNoPay['data'].length] = {{ $lines['no_pay'] }};
	@endforeach

	Highcharts.chart('access_graph', {
		title: {
			text: 'アクセス解析'
		},

		subtitle: {
			text: ''
		},
		xAxis: {
			categories: listMonth
		},
		yAxis: {
			title: {
				text: 'アクセス数'
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle'
		},
		plotOptions: {
			line: {
				dataLabels: {
					enabled: true
				},
				enableMouseTracking: true
			}
		},

		series: [listNoPay, listPay],

		responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'bottom'
					}
				}
			}]
		}
	});

	//合計/平均算出のための変数
	var no_pay			 = 0;
	var pay				 = 0;
	var total			 = 0;
	var no_pay_average	 = 0;
	var pay_average		 = 0;
	var total_average	 = 0;
	
	$.when(
		//入金なし合計
		$('.no_pay').each(function(){
			no_pay += parseInt($(this).text());
		}),
		
		//入金あり合計
		$('.pay').each(function(){
			pay += parseInt($(this).text());
		}),
		
		//全体の合計
		$('.total').each(function(){
			total += parseInt($(this).text());
		})
	).done(function(){
		//入金なし合計
		$('#total_no_pay').text(no_pay);
		
		//入金あり合計
		$('#total_pay').text(pay);
		
		//全体の合計
		$('#total_amount').text(total);
		
		//入金なし平均
		$('#no_pay_average').text(getFloor(no_pay/12, 1));
		
		//入金あり平均
		$('#pay_average').text(getFloor(pay/12, 1));
		
		//全体の平均
		$('#total_average').text(getFloor(total/12, 1));
	});
});
</script>

@endsection
