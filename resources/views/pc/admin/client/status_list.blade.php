@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-13 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>顧客ステータス変更の一覧</b>
				</div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_data) )
						{{ csrf_field() }}
						<center>
						{{ $db_data->links() }}
						<table border="1" align="center">
							<tr>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>顧客ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>広告ｺｰﾄﾞ</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ﾛｸﾞｲﾝID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>E-mail</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>PC-mail</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ｸﾞﾙｰﾌﾟ</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>登録状態</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>POINT</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>仮登録日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>登録日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>最終ｱｸｾｽ</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>入金回数</b>
								</td>
							</tr>
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:5px;text-align:center;">
			<!--							<a href="{{ url('/admin/member/client/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->id }}</a>-->
										{{ $lines->id }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->ad_cd }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->login_id }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->mobile_mail_address }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->mail_address }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->group_id }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ config('const.disp_regist_status')[$lines->status] }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->point }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->temporary_datetime }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->created_at }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->last_access_datetime }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->pay_count }}
									</td>

								</tr>
							@endforeach
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

	//更新ボタン押下時に更新用パラメータにデータ設定
	$('#push_btn').on('click', function(){

		//条件検索ボタン押下
		$('#formStatusSearch').submit(function(event){
			//ajax通信(アカウント編集処理)
			$.ajax({
				url: $(this).prop('action'),
				type: method,
				data: $(this).serialize(),
				timeout: timeout,
				success:function(result_flg){
					window.location.reload();
				},
				error: function(error) {

				}
			});
			
			return false;
		});

	});

});
</script>

@endsection
