@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>登録後送信メール</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
					<button id="create" type="submit" style="float:right;">新規作成</button>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
                    <form id="formBulkUpdate" class="form-horizontal" method="POST" action="/admin/member/melmaga/registered/mail/delete/send">
						{{ csrf_field() }}
					<center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:30px;">
								<b>ID</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>指定時間</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>有効/無効</b>
							</td>
							<td class="admin_table" style="width:240px;">
								<b>タイトル</b>
							</td>
							<td class="admin_table" style="width:70px;">
								<b>備考</b>
							</td>
							<td class="admin_table" style="width:15px;">
								削除<br /><input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr class="del slt_group" id="slt_group{{ $lines->id }}">
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/melmaga/registered/mail/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->specified_time }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ config('const.registered_disp_enable_disable')[$lines->enable_flg] }}
									</td>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/melmaga/registered/mail/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->title }}</a>
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->remarks }}
									</td>
									<td style="padding:2px;text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
								</tr>
							@endforeach
						@endif
					</table>
					<br />
					<button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;</button>
					</center>
					</form>
				</div>
			</div>
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/melmaga/registered/mail/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="specified_time" value="">
	<input type="hidden" name="enable_flg" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	$.datetimepicker.setLocale('ja');

	$('.start_date').each(function(){
		$('#'+this.id).datetimepicker();
	});

	//カーソルがフォーカスされたら日付を消す	
	$('[id^=start_date').focus(function(){
		$("#"+this.id).val('');
	});

	$('.end_date').each(function(){
		$('#'+this.id).datetimepicker();
	});

	//カーソルがフォーカスされたら日付を消す	
	$('[id^=end_date').focus(function(){
		$("#"+this.id).val('');
	});

	//削除選択にチェックしたセルの色を変更
	$('.del_group').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","white");
		}
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
		//チェックされたらセルの色を変更
		if( $(this).is(':checked') ){
			$('.del').css("background-color","#F4FA58");
		//チェックが外されたらセルの色を元に戻す
		}else{
			$('.del').css("background-color","white");			
		}
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/melmaga/registered/mail/search/setting', 'convert_table', 'width=640, height=315');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/melmaga/registered/mail/create', 'create', 'width=640, height=680');
		return false;
	});
	
	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formBulkUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection
