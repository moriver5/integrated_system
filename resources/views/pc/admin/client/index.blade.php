@extends('layouts.app')

@section('content')
<br />
<div class="container" style="font-size:12px;width:1800px;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<b>USER LIST</b>
			<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
			<button id="create" type="submit" style="float:right;">新規作成</button>
		</div>
		<form id="formMailDel" class="form-horizontal" method="POST" action="/admin/member/client/del/send">
			{{ csrf_field() }}
		<div class="panel-body">
			<span class="admin_default" style="margin-left:10px;">
				全件数：{{$total }} 件
				({{$currentPage}} / {{$lastPage}}㌻)
			</span>
			<center>{{ $db_data->links() }}</center>
			<table border="1" align="center" style="width:98%;">
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>顧客ID</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>広告ｺｰﾄﾞ</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>ﾛｸﾞｲﾝID</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>E-mail</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>PC-mail</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>ｸﾞﾙｰﾌﾟ</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>登録状態</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>POINT</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>ｱｸｼｮﾝ回数</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>仮登録日時</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>登録日時</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>最終ｱｸｾｽ</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>入金回数</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>停止</b><br />
						<input type="checkbox" id="soft_del_all" name="soft_del_all" value="1">
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>削除</b><br />
						<input type="checkbox" id="del_all" name="del_all" value="1">
					</td>
				</tr>
				@foreach($db_data as $lines)
					<tr style="background:@php if( $lines->disable == 1 ){echo 'darkgray';}@endphp;">
						<td style="padding:5px;text-align:center;">
							<a href="{{ url('/admin/member/client/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->id }}</a>
							<input type="hidden" name="id[]" value="{{ $lines->id }}">
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->ad_cd }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->login_id }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->mobile_mail_address }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->mail_address }}
						</td>
						<td style="padding:5px;text-align:center;">
							@if( !empty($db_group_data[$lines->group_id]) )
							{{ $db_group_data[$lines->group_id] }}
							@else
							<b><font color="red">未設定</font></b>
							@endif
						</td>
						<td style="padding:5px;text-align:center;">
							{{ config('const.disp_regist_status')[$lines->status] }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->point }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->action }}回
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->temporary_datetime }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->created_at }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->last_access_datetime }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->pay_count }}
						</td>
						<td style="padding:5px;text-align:center;">
							@if( $lines->disable == 1 )
							<input type="checkbox" class="soft_del soft_del_group" name="soft_del[]" value="{{ $lines->id }}" id="soft_del_group{{ $lines->id }}" checked>
							@else
							<input type="checkbox" class="soft_del soft_del_group" name="soft_del[]" value="{{ $lines->id }}" id="soft_del_group{{ $lines->id }}">
							@endif
						</td>
						<td style="padding:5px;text-align:center;">
							<input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}">
						</td>
					</tr>
				@endforeach
			</table>
			<br />
			<center><button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;一括削除&nbsp;&nbsp;&nbsp;</button></center>
		</div>
		</form>
	</div>
	
</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/client/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_type" value="">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="group_id" value="">
	<input type="hidden" name="reg_status" value="">
	<input type="hidden" name="dm_status" value="">
	<input type="hidden" name="start_regdate" value="">
	<input type="hidden" name="end_regdate" value="">
	<input type="hidden" name="start_provdate" value="">
	<input type="hidden" name="end_provdate" value="">
	<input type="hidden" name="start_lastdate" value="">
	<input type="hidden" name="end_lastdate" value="">
	<input type="hidden" name="start_paydate" value="">
	<input type="hidden" name="end_paydate" value="">
	<input type="hidden" name="start_paynum" value="">
	<input type="hidden" name="end_paynum" value="">
	<input type="hidden" name="start_payamount" value="">
	<input type="hidden" name="end_payamount" value="">
	<input type="hidden" name="start_actnum" value="">
	<input type="hidden" name="end_actnum" value="">
	<input type="hidden" name="start_pt" value="">
	<input type="hidden" name="end_pt" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="sort" value="">
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

	$('#soft_del_all').on('change', function() {
		$('.soft_del').prop('checked', this.checked);
	});

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

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMailDel', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_del_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection
