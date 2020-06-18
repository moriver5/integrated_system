@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>商品解析</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					全件数：{{ count($db_data) }} 件
					({{$currentPage}} / {{$lastPage}}㌻)
					<center>{{ $links }}</center>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>商品ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:35px;">
									<b>公開</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>購入者(ユニーク)</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>価格</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>売上金額</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>公開開始日</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
									<b>公開終了日</b>
								</td>
							</tr>
							@foreach($db_data as $product_id => $lines)
								@if( $lines['open_flg'] == 1 && !empty($lines['period_flg']) )
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
								@else
								<tr style="font:12px/120% 'メイリオ',sans-serif;background:gainsboro;">
								@endif
									<td style="padding:3px;text-align:center;">
										<a href="/admin/member/page/product/edit/{{ $currentPage }}/{{ $product_id }}" target="_blank">{{ $product_id }}</a>
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['open_flg'] == 1 && !empty($lines['period_flg']) )
										公開中
										@elseif( $lines['open_flg'] == 1 && empty($lines['period_flg']))
										期限切れ
										@else
										非公開
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/analytics/products/pay/{{ $product_id }}" target="_blank">{{ count($lines['costmer']) }}</a>
									</td>
									<td style="padding:3px;text-align:center;" class="price">
										{{ $lines['price'] }}円
									</td>
									<td style="padding:3px;text-align:center;" class="total">
										{{ $lines['amount'] }}円
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines['start_date'] }}
									</td>
									<td style="padding:3px;text-align:center;">
										{{ $lines['end_date'] }}
									</td>
								</tr>
							@endforeach
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									合計
								</td>
								<td style="padding:3px;text-align:center;">

								</td>
								<td style="padding:3px;text-align:center;" id="total_pay">

								</td>
								<td style="padding:3px;text-align:center;" id="total_price">

								</td>
								<td style="padding:3px;text-align:center;" id="total_amount">

								</td>
								<td style="padding:3px;text-align:center;background:white;" colspan="2" rowspan="2">

								</td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffffc6;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									平均
								</td>
								<td style="padding:3px;text-align:center;">

								</td>
								<td style="padding:3px;text-align:center;" id="pay_average">

								</td>
								<td style="padding:3px;text-align:center;" id="price_average">

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

        <div class="col-md-8 col-md-offset-2">
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
	var listBuy = {name:'購入者',data:[]};
	var listAmount = {name:'売上金額',data:[]};

	@foreach($db_data as $product_id => $lines)
		listMonth[listMonth.length] = '{{ $product_id }}';
		listBuy['data'][listBuy['data'].length] = {{ $lines['buy'] }};
		listAmount['data'][listAmount['data'].length] = {{ $lines['amount'] }};
	@endforeach

	Highcharts.chart('access_graph', {
		chart: {
			type: 'bar'
		},
		title: {
			text: '商品解析'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			title: {
				text: '商品ID'
			},
			categories: listMonth,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '売上金額'
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

	//合計/平均算出のための変数
	var pay				 = 0;
	var total			 = 0;
	var price			 = 0;
	var pay_average		 = 0;
	var total_average	 = 0;
	var price_average	 = 0;
	
	$.when(
		//購入者合計
		$('.pay').each(function(){
			pay += parseInt($(this).text());
		}),
		
		//価格合計
		$('.price').each(function(){
			price += parseInt($(this).text());
		}),
		
		//売上の合計
		$('.total').each(function(){
			total += parseInt($(this).text());
		})
	).done(function(){
		//購入者合計
		$('#total_pay').text(pay+"人");

		//価格の合計
		$('#total_price').text(price+"円");

		//全体の合計
		$('#total_amount').text(total+"円");

		//購入者平均
		$('#pay_average').text(getFloor(pay/12, 1)+"人");

		//価格平均
		$('#price_average').text(getFloor(price/12, 1)+"円");

		//売上の平均
		$('#total_average').text(getFloor(total/12, 1)+"円");
	});
});
</script>

@endsection
