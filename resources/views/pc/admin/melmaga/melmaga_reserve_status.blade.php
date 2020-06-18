@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>メルマガ予約状況</b>
				</div>
				<div class="panel-body">
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:40px;">
								<b>ID</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>配信状況</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:40px;">
								<b>配信数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:120px;">
								<b>配信予定日時</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:120px;">
								<b>予約日時</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								
							</td>
						</tr>
						@foreach($db_data as $lines)
							<tr>
								<td style="padding:2px;text-align:center;">
									<a href="{{ url('/admin/member/melmaga/mail/history/view/') }}/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->id }}</a>
								</td>
								<td style="padding:2px;text-align:center;">
									@if( preg_match("/[04]/",$lines->send_status) )
									<b><font color="red">配信待ち</font></b>
									@elseif( $lines->send_status == 1 )
									<b><font color="blue">配信中</font></b>
									@elseif( preg_match("/[25]/",$lines->send_status) )
										<font color="gray">配信済</font>
									@elseif( $lines->send_status == 3 )
										<font color="gray">キャンセル</font>
									@endif
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->send_count }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->reserve_send_date }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->created_at }}
								</td>
								<td style="padding:2px;text-align:center;">
									@if( preg_match("/[04]/",$lines->send_status) )
									<a href="{{ url('/admin/member/melmaga/reserve/status/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}" target="_blank">編集</a>
									@else
									<font color="gray">編集</font>
									@endif
								</td>
								<td style="padding:1px;width:39px;text-align:center;">
									@if( preg_match("/[04]/",$lines->send_status) )
									<form id="formCancel{{ $lines->id }}" class="form-horizontal" method="POST" action="/admin/member/melmaga/reserve/status/cancel/{{ $db_data->currentPage() }}/{{ $lines->id }}">
										{{ csrf_field() }}
										<button id="{{ $lines->id }}" class="cancel_btn" type="submit">ｷｬﾝｾﾙ</button>
									</form>
									@else
									--
									@endif
								</td>
							</tr>
						@endforeach
					</table>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/search/setting', 'convert_table', 'width=700, height=655');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/client/create', 'create', 'width=1000, height=655');
		return false;
	});

	//新規作成ボタンを押下
	$('.cancel_btn').click(function(){
		var form_id = 'formCancel' + this.id;
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert(form_id, 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.cancel_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $cancel_redirect_url }}?page={{ $db_data->currentPage() }}');
	});
});
</script>

@endsection
