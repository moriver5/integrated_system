@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>広告コード一覧</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
					<button id="create" type="submit" style="float:right;">新規作成</button>
				</div>

				<form id="formAdcode" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:30px;">
								<b>ID</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>コード</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>区分</b>
							</td>
							<td class="admin_table" style="width:240px;">
								<b>名称</b>
							</td>
							<td class="admin_table" style="width:70px;">
								<b>URL</b>
							</td>
							<td class="admin_table" style="width:70px;">
								<b>シングルオプト</b>
							</td>
							<td class="admin_table" style="width:40px;">
								削除 <input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/ad/adcode/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->ad_cd }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ config('const.ad_category')[$lines->category] }}
									</td>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/ad/adcode/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->name }}</a>
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->url }}
									</td>
									<td style="text-align:center;">
										@if( !empty($lines->single_opt) )
											〇
										@endif
									</td>
									<td style="text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}"></td>
								</tr>
							@endforeach
						@endif
					</table>
					<br />
					<center><button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;</button></center>
				</div>
				</form>
			</div>
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="category" value="">
	<input type="hidden" name="aggregate_flg" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
		search_win = window.open('/admin/member/ad/adcode/search/setting', 'convert_table', 'width=640, height=370');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/ad/adcode/create', 'create', 'width=540, height=565');
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formAdcode', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection
