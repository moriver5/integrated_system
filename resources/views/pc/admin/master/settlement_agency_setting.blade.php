@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-5 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>決済会社設定</b>
				</div>
				<div class="panel-body">
                    <form id="formGroup" class="form-horizontal" method="POST" action="/admin/member/master/settlement/agency/setting/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:20px;">ID</td>
											<td style="padding:1px 0px;width:50px;">名前</td>
											<td style="padding:1px 2px;width:25px;">選択</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="slt_group del_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td>{{ $lines->id }}<input type="hidden" name="id[]" value="{{ $lines->id }}"></td>
											<td><input type="text" id="name{{ $lines->id }}" name="user_name[]" value="{{ $lines->name }}" maxlength="{{ config('const.from_name_length') }}" class="form-control del_group"></td>
											@if( $lines->active == 1 )
											<td><input type="radio" class="del del_group" name="active_id" value="{{ $lines->id }}" id="del_group{{ $lines->id }}" checked></td>
											@else
											<td><input type="radio" class="del del_group" name="active_id" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
											@endif
										</tr>
										@endforeach
									</table>
								</div>
								<button type="submit" id="push_update" class="btn btn-primary">更新</button>
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
var sub_win;
$(document).ready(function(){

	$('#push_update').on('click', function() {
		var err_flg = false;
		$('[id^=name]').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				err_flg = true;
				return false;
			}
		});

		if( err_flg ){
			swal('未入力があります');
			return false;
		}

		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formGroup', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});

});
</script>

@endsection
