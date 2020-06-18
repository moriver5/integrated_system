@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container" style="margin-left:200px;">
    <div class="col">
		<div class="panel panel-default" style="width:1850px;">
			<div class="panel-heading" style="text-align:center;">
				<b>売上構成</b>
			</div>
			<div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
				<b><a href="/admin/member/analytics/sales/structure/{{$prev_year}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{ $year }}年&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/sales/structure/{{$next_year}}">NEXT</a></b>
			</div>
			<div class="panel-heading">
				@if( !empty($db_later_data) )
					<center>
					<table border="1" align="center" style="width:100%;">
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
								{{ $year }}
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>当月登録</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>登録2ヶ月目</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>登録3ヶ月目</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>登録4ヶ月目</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>登録5ヶ月目</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>登録6ヶ月目以降</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
								<b>全体</b>
							</td>
						</tr>
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">売上</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">者数単価</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">件数</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">購入単価</td>

						</tr>
						@foreach($db_later_data as $month => $lines)
							<tr style="font:12px/120% 'メイリオ',sans-serif;">
								<td style="padding:3px;text-align:center;" class="pay">
									{{ $month }}月
								</td>
								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount1">
									{{ number_format($lines[0]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total1">
									{{ number_format($lines[0]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price1">
									{{ number_format($lines[0]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count1">
									{{ number_format($lines[0]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price1">
									{{ number_format($lines[0]['order_unit_price']) }}
								</td>

								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount2">
									{{ number_format($lines[1]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total2">
									{{ number_format($lines[1]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price2">
									{{ number_format($lines[1]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count2">
									{{ number_format($lines[1]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price2">
									{{ number_format($lines[1]['order_unit_price']) }}
								</td>

								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount3">
									{{ number_format($lines[2]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total3">
									{{ number_format($lines[2]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price3">
									{{ number_format($lines[2]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count3">
									{{ number_format($lines[2]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price3">
									{{ number_format($lines[2]['order_unit_price']) }}
								</td>

								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount4">
									{{ number_format($lines[3]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total4">
									{{ number_format($lines[3]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price4">
									{{ number_format($lines[3]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count4">
									{{ number_format($lines[3]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price4">
									{{ number_format($lines[3]['order_unit_price']) }}
								</td>

								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount5">
									{{ number_format($lines[4]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total5">
									{{ number_format($lines[4]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price5">
									{{ number_format($lines[4]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count5">
									{{ number_format($lines[4]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price5">
									{{ number_format($lines[4]['order_unit_price']) }}
								</td>

								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount6">
									{{ number_format($lines[5]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total6">
									{{ number_format($lines[5]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price6">
									{{ number_format($lines[5]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count6">
									{{ number_format($lines[5]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price6">
									{{ number_format($lines[5]['order_unit_price']) }}
								</td>

								<td style="padding:3px;text-align:center;background:#eee8aa;" class="total_amount">
									{{ number_format($db_data[$month]['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_total">
									{{ number_format($db_data[$month]['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#ffe4e1;" class="user_unit_price">
									{{ number_format($db_data[$month]['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count">
									{{ number_format($db_data[$month]['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;background:#fdf5e6;" class="order_unit_price">
									{{ number_format($db_data[$month]['order_unit_price']) }}
								</td>

							</tr>						
						@endforeach
						<tr style="background-color:gray;">
							<td style="padding:1px;" colspan="78"></td>
						</tr>
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
							<td style="padding:3px;text-align:center;font-weight:bold;">
								合計
							</td>
							<td style="padding:3px;text-align:center;" id="total_amount1">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total1">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price1">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count1">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price1">

							</td>

							<td style="padding:3px;text-align:center;" id="total_amount2">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total2">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price2">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count2">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price2">

							</td>

							<td style="padding:3px;text-align:center;" id="total_amount3">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total3">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price3">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count3">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price3">

							</td>

							<td style="padding:3px;text-align:center;" id="total_amount4">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total4">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price4">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count4">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price4">

							</td>

							<td style="padding:3px;text-align:center;" id="total_amount5">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total5">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price5">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count5">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price5">

							</td>

							<td style="padding:3px;text-align:center;" id="total_amount6">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total6">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price6">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count6">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price6">

							</td>

							<td style="padding:3px;text-align:center;" id="total_amount">

							</td>
							<td style="padding:3px;text-align:center;" id="user_total">

							</td>
							<td style="padding:3px;text-align:center;" id="user_unit_price">

							</td>
							<td style="padding:3px;text-align:center;" id="order_count">

							</td>
							<td style="padding:3px;text-align:center;" id="order_unit_price">

							</td>
						</tr>
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
							<td style="padding:3px;text-align:center;font-weight:bold;">
								平均
							</td>
							<td style="padding:3px;text-align:center;" id="ave_total_amount1">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total1">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price1">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count1">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price1">

							</td>

							<td style="padding:3px;text-align:center;" id="ave_total_amount2">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total2">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price2">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count2">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price2">

							</td>

							<td style="padding:3px;text-align:center;" id="ave_total_amount3">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total3">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price3">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count3">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price3">

							</td>

							<td style="padding:3px;text-align:center;" id="ave_total_amount4">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total4">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price4">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count4">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price4">

							</td>

							<td style="padding:3px;text-align:center;" id="ave_total_amount5">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total5">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price5">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count5">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price5">

							</td>

							<td style="padding:3px;text-align:center;" id="ave_total_amount6">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total6">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price6">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count6">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price6">

							</td>

							<td style="padding:3px;text-align:center;" id="ave_total_amount">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_total">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_user_unit_price">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_count">

							</td>
							<td style="padding:3px;text-align:center;" id="ave_order_unit_price">

							</td>
						</tr>
					</table>
					</center>
				@endif
			</div>
		</div>

        <div class="col-md-9 col-md-offset" style="width:1600px;">
            <div class="panel panel-default" style="background:white;float:left;width:50%;">
				<div id="amount_graph" style="height:300px;width:100%;"></div>
			</div>

            <div class="panel panel-default" style="background:white;float:left;width:50%;">
				<div id="regist_graph" style="height:300px;width:100%;"></div>
			</div>
		</div>

		<div class="col-md-9 col-md-offset" style="width:1600px;">
			<div class="panel panel-default" style="background:white;float:left;width:50%;">
				<div id="total_graph" style="height:300px;width:100%; "></div>
			</div>
			<div class="panel panel-default" style="background:white;float:left;width:50%;">
				<div id="pie_graph" style="height:300px;width:100%; "></div>
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
	var listAmount1 = {name:'当月登録',data:[]};
	var listAmount2 = {name:'登録2ヵ月目',data:[]};
	var listAmount3 = {name:'登録3ヵ月目',data:[]};
	var listAmount4 = {name:'登録4ヵ月目',data:[]};
	var listAmount5 = {name:'登録5ヵ月目',data:[]};
	var listAmount6 = {name:'登録6ヵ月目以降',data:[]};
	var listTotalAmount = {name:'売上全体',data:[]};

	var listPay1 = {name:'当月登録',data:[]};
	var listPay2 = {name:'登録2ヵ月目',data:[]};
	var listPay3 = {name:'登録3ヵ月目',data:[]};
	var listPay4 = {name:'登録4ヵ月目',data:[]};
	var listPay5 = {name:'登録5ヵ月目',data:[]};
	var listPay6 = {name:'登録6ヵ月目以降',data:[]};
	var listTotalPay = {name:'注文者数全体',data:[]};

	@foreach($db_later_data as $month => $lines)
		listMonth[listMonth.length] = '{{ $month }}月';
		listAmount1['data'][listAmount1['data'].length] = {{ $lines[0]['total_amount'] }};
		listAmount2['data'][listAmount2['data'].length] = {{ $lines[1]['total_amount'] }};
		listAmount3['data'][listAmount3['data'].length] = {{ $lines[2]['total_amount'] }};
		listAmount4['data'][listAmount4['data'].length] = {{ $lines[3]['total_amount'] }};
		listAmount5['data'][listAmount5['data'].length] = {{ $lines[4]['total_amount'] }};
		listAmount6['data'][listAmount6['data'].length] = {{ $lines[5]['total_amount'] }};
		listTotalAmount['data'][listTotalAmount['data'].length] = {{ $db_data[$month]['total_amount'] }};
		listPay1['data'][listPay1['data'].length] = {{ $lines[0]['user_total'] }};
		listPay2['data'][listPay2['data'].length] = {{ $lines[1]['user_total'] }};
		listPay3['data'][listPay3['data'].length] = {{ $lines[2]['user_total'] }};
		listPay4['data'][listPay4['data'].length] = {{ $lines[3]['user_total'] }};
		listPay5['data'][listPay5['data'].length] = {{ $lines[4]['user_total'] }};
		listPay6['data'][listPay6['data'].length] = {{ $lines[5]['user_total'] }};
		listTotalPay['data'][listTotalPay['data'].length] = {{ $db_data[$month]['user_total'] }};
	@endforeach

	Highcharts.chart('amount_graph', {

		title: {
			text: '売上'
		},

		subtitle: {
			text: ''
		},
		xAxis: {
			categories: listMonth
		},
		yAxis: {
			title: {
				text: '売上金額'
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

		series: [listAmount1, listAmount2, listAmount3, listAmount4, listAmount5, listAmount6],

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

	Highcharts.chart('regist_graph', {

		title: {
			text: '注文者数'
		},

		subtitle: {
			text: ''
		},
		xAxis: {
			categories: listMonth
		},
		yAxis: {
			title: {
				text: '注文者数'
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

		series: [listPay1, listPay2, listPay3, listPay4, listPay5, listPay6],

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

	Highcharts.chart('total_graph', {

		title: {
			text: '全体'
		},

		subtitle: {
			text: ''
		},
		xAxis: {
			categories: listMonth
		},
		yAxis: {
			title: {
				text: '売上金額'
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

		series: [listTotalAmount, listTotalPay],

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
	var total_amount	 = 0;
	var user_total		 = 0;
	var user_unit_price	 = 0;
	var order_count		 = 0;
	var order_unit_price = 0;

	var total_amount1	 = 0;
	var user_total1		 = 0;
	var user_unit_price1 = 0;
	var order_count1	 = 0;
	var order_unit_price1 = 0;

	var total_amount2	 = 0;
	var user_total2		 = 0;
	var user_unit_price2 = 0;
	var order_count2	 = 0;
	var order_unit_price2 = 0;

	var total_amount3	 = 0;
	var user_total3		 = 0;
	var user_unit_price3 = 0;
	var order_count3	 = 0;
	var order_unit_price3 = 0;

	var total_amount4	 = 0;
	var user_total4		 = 0;
	var user_unit_price4 = 0;
	var order_count4	 = 0;
	var order_unit_price4 = 0;

	var total_amount5	 = 0;
	var user_total5		 = 0;
	var user_unit_price5 = 0;
	var order_count5	 = 0;
	var order_unit_price5 = 0;

	var total_amount6	 = 0;
	var user_total6		 = 0;
	var user_unit_price6 = 0;
	var order_count6	 = 0;
	var order_unit_price6 = 0;


	$.when(
		//売上
		$('.total_amount').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount += parseInt(count);
		}),
		//者数
		$('.user_total').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price += parseInt(count);
		}),
		//件数
		$('.order_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price += parseInt(count);
		}),

		//売上
		$('.total_amount1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount1 += parseInt(count);
		}),
		//者数
		$('.user_total1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total1 += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price1 += parseInt(count);
		}),
		//件数
		$('.order_count1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count1 += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price1 += parseInt(count);
		}),

		//売上
		$('.total_amount2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount2 += parseInt(count);
		}),
		//者数
		$('.user_total2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total2 += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price2 += parseInt(count);
		}),
		//件数
		$('.order_count2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count2 += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price2 += parseInt(count);
		}),

		//売上
		$('.total_amount3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount3 += parseInt(count);
		}),
		//者数
		$('.user_total3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total3 += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price3 += parseInt(count);
		}),
		//件数
		$('.order_count3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count3 += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price3 += parseInt(count);
		}),

		//売上
		$('.total_amount4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount4 += parseInt(count);
		}),
		//者数
		$('.user_total4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total4 += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price4 += parseInt(count);
		}),
		//件数
		$('.order_count4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count4 += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price4 += parseInt(count);
		}),

		//売上
		$('.total_amount5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount5 += parseInt(count);
		}),
		//者数
		$('.user_total5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total5 += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price5 += parseInt(count);
		}),
		//件数
		$('.order_count5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count5 += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price5 += parseInt(count);
		}),

		//売上
		$('.total_amount6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			total_amount6 += parseInt(count);
		}),
		//者数
		$('.user_total6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_total6 += parseInt(count);
		}),
		//者数単価
		$('.user_unit_price6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price6 += parseInt(count);
		}),
		//件数
		$('.order_count6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count6 += parseInt(count);
		}),
		//購入単価
		$('.order_unit_price6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price6 += parseInt(count);
		}),
	).done(function(){
		//合計-
		$('#total_amount').text(total_amount.toLocaleString());
		$('#user_total').text(user_total.toLocaleString());
		$('#user_unit_price').text(user_unit_price.toLocaleString());
		$('#order_count').text(order_count.toLocaleString());
		$('#order_unit_price').text(order_unit_price.toLocaleString());

		//合計-
		$('#total_amount1').text(total_amount1.toLocaleString());
		$('#user_total1').text(user_total1.toLocaleString());
		$('#user_unit_price1').text(user_unit_price1.toLocaleString());
		$('#order_count1').text(order_count1.toLocaleString());
		$('#order_unit_price1').text(order_unit_price1.toLocaleString());

		//合計-
		$('#total_amount2').text(total_amount2.toLocaleString());
		$('#user_total2').text(user_total2.toLocaleString());
		$('#user_unit_price2').text(user_unit_price2.toLocaleString());
		$('#order_count2').text(order_count2.toLocaleString());
		$('#order_unit_price2').text(order_unit_price2.toLocaleString());

		//合計-
		$('#total_amount3').text(total_amount3.toLocaleString());
		$('#user_total3').text(user_total3.toLocaleString());
		$('#user_unit_price3').text(user_unit_price3.toLocaleString());
		$('#order_count3').text(order_count3.toLocaleString());
		$('#order_unit_price3').text(order_unit_price3.toLocaleString());

		//合計-
		$('#total_amount4').text(total_amount4.toLocaleString());
		$('#user_total4').text(user_total4.toLocaleString());
		$('#user_unit_price4').text(user_unit_price4.toLocaleString());
		$('#order_count4').text(order_count4.toLocaleString());
		$('#order_unit_price4').text(order_unit_price4.toLocaleString());

		//合計-
		$('#total_amount5').text(total_amount5.toLocaleString());
		$('#user_total5').text(user_total5.toLocaleString());
		$('#user_unit_price5').text(user_unit_price5.toLocaleString());
		$('#order_count5').text(order_count5.toLocaleString());
		$('#order_unit_price5').text(order_unit_price5.toLocaleString());

		//合計-
		$('#total_amount6').text(total_amount6.toLocaleString());
		$('#user_total6').text(user_total6.toLocaleString());
		$('#user_unit_price6').text(user_unit_price6.toLocaleString());
		$('#order_count6').text(order_count6.toLocaleString());
		$('#order_unit_price6').text(order_unit_price6.toLocaleString());


		//平均-
		var ave_total_amount = total_amount / {{ $average_count }};
		$('#ave_total_amount').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total / {{ $average_count }};
		$('#ave_user_total').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price / {{ $average_count }};
		$('#ave_user_unit_price').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count / {{ $average_count }};
		$('#ave_order_count').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price / {{ $average_count }};
		$('#ave_order_unit_price').text(ave_order_unit_price.toLocaleString());

		//平均-
		var ave_total_amount = total_amount1 / {{ $average_count }};
		$('#ave_total_amount1').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total1 / {{ $average_count }};
		$('#ave_user_total1').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price1 / {{ $average_count }};
		$('#ave_user_unit_price1').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count1 / {{ $average_count }};
		$('#ave_order_count1').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price1 / {{ $average_count }};
		$('#ave_order_unit_price1').text(ave_order_unit_price.toLocaleString());

		//平均-
		var ave_total_amount = total_amount2 / {{ $average_count }};
		$('#ave_total_amount2').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total2 / {{ $average_count }};
		$('#ave_user_total2').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price2 / {{ $average_count }};
		$('#ave_user_unit_price2').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count2 / {{ $average_count }};
		$('#ave_order_count2').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price2 / {{ $average_count }};
		$('#ave_order_unit_price2').text(ave_order_unit_price.toLocaleString());

		//平均-
		var ave_total_amount = total_amount3 / {{ $average_count }};
		$('#ave_total_amount3').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total3 / {{ $average_count }};
		$('#ave_user_total3').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price3 / {{ $average_count }};
		$('#ave_user_unit_price3').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count3 / {{ $average_count }};
		$('#ave_order_count3').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price3 / {{ $average_count }};
		$('#ave_order_unit_price3').text(ave_order_unit_price.toLocaleString());

		//平均-
		var ave_total_amount = total_amount4 / {{ $average_count }};
		$('#ave_total_amount4').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total4 / {{ $average_count }};
		$('#ave_user_total4').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price4 / {{ $average_count }};
		$('#ave_user_unit_price4').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count4 / {{ $average_count }};
		$('#ave_order_count4').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price4 / {{ $average_count }};
		$('#ave_order_unit_price4').text(ave_order_unit_price.toLocaleString());

		//平均-
		var ave_total_amount = total_amount5 / {{ $average_count }};
		$('#ave_total_amount5').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total5 / {{ $average_count }};
		$('#ave_user_total5').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price5 / {{ $average_count }};
		$('#ave_user_unit_price5').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count5 / {{ $average_count }};
		$('#ave_order_count5').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price5 / {{ $average_count }};
		$('#ave_order_unit_price5').text(ave_order_unit_price.toLocaleString());

		//平均-
		var ave_total_amount = total_amount6 / {{ $average_count }};
		$('#ave_total_amount6').text(ave_total_amount.toLocaleString());

		var ave_user_total = user_total6 / {{ $average_count }};
		$('#ave_user_total6').text(ave_user_total.toLocaleString());

		var ave_user_unit_price = user_unit_price6 / {{ $average_count }};
		$('#ave_user_unit_price6').text(ave_user_unit_price.toLocaleString());

		var ave_order_count = order_count6 / {{ $average_count }};
		$('#ave_order_count6').text(ave_order_count.toLocaleString());

		var ave_order_unit_price = order_unit_price6 / {{ $average_count }};
		$('#ave_order_unit_price6').text(ave_order_unit_price.toLocaleString());

		Highcharts.chart('pie_graph', {
			chart: {
				type: 'pie',
				options3d: {
					enabled: true,
					alpha: 45,
					beta: 0
				}
			},
			title: {
				text: '登録後の経過別 売上比'
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
					['当月登録 ' + Math.round((total_amount1 / total_amount)*100,2) + '%', total_amount1 / total_amount],
					['登録2ヶ月目 ' + Math.round((total_amount2 / total_amount)*100,2) + '%', total_amount2 / total_amount],
					['登録3ヶ月目 ' + Math.round((total_amount3 / total_amount)*100,2) + '%', total_amount3 / total_amount],
					['登録4ヶ月目 ' + Math.round((total_amount4 / total_amount)*100,2) + '%', total_amount4 / total_amount],
					['登録5ヶ月目 ' + Math.round((total_amount5 / total_amount)*100,2) + '%', total_amount5 / total_amount],
					['登録6ヶ月目以降 ' + Math.round((total_amount6 / total_amount)*100,2) + '%', total_amount6 / total_amount],
				]
			}]
		});


	});
});
</script>

@endsection
