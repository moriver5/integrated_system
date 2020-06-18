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
					<b><a href="/admin/member/analytics/access/{{$prev_year}}/{{ $prev_month }}/{{ $prev_day }}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/access/{{$prev_year}}/{{ $month }}">{{ $year }}年{{ $month }}月</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/access/{{$next_year}}/{{ $next_month }}/{{ $next_day }}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;" rowspan="2">
									<b>{{ $day }}日</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
									<b>入無し</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
									<b>入有り</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" rowspan="2">
									<b>全体</b>
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
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>24h</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>24h</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>24h</b>
								</td>
							</tr>
							@foreach($db_data as $day => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;">
										{{ $day }}時
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
									<td style="padding:3px;text-align:center;" class="no_pay24">
										{{ $lines['no_pay24'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="pay24">
										{{ $lines['pay24'] }}
									</td>
									<td style="padding:3px;text-align:center;" class="total24">
										{{ $lines['total24'] }}
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
								<td style="padding:3px;text-align:center;" id="total_no_pay24">

								</td>
								<td style="padding:3px;text-align:center;" id="total_pay24">

								</td>
								<td style="padding:3px;text-align:center;" id="total_amount24">

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
								<td style="padding:3px;text-align:center;" id="no_pay_average24">

								</td>
								<td style="padding:3px;text-align:center;" id="pay_average24">

								</td>
								<td style="padding:3px;text-align:center;" id="total_average24">

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
	var no_pay			 = 0;
	var pay				 = 0;
	var total			 = 0;
	var no_pay24		 = 0;
	var pay24			 = 0;
	var total24			 = 0;
	var no_pay_average24 = 0;
	var pay_average24	 = 0;
	var total_average24	 = 0;
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
		}),

		//入金なし合計
		$('.no_pay24').each(function(){
			no_pay24 += parseInt($(this).text());
		}),
		
		//入金あり合計
		$('.pay24').each(function(){
			pay24 += parseInt($(this).text());
		}),
		
		//全体の合計
		$('.total24').each(function(){
			total24 += parseInt($(this).text());
		})

	).done(function(){
		//入金なし合計
		$('#total_no_pay').text(no_pay);

		//入金あり合計
		$('#total_pay').text(pay);
		
		//全体の合計
		$('#total_amount').text(total);

		//入金なし合計
		$('#total_no_pay24').text(no_pay24);
		
		//入金あり合計
		$('#total_pay24').text(pay24);
		
		//全体の合計
		$('#total_amount24').text(total24);

		//入金なし平均
		$('#no_pay_average').text(getFloor(no_pay/24, 1));
		
		//入金あり平均
		$('#pay_average').text(getFloor(pay/24, 1));
		
		//全体の平均
		$('#total_average').text(getFloor(total/24, 1));

		//入金なし平均
		$('#no_pay_average24').text(getFloor(no_pay24/24, 1));
		
		//入金あり平均
		$('#pay_average24').text(getFloor(pay24/24, 1));
		
		//全体の平均
		$('#total_average24').text(getFloor(total24/24, 1));
	});
});
</script>

@endsection
