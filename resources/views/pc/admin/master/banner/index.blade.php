@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>バナー設定</b>
				</div>
				<div class="panel-body">
                    <form id="formUpdate" class="form-horizontal" method="POST" action="/admin/member/master/banner/update/send">
						{{ csrf_field() }}
						<span class="admin_default" style="margin-left:10px;">
							全件数：{{$total }} 件
							({{$currentPage}} / {{$lastPage}}㌻)
						</span>
						<center>{{ $links }}</center>
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:30px;">ID</td>
											<td style="padding:1px 0px;width:450px;">表示内容</td>
											<td style="padding:1px 0px;width:300px;">プレビュー</td>
											<td style="padding:1px 0px;width:40px;">
												表示<br /><input type="checkbox" id="disp_all" name="disp_all" value="1">
											</td>
											<td style="padding:1px 0px;width:45px;">
												削除<br /><input type="checkbox" id="del_all" name="del_all" value="1">
											</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="del slt_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td>{{ $lines->id }}<input type="hidden" class="del" name="id[]" value="{{ $lines->id }}"></td>
											<td><textarea class="del banner_data" id="key{{ $lines->id }}" style="width:100%;height:100%;" cols="75" rows="4" name="banner[]">{{ $lines->banner }}</textarea></td>
											<td style="padding:5px;">{!! $lines->banner !!}</td>
											<td>
												@if( $lines->disp_flg )
													<input type="checkbox" class="disp form-control" id="disp_flg{{ $lines->id }}" name="disp_flg[]" value="{{ $lines->id }}" checked>
												@else
													<input type="checkbox" class="disp form-control" id="disp_flg{{ $lines->id }}" name="disp_flg[]" value="{{ $lines->id }}">
												@endif
											</td>
											<td style="text-align:center;"><input type="checkbox" class="del del_group form-control" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
										</tr>
										@endforeach
									</table>
								</div>
								<button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button type="submit" id="add_key" class="btn btn-primary">バナー追加</button>
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

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
	//キー追加ボタンを押下
	$('#add_key').on('click', function(){
		sub_win = window.open('/admin/member/master/banner/create', 'convert_table', 'width=700, height=350');
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#disp_all').on('change', function() {
		$('.disp').prop('checked', this.checked);
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});
	
	//更新ボタン押下
	$('#push_update').on('click', function() {
		//変換キーに未入力があるか確認
		$('.banner_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});
	});

});
</script>

@endsection
