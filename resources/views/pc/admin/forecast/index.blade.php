@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-13 col-md-offset">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>予想一覧</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
					<button id="create" type="submit" style="float:right;">新規作成</button>
				</div>

				<form id="formBulkUpdate" class="form-horizontal" method="POST" action="/admin/member/forecast/update/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:40px;">
								<b>予想ID</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>公開</b>
							</td>
							<td class="admin_table" style="width:35px;">
								<b>カテゴリ</b>
							</td>
							<td class="admin_table" style="width:50px;">
								<b>閲覧数</b>
							</td>
							<td class="admin_table" style="width:230px;">
								<b>タイトル</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>表示日時</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>公開日時</b>
							</td>
							<td class="admin_table" style="width:25px;">
								<b>削除</b><br />
								<input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/forecast/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;">
										@if( $lines->open_flg == 1 )
											〇
										@endif
									</td>
									<td style="padding:2px;text-align:center;">
										@foreach($forecast_category as $category)
											@if( $category[0] == $lines->category )
												{{ $category[1] }}
											@endif
										@endforeach
									</td>
									<td style="padding:2px;text-align:center;">
										@if( $lines->visitor > 0 )
											<a href="{{ url('/admin/member/forecast/access') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->visitor }}</a>
										@else
											0
										@endif
									</td>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/forecast/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->title }}</a>
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->disp_sdate }}~{{ $lines->disp_edate }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->open_sdate }}~{{ $lines->open_edate }}
									</td>
									<td style="padding:2px;text-align:center;">
										<input id="del_flg" class="del del_group" type="checkbox" name="del_flg[]" value="{{ $lines->id }}">
									</td>
								</tr>
							@endforeach
						@endif
					</table>
					<br />
					<div class="form-group">
						<div class="col-md-6 col-md-offset-5">
							<button id="push_btn" type="submit" class="btn btn-primary">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</button>
						</div>
					</div>
				</div>
				</form>
			</div>	
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/forecast/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="category" value="">
	<input type="hidden" name="groups" value="">
	<input type="hidden" name="campaigns" value="">
	<input type="hidden" name="disp_sdate" value="">
	<input type="hidden" name="disp_edate" value="">
	<input type="hidden" name="open_sdate" value="">
	<input type="hidden" name="open_edate" value="">
	<input type="hidden" name="disp_type" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="sort" value="">
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

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/forecast/search/setting', 'convert_table', 'width=805, height=900');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/forecast/create', 'create', 'width=1000, height=735');
		return false;
	});
	
	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formBulkUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection
