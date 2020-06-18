@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-1">

			<form id="formGroupMove" class="form-horizontal" method="POST" action="/admin/member/group/move/bulk/send">
            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_group_data) )
						{{ csrf_field() }}
						<center>
							<div style="margin:10px;"><b>グループ一括移行</b></div>
						<table border="1" align="center" style="width:100%;font-size:11px;">
							<tr>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:4%;">
									<b>選択</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>グループ名</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>説明</b>
								</td>
							</tr>
							@foreach($db_group_data as $lines)
								<tr id="group{{ $lines->id }}">
									<td style="text-align:center;">
										<input type="checkbox" name="move_group_id[]" value="{{ $lines->id }}" id="move_group_id{{ $lines->id }}" class="move_group_id">
									</td>
									<td style="text-align:center;">
										{{ $lines->id }}
									</td>
									<td style="text-align:center;">
										{{ $lines->name }}
									</td>
									<td style="text-align:center;">
										{{ $lines->memo }}
									</td>
								</tr>
							@endforeach
						</table>
						</center>
					@endif
                </div>
            </div>
				<center><div class="arrow" style="height:115px;"></div><b>移行先グループ選択</b></center>
            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_group_data) )
						{{ csrf_field() }}
						<center>
						<button id="move_btn" type="submit" class="btn btn-primary" style="margin-bottom:10px;">
							グループ一括移行
						</button>
						<table border="1" align="center" style="width:100%;font-size:11px;">
							<tr>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:4%;">
									<b>選択</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>グループ名</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>説明</b>
								</td>
							</tr>
							@foreach($db_group_data as $lines)
								<tr class="slt_group" id="slt_group{{ $lines->id }}">
									<td style="text-align:center;">
										<input type="radio" name="group_id" value="{{ $lines->id }}" id="slt_group_id{{ $lines->id }}" class="slt_group_id">
									</td>
									<td style="text-align:center;">
										{{ $lines->id }}
									</td>
									<td style="text-align:center;">
										{{ $lines->name }}
									</td>
									<td style="text-align:center;">
										{{ $lines->memo }}
									</td>
								</tr>
							@endforeach
						</table>
						</center>
					@endif
                </div>
            </div>
			</form>		
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//移行元グループでチェックしたセルの色を変更
	$('.move_group_id').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#group" + this.id.replace(/move_group_id/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#group" + this.id.replace(/move_group_id/,"")).css("background-color","white");
		}
	});

	//移行先グループ選択でチェックしたセルの色を変更
	$('.slt_group_id').on('click', function(){
		//最初にチェック済の背景色を消す
		$(".slt_group").css("background-color","");			

		
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/slt_group_id/,"")).css("background-color","#F4FA58");
		}
	});
	
	//グループ移行ボタン押下後のダイアログ確認メッセージ
	$('#move_btn').on('click', function(){
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formGroupMove', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_move_group_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});

});
</script>

@endsection
