@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-11 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>媒体集計</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:10%;">
								<b>広告コード</b>
							</td>
							<td class="admin_table" style="width:10%;">
								<b>広告コード名称</b>
							</td>
							<td class="admin_table" style="width:10%;">
								&nbsp;
							</td>
							<td class="admin_table" style="width:10%;">
								<b>合計</b>
							</td>
							@foreach($list_date as $date)
							<td class="admin_table" style="width:10%;">
								<b>{{ $date }}</b>
							</td>
							@endforeach
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $ad_cd => $lines)
							<tr>
								<td style="padding:2px;text-align:center;" rowspan="8">
									<a href="{{ url('/admin/member/ad/adcode/edit/'.$currentPage.'/'.$list_id[$ad_cd])}}" target="_blank">{{ $ad_cd }}</a>
								</td>
								<td style="padding:2px;text-align:center;" rowspan="8">
									{{ $list_ad[$ad_cd] }}
								</td>
								<td style="padding:2px;text-align:center;">
									アクセス
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['pv'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['pv'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									仮登録者数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['temp_reg'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['temp_reg'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									登録者数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['reg'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['reg'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									退会者数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['quit'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['quit'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									注文数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['order_num'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['order_num'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									購入数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['pay'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['pay'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									購入率
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $line['pay_rate'] }}%
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['pay_rate'] }}%
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									売上金額
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['amount'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['amount'] }}
								</td>
								@endforeach
							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>	
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/media/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="start_date" value="">
	<input type="hidden" name="end_date" value="">
	<input type="hidden" name="category" value="">
	<input type="hidden" name="disp_type" value="">
	<input type="hidden" name="action_flg" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/ad/media/search/setting', 'convert_table', 'width=680, height=380');
		return false;
	});

});
</script>

@endsection
