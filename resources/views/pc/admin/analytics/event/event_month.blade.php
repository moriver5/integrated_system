@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>イベント効果</b>
				</div>
                <div class="panel-heading" style="font:normal 12px/120% 'メイリオ',sans-serif;text-align:center;">
					<b><a href="/admin/member/analytics/event/{{ $year }}/{{$prev_month}}">PREV</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/event/{{ $year }}">{{ $year }}</a>年{{ $month }}月&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/admin/member/analytics/event/{{ $year }}/{{$next_month}}">NEXT</a></b>
				</div>
				<div class="panel-heading">
					@if( !empty($db_data) )
						<center>
						<table border="1" align="center" style="width:100%;">
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;" rowspan="2">
									{{ $month }}月
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>無料</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>キャンペーン(有料)</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;" colspan="2">
									<b>合計</b>
								</td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;">
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
									公開数
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>閲覧数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>公開数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>閲覧数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>公開数</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>閲覧数</b>
								</td>
							</tr>
							@foreach($db_data as $day => $lines)
								<tr style="font:12px/120% 'メイリオ',sans-serif;">
									<td style="padding:3px;text-align:center;" class="pay">
										<a href="/admin/member/analytics/event/{{ $year }}/{{ $month }}/{{ $day }}" target="_blank">{{ $day }}日</a>
									</td>
									<td style="padding:3px;text-align:center;" class="credit_count">
										@if( $lines['free_open_count'] > 0 )
										{{ number_format($lines['free_open_count']) }}
										@else
											<font color="gainsboro">0</font>
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="credit_money">
										@if( $lines['free_view_count'] > 0 )
										{{ number_format($lines['free_view_count']) }}
										@else
											<font color="gainsboro">0</font>
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="netbank_count">
										@if( $lines['pt_open_count'] > 0 )
										{{ number_format($lines['pt_open_count']) }}
										@else
											<font color="gainsboro">0</font>
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="netbank_money">
										@if( $lines['pt_view_count'] > 0 )
										{{ number_format($lines['pt_view_count']) }}
										@else
											<font color="gainsboro">0</font>
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="hand_count">
										@if( $lines['open_total'] > 0 )
										{{ number_format($lines['open_total']) }}
										@else
											<font color="gainsboro">0</font>
										@endif
									</td>
									<td style="padding:3px;text-align:center;" class="hand_money">
										@if( $lines['view_total'] > 0 )
										{{ number_format($lines['view_total']) }}
										@else
											<font color="gainsboro">0</font>
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
									{{ $total_data['free_open_total'] }}
								</td>
								<td style="padding:3px;text-align:center;" id="credit_money">
									{{ $total_data['free_view_total'] }}
								</td>
								<td style="padding:3px;text-align:center;" id="netbank_count">
									{{ $total_data['pt_open_total'] }}
								</td>
								<td style="padding:3px;text-align:center;" id="netbank_money">
									{{ $total_data['pt_view_total'] }}
								</td>
								<td style="padding:3px;text-align:center;" id="hand_count">
									{{ $total_data['open_total'] }}
								</td>
								<td style="padding:3px;text-align:center;" id="hand_money">
									{{ $total_data['view_total'] }}
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

});
</script>

@endsection
