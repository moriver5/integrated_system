@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading" style="text-align:center;">
					<b>新規入金</b>
				</div>
				<div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/newpayment/{{$prev_year}}/{{$prev_month}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/newpayment/{{ $year }}">{{ $year }}年</a>{{ $month }}月&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/newpayment/{{$next_year}}/{{$next_month}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
									{{ $year }}
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
									<b>登録者数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;border-left:hidden;">
									<b>&nbsp;</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;bold;border-left:hidden;">
									<b>&nbsp;</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="5">
									<b>登録から初回入金までの経過日数 ／（人数）</b>
								</td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									初回入金者
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>入金率</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>初日～7日目</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>8日～14日目</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>15日～30日目</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>31日～60日目</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>61日目以降</b>
								</td>
							</tr>
							@foreach($db_data as $day => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/analytics/newpayment/{{ $year }}/{{ $month }}/{{ $day }}">{{ $day }}日</a>
									</td>
									<td style="padding:3px;text-align:center;" class="regist_count">
										{{ number_format($lines['total_regist']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="user_count">
										{{ number_format($lines['total_first_pay']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="user_payment_rate">
										{{ number_format($lines['payment_rate']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_count1">
										{{ number_format($lines['elapsed_day1']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_count2">
										{{ number_format($lines['elapsed_day8']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_count3">
										{{ number_format($lines['elapsed_day15']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_count4">
										{{ number_format($lines['elapsed_day31']) }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay_count5">
										{{ number_format($lines['elapsed_day61']) }}
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
								<td style="padding:3px;text-align:center;" id="total_regist">

								</td>
								<td style="padding:3px;text-align:center;" id="total_user">

								</td>
								<td style="padding:3px;text-align:center;" id="total_payment_rate">

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
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									平均
								</td>
								<td style="padding:3px;text-align:center;" id="average_regist">

								</td>
								<td style="padding:3px;text-align:center;" id="average_user">

								</td>
								<td style="padding:3px;text-align:center;" id="average_payment_rate">

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
							</tr>
						</table>
						</center>
					@endif
				</div>
			</div>
		</div>

        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default" style="background:white;">
				<div id="newpay_graph" style="height:500px;width:100%; "></div>
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
	var listPay7 = {name:'初日～7日目',data:[]};
	var listPay14 = {name:'8日～14日目',data:[]};
	var listPay30 = {name:'15日～30日目',data:[]};
	var listPay60 = {name:'31日～60日目',data:[]};
	var listPay61 = {name:'61日目以降',data:[]};
	var listRegist = {name:'登録者数',data:[]};
	var listFirstPay= {name:'初回入金者',data:[]};

	@foreach($db_data as $month => $lines)
		listMonth[listMonth.length] = '{{ $month }}日';
		listPay7['data'][listPay7['data'].length] = {{ $lines['elapsed_day1'] }};
		listPay14['data'][listPay14['data'].length] = {{ $lines['elapsed_day8'] }};
		listPay30['data'][listPay30['data'].length] = {{ $lines['elapsed_day15'] }};
		listPay60['data'][listPay60['data'].length] = {{ $lines['elapsed_day31'] }};
		listPay61['data'][listPay61['data'].length] = {{ $lines['elapsed_day61'] }};
		listRegist['data'][listRegist['data'].length] = {{ $lines['total_regist'] }};
		listFirstPay['data'][listFirstPay['data'].length] = {{ $lines['total_first_pay'] }};
	@endforeach

	Highcharts.chart('newpay_graph', {

		title: {
			text: '新規入金'
		},

		subtitle: {
			text: ''
		},
		xAxis: {
			categories: listMonth
		},
		yAxis: {
			title: {
				text: '入金者数'
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

		series: [listRegist, listFirstPay, listPay7, listPay14, listPay30, listPay60, listPay61],

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
	var regist_count = 0;
	var user_count	 = 0;
	var user_payment_rate = 0;
	var pay_count1	 = 0;
	var pay_count2	 = 0;
	var pay_count3	 = 0;
	var pay_count4	 = 0;
	var pay_count5	 = 0;

	var ave_regist_count = 0;
	var ave_user_count	 = 0;

	var ave_pay_count1	 = 0;
	var ave_pay_count2	 = 0;
	var ave_pay_count3	 = 0;
	var ave_pay_count4	 = 0;
	var ave_pay_count5	 = 0;

	$.when(
		//登録者数
		$('.regist_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			regist_count += parseInt(count);
		}),
		//初回入金者
		$('.user_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_count += parseInt(count);
		}),
		//入金率
		$('.user_payment_rate').each(function(){
			var count = $(this).text().replace(/,/g,"");
			user_payment_rate += parseInt(count);
		}),
		//初日～7日目
		$('.pay_count1').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count1 += parseInt(count);
		}),
		//8日～14日目
		$('.pay_count2').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count2 += parseInt(count);
		}),
		//15日～30日目
		$('.pay_count3').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count3 += parseInt(count);
		}),
		//31日～60日目
		$('.pay_count4').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count4 += parseInt(count);
		}),
		//61日目以降
		$('.pay_count5').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count5 += parseInt(count);
		})
	).done(function(){
		//合計-登録者数
		$('#total_regist').text(regist_count.toLocaleString());

		//合計-初回入金者
		$('#total_user').text(user_count.toLocaleString());

		//合計-入金率
		var user_payment_rate = 0;
		if( user_count > 0 ){
			user_payment_rate = Math.round(user_count / regist_count);
			$('#total_payment_rate').text(user_payment_rate.toLocaleString());
		}else{
			$('#total_payment_rate').text(0);			
		}

		//合計-登録から初回入金までの経過日数
		$('#total_pay1').text(pay_count1.toLocaleString());
		$('#total_pay2').text(pay_count2.toLocaleString());
		$('#total_pay3').text(pay_count3.toLocaleString());
		$('#total_pay4').text(pay_count4.toLocaleString());
		$('#total_pay5').text(pay_count5.toLocaleString());

		//平均-登録者数
		ave_regist_count = regist_count / {{ $average_count }};
		$('#average_regist').text(ave_regist_count.toLocaleString());

		//平均-初回入金者
		ave_user_count = user_count / {{ $average_count }};
		$('#average_user').text(ave_user_count.toLocaleString());

		//平均-入金率
		$('#average_payment_rate').text(user_payment_rate.toLocaleString());

		//平均-登録から初回入金までの経過日数
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
	});
});
</script>

@endsection
