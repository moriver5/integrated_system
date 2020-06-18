@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
		<div class="panel panel-default" style="width:1400px;">
			<div class="panel-heading" style="text-align:center;">
				<b>入金構成</b>
			</div>
			<div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
				<b><a href="/admin/member/analytics/payment/structure/{{$prev_year}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{ $year }}年&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/payment/structure/{{$next_year}}">NEXT</a></b>
			</div>
			<div class="panel-heading">
				@if( !empty($db_data) )
					<center>
					<table border="1" align="center" style="width:100%;">
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;" rowspan="2">
								{{ $year }}
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
								<b>売上</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
								<b>者数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;border-left:hidden;">
								<b>&nbsp;</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
								<b>件数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;border-left:hidden;">
								<b>&nbsp;</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="10">
								<b>件数内訳（入金回数別）</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
								<b>{{ $year }}</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="10">
								<b>売上内訳（入金回数別）</b>
							</td>
						</tr>
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
								者数単価
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>件数単価</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>初回購入</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>2回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>3回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>4回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>5回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>6回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>7回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>8回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>9回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>10回以上</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>初回購入</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>2回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>3回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>4回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>5回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>6回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>7回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>8回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>9回</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>10回以上</b>
							</td>
						</tr>
						@foreach($db_data as $month => $lines)
							<tr style="font:12px/120% 'メイリオ',sans-serif;">
								<td style="padding:3px;text-align:center;" class="pay">
									<a href="/admin/member/analytics/payment/structure/{{ $year }}/{{ $month }}">{{ $month }}月</a>
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count">
									{{ number_format($lines['total_amount']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_count">
									{{ number_format($lines['user_total']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="user_unit_price">
									{{ number_format($lines['user_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_count">
									{{ number_format($lines['order_count']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="order_unit_price">
									{{ number_format($lines['order_unit_price']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count1">
									{{ number_format($lines['pay_count1']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count2">
									{{ number_format($lines['pay_count2']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count3">
									{{ number_format($lines['pay_count3']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count4">
									{{ number_format($lines['pay_count4']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count5">
									{{ number_format($lines['pay_count5']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count6">
									{{ number_format($lines['pay_count6']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count7">
									{{ number_format($lines['pay_count7']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count8">
									{{ number_format($lines['pay_count8']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count9">
									{{ number_format($lines['pay_count9']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="pay_count10">
									{{ number_format($lines['pay_count10']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="">
									<a href="/admin/member/analytics/payment/structure/{{ $year }}/{{ $month }}">{{ $month }}月</a>
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count1">
									{{ number_format($lines['amount_count1']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count2">
									{{ number_format($lines['amount_count2']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count3">
									{{ number_format($lines['amount_count3']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count4">
									{{ number_format($lines['amount_count4']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count5">
									{{ number_format($lines['amount_count5']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count6">
									{{ number_format($lines['amount_count6']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count7">
									{{ number_format($lines['amount_count7']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count8">
									{{ number_format($lines['amount_count8']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count9">
									{{ number_format($lines['amount_count9']) }}
								</td>
								<td style="padding:3px;text-align:center;" class="amount_count10">
									{{ number_format($lines['amount_count10']) }}
								</td>
							</tr>
						@endforeach
						<tr style="background-color:gray;">
							<td style="padding:1px;" colspan="27"></td>
						</tr>
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
							<td style="padding:3px;text-align:center;font-weight:bold;">
								合計
							</td>
							<td style="padding:3px;text-align:center;" id="total_amount">

							</td>
							<td style="padding:3px;text-align:center;" id="total_user">

							</td>
							<td style="padding:3px;text-align:center;" id="total_user_unit_price">

							</td>
							<td style="padding:3px;text-align:center;" id="total_order">

							</td>
							<td style="padding:3px;text-align:center;" id="total_order_unit_price">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay1">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay2">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay3">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay4">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay5">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay6">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay7">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay8">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay9">

							</td>
							<td style="padding:3px;text-align:center;" id="total_pay10">

							</td>
							<td style="padding:3px;text-align:center;">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount1">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount2">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount3">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount4">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount5">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount6">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount7">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount8">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount9">

							</td>
							<td style="padding:3px;text-align:center;" id="total_amount10">

							</td>
						</tr>
						<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
							<td style="padding:3px;text-align:center;font-weight:bold;">
								平均
							</td>
							<td style="padding:3px;text-align:center;" id="average_amount">

							</td>
							<td style="padding:3px;text-align:center;" id="average_user">

							</td>
							<td style="padding:3px;text-align:center;" id="average_user_unit_price">

							</td>
							<td style="padding:3px;text-align:center;" id="average_order">

							</td>
							<td style="padding:3px;text-align:center;" id="average_order_unit_price">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay1">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay2">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay3">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay4">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay5">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay6">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay7">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay8">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay9">

							</td>
							<td style="padding:3px;text-align:center;" id="average_pay10">

							</td>
							<td style="padding:3px;text-align:center;">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount1">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount2">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount3">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount4">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount5">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount6">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount7">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount8">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount9">

							</td>
							<td style="padding:3px;text-align:center;" id="average_amount10">

							</td>
						</tr>
					</table>
					</center>
				@endif
			</div>
		</div>

        <div class="col-md-10 col-md-offset" style="width:1400px;">
            <div class="panel panel-default" style="background:white;float:left;width:50%;">
				<div id="pay_graph" style="height:400px;width:100%;"></div>
			</div>
            <div class="panel panel-default" style="background:white;float:left;width:50%;">
				<div id="amount_graph" style="height:400px;width:100%;"></div>
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
	var listPay1 = {name:'',data:[]};
	var listPay2 = {name:'',data:[]};
	var listPay3 = {name:'',data:[]};
	var listPay4 = {name:'',data:[]};
	var listPay5 = {name:'',data:[]};
	var listPay6 = {name:'',data:[]};
	var listPay7 = {name:'',data:[]};
	var listPay8 = {name:'',data:[]};
	var listPay9 = {name:'',data:[]};
	var listPay10 = {name:'',data:[]};
	var listAmount = {name:'売上合計',data:[]};
	var listPayNum= {name:'入金者数',data:[]};
	var listOrderNum= {name:'注文件数',data:[]};

	@foreach($db_data as $month => $lines)
		listMonth[listMonth.length] = '{{ $month }}月';
		listPay1['data'][listPay1['data'].length] = {{ $lines['amount_count1'] }};
		listPay2['data'][listPay2['data'].length] = {{ $lines['amount_count2'] }};
		listPay3['data'][listPay3['data'].length] = {{ $lines['amount_count3'] }};
		listPay4['data'][listPay4['data'].length] = {{ $lines['amount_count4'] }};
		listPay5['data'][listPay5['data'].length] = {{ $lines['amount_count5'] }};
		listPay6['data'][listPay6['data'].length] = {{ $lines['amount_count6'] }};
		listPay7['data'][listPay7['data'].length] = {{ $lines['amount_count7'] }};
		listPay8['data'][listPay8['data'].length] = {{ $lines['amount_count8'] }};
		listPay9['data'][listPay9['data'].length] = {{ $lines['amount_count9'] }};
		listPay10['data'][listPay10['data'].length] = {{ $lines['amount_count10'] }};
		listAmount['data'][listAmount['data'].length] = {{ $lines['total_amount'] }};
		listPayNum['data'][listPayNum['data'].length] = {{ $lines['user_total'] }};
		listOrderNum['data'][listOrderNum['data'].length] = {{ $lines['order_count'] }};

		@if( $month == 1 )
			listPay1['name'] = '{{ $month }}回';
		@elseif( $month == 2 )
			listPay2['name'] = '{{ $month }}回';
		@elseif( $month == 3 )
			listPay3['name'] = '{{ $month }}回';
		@elseif( $month == 4 )
			listPay4['name'] = '{{ $month }}回';
		@elseif( $month == 5 )
			listPay5['name'] = '{{ $month }}回';
		@elseif( $month == 6 )
			listPay6['name'] = '{{ $month }}回';
		@elseif( $month == 7 )
			listPay7['name'] = '{{ $month }}回';
		@elseif( $month == 8 )
			listPay8['name'] = '{{ $month }}回';
		@elseif( $month == 9 )
			listPay9['name'] = '{{ $month }}回';
		@elseif( $month == 10 )
			listPay10['name'] = '{{ $month }}回';
		@endif
	@endforeach

	Highcharts.chart('pay_graph', {

		title: {
			text: '売上内訳(入金回数別)'
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

		series: [listPay1, listPay2, listPay3, listPay4, listPay5, listPay6, listPay7, listPay8, listPay9, listPay10],

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

	Highcharts.chart('amount_graph', {

		title: {
			text: '売上合計金額'
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

		series: [listAmount, listPayNum, listOrderNum],

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
	var amount_count = 0;
	var user_count	 = 0;
	var user_unit_price = 0;
	var order_count	 = 0;
	var order_unit_price = 0;
	var pay_count1	 = 0;
	var pay_count2	 = 0;
	var pay_count3	 = 0;
	var pay_count4	 = 0;
	var pay_count5	 = 0;
	var pay_count6	 = 0;
	var pay_count7	 = 0;
	var pay_count8	 = 0;
	var pay_count9	 = 0;
	var pay_count10	 = 0;

	var ave_amount_count = 0;
	var ave_user_count	 = 0;
	var ave_order_count	 = 0;
	var ave_order_unit_price = 0;

	var amount_count1 = 0;
	var amount_count2 = 0;
	var amount_count3 = 0;
	var amount_count4 = 0;
	var amount_count5 = 0;
	var amount_count6 = 0;
	var amount_count7 = 0;
	var amount_count8 = 0;
	var amount_count9 = 0;
	var amount_count10 = 0;

	var ave_pay_count1	 = 0;
	var ave_pay_count2	 = 0;
	var ave_pay_count3	 = 0;
	var ave_pay_count4	 = 0;
	var ave_pay_count5	 = 0;
	var ave_pay_count6	 = 0;
	var ave_pay_count7	 = 0;
	var ave_pay_count8	 = 0;
	var ave_pay_count9	 = 0;
	var ave_pay_count10	 = 0;

	var ave_amount_count1 = 0;
	var ave_amount_count2 = 0;
	var ave_amount_count3 = 0;
	var ave_amount_count4 = 0;
	var ave_amount_count5 = 0;
	var ave_amount_count6 = 0;
	var ave_amount_count7 = 0;
	var ave_amount_count8 = 0;
	var ave_amount_count9 = 0;
	var ave_amount_count10 = 0;

	$.when(
		//
		$('.amount_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count += parseInt(count);
		}),
		//
		$('.user_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_count += parseInt(count);
		}),
		//
		$('.user_unit_price').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_unit_price += parseInt(count);
		}),
		//
		$('.order_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_count += parseInt(count);
		}),
		//
		$('.order_unit_price').each(function(){
			var count = $(this).text().replace(/,/g,"");
			order_unit_price += parseInt(count);
		}),
		//
		$('.pay_count1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count1 += parseInt(count);
		}),
		//
		$('.pay_count2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count2 += parseInt(count);
		}),
		//
		$('.pay_count3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count3 += parseInt(count);
		}),
		//
		$('.pay_count4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count4 += parseInt(count);
		}),
		//
		$('.pay_count5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count5 += parseInt(count);
		}),
		//
		$('.pay_count6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count6 += parseInt(count);
		}),
		//
		$('.pay_count7').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count7 += parseInt(count);
		}),
		//
		$('.pay_count8').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count8 += parseInt(count);
		}),
		//
		$('.pay_count9').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count9 += parseInt(count);
		}),
		//
		$('.pay_count10').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count10 += parseInt(count);
		}),
		//
		$('.amount_count1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count1 += parseInt(count);
		}),
		//
		$('.amount_count2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count2 += parseInt(count);
		}),
		//
		$('.amount_count3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count3 += parseInt(count);
		}),
		//
		$('.amount_count4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count4 += parseInt(count);
		}),
		//
		$('.amount_count5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count5 += parseInt(count);
		}),
		//
		$('.amount_count6').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count6 += parseInt(count);
		}),
		//
		$('.amount_count7').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count7 += parseInt(count);
		}),
		//
		$('.amount_count8').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count8 += parseInt(count);
		}),
		//
		$('.amount_count9').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count9 += parseInt(count);
		}),
		//
		$('.amount_count10').each(function(){
			var count = $(this).text().replace(/,/g,"");
			amount_count10 += parseInt(count);
		})
	).done(function(){
		//合計
		$('#total_amount').text(amount_count.toLocaleString());
		$('#total_user').text(user_count.toLocaleString());

		var user_unit_price = 0;
		if( user_count > 0 ){
			user_unit_price = Math.round(amount_count / user_count);
			$('#total_user_unit_price').text(user_unit_price.toLocaleString());
		}else{
			$('#total_user_unit_price').text(0);			
		}

		var order_unit_price = 0;
		if( order_count > 0 ){
			order_unit_price = Math.round(amount_count / order_count);
			$('#total_order_unit_price').text(order_unit_price.toLocaleString());
		}else{
			$('#total_order_unit_price').text(0);			
		}

		$('#total_order').text(order_count.toLocaleString());
		$('#total_pay1').text(pay_count1.toLocaleString());
		$('#total_pay2').text(pay_count2.toLocaleString());
		$('#total_pay3').text(pay_count3.toLocaleString());
		$('#total_pay4').text(pay_count4.toLocaleString());
		$('#total_pay5').text(pay_count5.toLocaleString());
		$('#total_pay6').text(pay_count6.toLocaleString());
		$('#total_pay7').text(pay_count7.toLocaleString());		
		$('#total_pay8').text(pay_count8.toLocaleString());		
		$('#total_pay9').text(pay_count9.toLocaleString());		
		$('#total_pay10').text(pay_count10.toLocaleString());

		$('#total_amount1').text(amount_count1.toLocaleString());
		$('#total_amount2').text(amount_count2.toLocaleString());
		$('#total_amount3').text(amount_count3.toLocaleString());
		$('#total_amount4').text(amount_count4.toLocaleString());
		$('#total_amount5').text(amount_count5.toLocaleString());
		$('#total_amount6').text(amount_count6.toLocaleString());
		$('#total_amount7').text(amount_count7.toLocaleString());		
		$('#total_amount8').text(amount_count8.toLocaleString());		
		$('#total_amount9').text(amount_count9.toLocaleString());		
		$('#total_amount10').text(amount_count10.toLocaleString());		

		//平均
		ave_amount_count = amount_count / {{ $average_count }};
		$('#average_amount').text(ave_amount_count.toLocaleString());

		ave_user_count = user_count / {{ $average_count }};
		$('#average_user').text(ave_user_count.toLocaleString());

		$('#average_user_unit_price').text(user_unit_price.toLocaleString());

		ave_order_count = order_count / {{ $average_count }};
		$('#average_order').text(ave_order_count.toLocaleString());

		$('#average_order_unit_price').text(order_unit_price.toLocaleString());

		ave_pay_count1 = pay_count1 / {{ $average_count }};
		$('#average_pay1').text(ave_pay_count1.toLocaleString());

		ave_pay_count2 = pay_count2 / {{ $average_count }};
		$('#average_pay2').text(ave_pay_count2.toLocaleString());

		ave_pay_count3 = pay_count3 / {{ $average_count }};
		$('#average_pay3').text(ave_pay_count3.toLocaleString());

		ave_pay_count4 = pay_count4 / {{ $average_count }};
		$('#average_pay4').text(ave_pay_count4.toLocaleString());

		ave_pay_count5 = pay_count5 / {{ $average_count }};
		$('#average_pay5').text(ave_pay_count5.toLocaleString());

		ave_pay_count6 = pay_count6 / {{ $average_count }};
		$('#average_pay6').text(ave_pay_count6.toLocaleString());

		ave_pay_count7 = pay_count7 / {{ $average_count }};
		$('#average_pay7').text(ave_pay_count7.toLocaleString());

		ave_pay_count8 = pay_count8 / {{ $average_count }};
		$('#average_pay8').text(ave_pay_count8.toLocaleString());

		ave_pay_count9 = pay_count9 / {{ $average_count }};
		$('#average_pay9').text(ave_pay_count9.toLocaleString());

		ave_pay_count10 = pay_count10 / {{ $average_count }};
		$('#average_pay10').text(ave_pay_count10.toLocaleString());

		ave_amount_count1 = amount_count1 / {{ $average_count }};
		$('#average_amount1').text(ave_amount_count1.toLocaleString());

		ave_amount_count2 = amount_count2 / {{ $average_count }};
		$('#average_amount2').text(ave_amount_count2.toLocaleString());

		ave_amount_count3 = amount_count3 / {{ $average_count }};
		$('#average_amount3').text(ave_amount_count3.toLocaleString());

		ave_amount_count4 = amount_count4 / {{ $average_count }};
		$('#average_amount4').text(ave_amount_count4.toLocaleString());

		ave_amount_count5 = amount_count5 / {{ $average_count }};
		$('#average_amount5').text(ave_amount_count5.toLocaleString());

		ave_amount_count6 = amount_count6 / {{ $average_count }};
		$('#average_amount6').text(ave_amount_count6.toLocaleString());

		ave_amount_count7 = amount_count7 / {{ $average_count }};
		$('#average_amount7').text(ave_amount_count7.toLocaleString());

		ave_amount_count8 = amount_count8 / {{ $average_count }};
		$('#average_amount8').text(ave_amount_count8.toLocaleString());

		ave_amount_count9 = amount_count9 / {{ $average_count }};
		$('#average_amount9').text(ave_amount_count9.toLocaleString());

		ave_amount_count10 = amount_count10 / {{ $average_count }};
		$('#average_amount10').text(ave_amount_count10.toLocaleString());

	});
});
</script>

@endsection
