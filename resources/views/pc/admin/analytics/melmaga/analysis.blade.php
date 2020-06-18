@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>メルマガ解析</b>
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
									<b>メルマガID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>閲覧済</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>未閲覧</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>配信数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:110px;">
									<b>配信日</b>
								</td>
							</tr>
							@foreach($db_data as $melmaga_id => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;">
										<a href="/admin/member/melmaga/mail/history/view/{{ $currentPage }}/{{ $melmaga_id }}" target="_blank">{{ $melmaga_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="no_pay">
										<a href="/admin/member/analytics/melmaga/access/visited/{{ $melmaga_id }}" target="_blank">{{ $lines['read'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/analytics/melmaga/access/unseen/{{ $melmaga_id }}" target="_blank">{{ $lines['no_read'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										{{ $lines['total'] }}
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines['send_date'] }}
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
								<td style="padding:3px;text-align:center;">

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
								<td style="padding:3px;text-align:center;">

								</td>
							</tr>
						</table>
						</center>
					@endif
				</div>
			</div>
        </div>

        <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default" style="background:white;">
				<div id="access_graph" style="height:1600px;width:100%;"></div>
			</div>
		</div>

    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("div #access_graph").css("height","{{ (count($db_data)* 25 + 110) }}px");

	//グラフ用
	var listMonth = [];
	var listPay = {name:'閲覧済',data:[]};
	var listNoPay = {name:'未閲覧',data:[]};

	@foreach($db_data as $month => $lines)
		listMonth[listMonth.length] = '{{ $month }}';
		listPay['data'][listPay['data'].length] = {{ $lines['read'] }};
		listNoPay['data'][listNoPay['data'].length] = {{ $lines['no_read'] }};
	@endforeach
/*
	Highcharts.chart('access_graph', {
		title: {
			text: 'メルマガ解析'
		},

		subtitle: {
			text: ''
		},
		xAxis: {
			title: {
				text: 'メルマガID'
			},
			categories: listMonth
		},
		yAxis: {
			title: {
				text: '件数'
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
*/
	Highcharts.chart('access_graph', {
		chart: {
			type: 'bar'
		},
		title: {
			text: 'メルマガ解析'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			title: {
				text: 'メルマガID'
			},
			categories: listMonth,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '件数'
			},
			stackLabels: {
				enabled: true,
				style: {
					fontWeight: 'bold',
					color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				}
			}
		},
		legend: {
			align: 'right',
			x: 0,
			verticalAlign: 'top',
			y: 0,
			floating: true,
			backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
			borderColor: '#CCC',
			borderWidth: 1,
			shadow: false
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">メルマガID：{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:2">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.0f}件</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			series: {
				stacking: 'normal',
				dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				}
			}
		},
		series: [listNoPay, listPay],
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
