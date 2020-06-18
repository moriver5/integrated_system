@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:wheat;"><b>お知らせ設定</b></div>
				<div class="panel-body">
                    <form id="formPopupInfo" class="form-horizontal" method="POST" action="/admin/member/master/info/delete/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									{{ $db_data->links() }}
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:30px;">ID</td>
											<td style="padding:1px 0px;width:35px;">表示順</td>
											<td style="padding:1px 0px;width:250px;">内容</td>
											<td style="padding:1px 0px;width:40px;">表示時間</td>
											<td style="padding:1px 0px;width:30px;">表示</td>
											<td style="padding:1px 2px;width:35px;">
												削除 <input type="checkbox" id="del_all" name="del_all" value="1">
											</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="slt_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td><a href="/admin/member/master/info/edit/{{ $lines->id }}" target="_blank">{{ $lines->id }}</a><input type="hidden" name="id[]" value="{{ $lines->id }}"></td>
											<td>{{ $lines->order }}</td>
											<td><a href="/admin/member/master/info/edit/{{ $lines->id }}" target="_blank">{{ mb_substr($lines->userinfo,0,40) }}・・</a></td>
											<td>
												@if( $lines->disp_flg )
													{{ $lines->disptime }}秒
												@else
													－
												@endif
											</td>
											<td>
												@if( $lines->disp_flg )
													〇
												@else
													－
												@endif
											</td>
											<td style="text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
										</tr>
										@endforeach
									</table>
								</div>
								<button type="submit" id="push_update" class="btn btn-primary">一括削除</button>
								<a href="/admin/member/master/info/create" class="btn btn-primary" target="_blank">新規作成</a>
							</div>
						</center>
					</form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formPopupInfo', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_setting_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection
