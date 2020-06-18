@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-5 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>確認アドレス設定</b>
				</div>
				<div class="panel-body">
                    <form id="formGroup" class="form-horizontal" method="POST" action="/admin/member/master/confirm/email/setting/del/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									{{ $db_data->links() }}
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:20px;">ID</td>
											<td style="padding:1px 0px;width:50px;">名前</td>
											<td style="padding:1px 0px;width:100px;">送信アドレス</td>
											<td style="padding:1px 2px;width:25px;">削除 <input type="checkbox" id="del_all" name="del_all" value="1"></td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="slt_group del_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td>{{ $lines->id }}<input type="hidden" name="id[]" value="{{ $lines->id }}"></td>
											<td><input type="text" id="name{{ $lines->id }}" name="user_name[]" value="{{ $lines->name }}" maxlength="{{ config('const.from_name_length') }}" class="form-control del_group"></td>
											<td><input type="text" id="email{{ $lines->id }}" name="email[]" value="{{ $lines->email }}" maxlength={{ config('const.email_length') }} class="form-control group_data del_group"></td>
											<td><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
										</tr>
										@endforeach
									</table>
								</div>
								<button type="submit" id="push_update" class="btn btn-primary">更新</button>
								<button type="submit" id="add_address" class="btn btn-primary">アドレス追加</button>
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
	//グループ管理で削除選択にチェックしたセルの色を変更
	$('.del_group').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#name" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#email" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#name" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#email" + this.id.replace(/del_group/,"")).css("background-color","white");
		}
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formGroup', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
	//グループ追加ボタンを押下
	$('#add_address').on('click', function(){
		sub_win = window.open('/admin/member/master/confirm/email/setting/add', 'confirm_email_add', 'width=700, height=350');
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
		//セルの色を変更
		if( $(this).is(':checked') ){
			$(".del_group").css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$(".del_group").css("background-color","white");
		}
	});

	//更新ボタン押下
	$('#push_update').on('click', function() {
		//グループ名に未入力があるか確認
		$('.group_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});
	});
	
	//グループ名のテキストBOXにカーソルが当たったら
	$('.group_data').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});

});
</script>

@endsection
