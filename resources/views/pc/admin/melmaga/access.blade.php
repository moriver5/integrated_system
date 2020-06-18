@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>アクセス一覧</b>
					<button id="push_mail_btn" type="submit" style="float:right;margin-left:10px;">
						メール配信
					</button>
				</div>

				<div class="panel-body">
					<span style="margin-left:10px;color:black;font:normal 13px/130% 'メイリオ',sans-serif;">
						全件数：{{ $db_data->total() }} 件
						({{ $db_data->currentPage() }} / {{ $db_data->lastPage() }}㌻)
					</span>
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_search" style="padding:3px;">
								<b>顧客ID</b>
							</td>
							<td class="admin_search" style="padding:3px;">
								<b>E-Mail</b>
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td class="admin_td">
										<a href="{{ url('/admin/member/client/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}?back_btn=0" target="_blank">{{ $lines->id }}</a>
									</td>
									<td class="admin_td">
										{{ $lines->mail_address }}
									</td>
								</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>	
		</div>	
	</div>	

</div>

<script type="text/javascript">
$(document).ready(function(){
	//個別メールボタン押下
	$('#push_mail_btn').click(function(){
		send_mail_win = window.open('/admin/member/forecast/access/{{ $page }}/{{ $id }}/mail', 'edit_mail', 'width=600, height=580');
		return false;
	});
});
</script>

@endsection
