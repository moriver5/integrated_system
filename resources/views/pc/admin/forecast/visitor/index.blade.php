@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">

            <div class="panel panel-default">
                <div class="panel-body">
					<form id="formVisitorSearch" class="form-horizontal" method="POST" action="/admin/member/forecast/visitor/search">
					{{ csrf_field() }}
					<center>
					<table style="width:100%;text-align:left;" border="1">
						<tr>
							<td class="admin_table" style="width:60px;">
								予想ID
							</td>
							<td class="admin_table" style="width:625px;">
								<input id="forecast_id" type="text" class="form-control" name="forecast_id" value="{{ $forecast_id }}" placeholder=", (半角カンマ)区切りで複数検索可">
							</td>
							<td style="text-align:center;float:left;width:100%;padding:2px;">								
								<button id="search_btn" type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;検索&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</button>
							</td>
						</tr>
					</table>
					</center>
					</form>
                </div>
            </div>

        </div>

        <div class="col-md-12 col-md-offset">
			@if( !empty($db_data) )
            <div class="panel panel-default">
                <div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$db_data->total()}} 件
						({{$db_data->currentPage()}} / {{$db_data->lastPage()}}㌻)
					</span>
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="99%" style="font:normal 13px/130% 'メイリオ',sans-serif;">
						<tr>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>顧客ID</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>広告ｺｰﾄﾞ</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>E-mail</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>ｸﾞﾙｰﾌﾟ</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>登録状態</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>電話番号</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>DM</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>POINT</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>登録日時</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>最終ｱｸｾｽ</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
								<b>入金回数</b>
							</td>
						</tr>
						@foreach($db_data as $lines)
							<tr>
								<td style="padding:2px;text-align:center;">
									<a href="{{ url('/admin/member/client/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}?back=1">{{ $lines->id }}</a>
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->ad_cd }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->mail_address }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->group_id }}
								</td>
								<td style="padding:2px;text-align:center;">
									@foreach($list_status as $status)
										@if( $status[0] == $lines->status)
											{{ $status[1] }}
										@endif
									@endforeach
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->credit_certify_phone_no }}
								</td>
								<td style="padding:2px;text-align:center;">
									@if( $lines->mail_status == 1 )
										〇
									@else
										--
									@endif
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->point }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->created_at }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->last_access_datetime }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->pay_count }}
								</td>

							</tr>
						@endforeach
					</table>
                </div>
            </div>
			@endif
        </div>

    </div>
</div>


<script type="text/javascript">
$(document).ready(function(){
	//検索ボタン押下後、予想IDが入力されていなかったらアラート表示
	$('#search_btn').on('click', function(){
		if( $('[name="forecast_id"]').val() == '' ){
			swal({
			  title: 'warning',
			  text: '{{ __('messages.dialog_none_forecast_msg') }}',
			  icon: "warning",
			  buttons: false,
			  dangerMode: true,
			});
			return false;	
		}
	});
});
</script>

@endsection
