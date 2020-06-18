@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>PVログ</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/pv/access/{{$prev_year}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{ $year }}年&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/pv/access/{{$next_year}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:210px;">
									
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>1月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>2月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>3月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>4月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>5月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>6月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>7月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>8月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>9月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>10月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>11月</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>12月</b>
								</td>
							</tr>
							@foreach($db_data as $url => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:left;" class="pay">
										{{ $url }}
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['1']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/1/{{ $lines['1']['id'] }}" class="jan_count">{{ $lines['1']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['2']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/2/{{ $lines['2']['id'] }}" class="feb_count">{{ $lines['2']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['3']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/3/{{ $lines['3']['id'] }}" class="mar_count">{{ $lines['3']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['4']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/4/{{ $lines['4']['id'] }}" class="apr_count">{{ $lines['4']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['5']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/5/{{ $lines['5']['id'] }}" class="may_count">{{ $lines['5']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['6']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/6/{{ $lines['6']['id'] }}" class="jun_count">{{ $lines['6']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['7']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/7/{{ $lines['7']['id'] }}" class="jul_count">{{ $lines['7']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['8']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/8/{{ $lines['8']['id'] }}" class="aug_count">{{ $lines['8']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['9']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/9/{{ $lines['9']['id'] }}" class="sep_count">{{ $lines['9']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['10']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/10/{{ $lines['10']['id'] }}" class="oct_count">{{ $lines['10']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['11']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/11/{{ $lines['11']['id'] }}" class="nov_count">{{ $lines['11']['total'] }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:3px;text-align:center;">
										@if( $lines['12']['total'] > 0 )
										<a href="/admin/member/analytics/pv/access/{{ $year }}/12/{{ $lines['12']['id'] }}" class="dec_count">{{ $lines['12']['total'] }}</a>
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
								<td style="padding:3px;text-align:center;" id="total_nov">

								</td>
								<td style="padding:3px;text-align:center;" id="total_dec">

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
			jan_count += parseInt($(this).text());
		}),
		//
		$('.feb_count').each(function(){
			feb_count += parseInt($(this).text());
		}),
		//
		$('.mar_count').each(function(){
			mar_count += parseInt($(this).text());
		}),
		//
		$('.apr_count').each(function(){
			apr_count += parseInt($(this).text());
		}),
		//
		$('.may_count').each(function(){
			may_count += parseInt($(this).text());
		}),
		//
		$('.jun_count').each(function(){
			jun_count += parseInt($(this).text());
		}),
		//
		$('.jul_count').each(function(){
			jul_count += parseInt($(this).text());
		}),
		//
		$('.aug_count').each(function(){
			aug_count += parseInt($(this).text());
		}),
		//
		$('.sep_count').each(function(){
			sep_count += parseInt($(this).text());
		}),
		//
		$('.oct_count').each(function(){
			oct_count += parseInt($(this).text());
		}),
		//
		$('.nov_count').each(function(){
			nov_count += parseInt($(this).text());
		}),
		//
		$('.dec_count').each(function(){
			dec_count += parseInt($(this).text());
		})
	).done(function(){
		//合計
		$('#total_jan').text(jan_count);
		//合計
		$('#total_feb').text(feb_count);
		//合計
		$('#total_mar').text(mar_count);
		//合計
		$('#total_apr').text(apr_count);
		//合計
		$('#total_may').text(may_count);
		//合計
		$('#total_jun').text(jun_count);
		//合計
		$('#total_jul').text(jul_count);
		//合計
		$('#total_aug').text(aug_count);
		//合計
		$('#total_sep').text(sep_count);
		//合計
		$('#total_oct').text(oct_count);
		//合計
		$('#total_nov').text(nov_count);
		//合計
		$('#total_dec').text(dec_count);		
	});
});
</script>

@endsection
