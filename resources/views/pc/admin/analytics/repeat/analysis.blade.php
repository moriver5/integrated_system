@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>リピートユーザー解析(購入回数順)</b>
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
									<b>ログインID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:45px;">
									<b>登録状態</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>購入回数合計</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:120px;">
									<b>購入金額合計</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:45px;">
									<b>ポイント</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>初回購入日</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>最終購入日</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>最終アクセス</b>
								</td>
							</tr>
							@foreach($db_data as $login_id => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;" class="no_pay">
										<a href="/admin/member/client/edit/{{$currentPage}}/{{ $lines['client_id'] }}" target="_blank">{{ $login_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines['status'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										<a href="/admin/member/client/edit/{{ $lines['client_id'] }}/order/history" target="_blank">{{ $lines['pay_num'] }}</a>回
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										{{ $lines['amount'] }}円
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines['point'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										<a href="/admin/member/client/edit/{{ $lines['client_id'] }}/order/history/{{ $lines['first_order_id'] }}" target="_blank">{{ $lines['first_date'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										<a href="/admin/member/client/edit/{{ $lines['client_id'] }}/order/history/{{ $lines['end_order_id'] }}" target="_blank">{{ $lines['end_date'] }}</a>
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines['last_access'] }}
									</td>
								</tr>
							@endforeach
						</table>
						</center>
					@endif
				</div>
			</div>
        </div>

        <div class="col-md-9 col-md-offset-2">
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
	var listBuy = {name:'ログインID',data:[]};
	var listAmount = {name:'購入回数合計',data:[]};

	@foreach($db_data as $login_id => $lines)
		listMonth[listMonth.length] = '{{ $login_id }}';
		listBuy['data'][listBuy['data'].length] = {{ $lines['pay_num'] }};
	@endforeach

	Highcharts.chart('access_graph', {
		chart: {
			type: 'bar'
		},
		title: {
			text: 'リピートユーザー解析'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			title: {
				text: 'ログインID'
			},
			categories: listMonth,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '購入回数合計'
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
			headerFormat: '<span style="font-size:10px">商品ID：{point.key}</span><table>',
			pointFormat: '<tr><td style="color:{series.color};padding:2">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			series: {

				dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				}
			}
		},
		series: [listBuy, listAmount],
	});

});
</script>

@endsection
