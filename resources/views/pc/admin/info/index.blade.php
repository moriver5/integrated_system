@extends('layouts.app')

@section('content')
<br />
<div class="container">
	<div class="col-md-10 col-md-offset-1">
		<form id="formMailDel" class="form-horizontal" method="POST" action="/admin/member/info/delete/send">
			{{ csrf_field() }}
		<div class="panel panel-default" style="font-size:12px;">
			<div class="panel-heading">
				<b>受信メール一覧</b>
				<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
			</div>
			<div class="panel-body">
				<span class="admin_default" style="margin-left:10px;">
					全件数：{{$total }} 件
					({{$currentPage}} / {{$lastPage}}㌻)
				</span>
				<center>{{ $links }}</center>
				<table border="1" align="center" width='98%'>
					<tr>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>受信日時</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>返信日時</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>顧客ID</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>件名</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>差出人</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>グループ</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>登録状態</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>MEMO</b>
						</td>
						<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
							<b>削除</b><br />
							<input type="checkbox" id="del_all" name="del_all" value="1">
						</td>
					</tr>
					@foreach($db_data as $lines)
						<tr>
							<td style="padding:5px;text-align:center;">
								@if( !empty($lines->created_at) )
								{{ $lines->created_at }}
								@else
								-----
								@endif
							</td>
							<td style="padding:5px;text-align:center;">
								@if( !empty($lines->reply_date) )
								{{ $lines->reply_date }}
								@else
								-----
								@endif
							</td>
							<td style="padding:5px;text-align:center;">
								{{ $lines->client_id }}
							</td>
							<td style="padding:5px;text-align:center;">
								<a href="{{ url('/admin/member/info/replay') }}/{{ $db_data->currentPage() }}/{{$lines->id}}" target="_blank">{{ $lines->subject }}</a>
							</td>
							<td style="padding:5px;text-align:center;">
								{{ $lines->email }}
							</td>
							<td style="padding:5px;text-align:center;">
								@if( !empty($lines->group_id) && !empty($groups[$lines->group_id]) )
								{{ $groups[$lines->group_id] }}
								@endif
							</td>
							<td style="padding:5px;text-align:center;">
								{{ $status[$lines->status] }}
							</td>
							<td style="padding:5px;text-align:center;">
								@if( !empty($lines->memo) )
								{{ $lines->memo }}
								@endif
							</td>
							<td style="text-align:center;">
								<input type="hidden" name="id[]" value="{{ $lines->id }}">
								<input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}">
							</td>
						</tr>
					@endforeach
				</table>
				<br />
				<center><button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;</button></center>
			</div>
		</div>
		</form>
	</div>
</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/info/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="start_reply_date" value="">
	<input type="hidden" name="end_reply_date" value="">
	<input type="hidden" name="start_receive_date" value="">
	<input type="hidden" name="end_receive_date" value="">
	<input type="hidden" name="reply_flg" value="">
	<input type="hidden" name="search_disp_num" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/info/search/setting', 'info_search', 'width=720, height=335');
		return false;
	});

	//削除ボタン押下
	$('#push_update').on('click', function() {
		//グループ名に未入力があるか確認
		$('.group_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMailDel', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_del_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection
