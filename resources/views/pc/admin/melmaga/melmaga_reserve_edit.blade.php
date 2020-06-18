@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>予約配信のメルマガ編集&nbsp;&nbsp;&nbsp;&nbsp;</b>
				</div>
                <div class="panel-body">
                    <form id="formMelmagaEdit" class="form-horizontal" method="POST" action="/admin/member/melmaga/reserve/status/edit/{{ $melmaga_id }}/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="3"><b>テンプレート</b></td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信者名</b>
											</td>
											<td>
												<input type="text" name="from_name" class="form-control" value="{{ config('const.html_title') }}" maxlength="{{ config('const.from_name_length') }}" placeholder="送信者名">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信元メールアドレス</b>
											</td>
											<td>
												<input type="text" name="from_mail" class="form-control" value="{{ config("const.mail_from") }}" maxlength="{{ config('const.email_length') }}" placeholder="送信元メールアドレス">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信予定時刻</b>
											</td>
											<td>
												<input type="text" id="reserve_date" name="reserve_date" class="form-control" value="{{ $db_data->reserve_send_date }}" placeholder="">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>件名</b>
											</td>
											<td>
												<input type="text" name="subject" class="form-control" value="{{ $db_data->subject }}" maxlength="{{ config('const.subject_length') }}" placeholder="件名">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td colspan="2">
												<textarea cols="60" rows="10" id="text_body" name="text_body" class="form-control" placeholder="メルマガの内容">{{ $db_data->text_body }}</textarea>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td colspan="2">
												<textarea cols="60" rows="10" id="html_body" id="html_body" name="html_body" class="form-control" placeholder="メルマガの内容(HTML)">{{ $db_data->html_body }}</textarea>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>ニュースページに表示する</b>
											</td>
											<td>
												<input type="checkbox" name="newspage_flg" class="form-control" value="1" checked>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>履歴&確認アドレスへ送信</b>
											</td>
											<td>
												@if( $db_data->send_status == 4 )
												<input type="checkbox" name="history_flg" class="form-control" value="1">
												@else
												<input type="checkbox" name="history_flg" class="form-control" value="1" checked>												
												@endif
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">メルマガ更新</button>
								<button type="submit" id="convert_table" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;変換表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button type="submit" id="html_convert_table" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;変換表(HTML用)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button type="submit" id="convert_emoji" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;絵文字表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button type="submit" id="html_convert_emoji" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;絵文字表(HTML用)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
							</div>
						</center>
					</form>
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
	$.datetimepicker.setLocale('ja');

	//送信予定時刻
	$('#reserve_date').datetimepicker();

	//カーソルがフォーカスされたら日付を消す	
	$('[id^=reserve_date').focus(function(){
		$("#reserve_date").val('');
	});

	//変換表ボタン押下
	$('#convert_table').on('click', function(){
		var id = $('[name="tab"]').val();
		sub_win = window.open('/admin/member/page/convert?mode=text', 'convert_table', 'width=1000, height=300');
		return false;
	});

	//変換表ボタン押下
	$('#html_convert_table').on('click', function(){
		var id = $('[name="tab"]').val();
		sub_win = window.open('/admin/member/page/convert', 'convert_table', 'width=1000, height=300');
		return false;
	});

	//絵文字表ボタン押下
	$('#convert_emoji').on('click', function(){
		sub_win = window.open('/admin/member/melmaga/emoji/convert', 'convert_emoji', 'width=1000, height=300');
		return false;
	});

	//絵文字表(HTML)ボタン押下
	$('#html_convert_emoji').on('click', function(){
		sub_win = window.open('/admin/member/melmaga/emoji/convert/html', 'convert_table', 'width=1000, height=300');
		return false;
	});

	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMelmagaEdit', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection
