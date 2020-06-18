@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>ご利用者の声</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
					<button id="create" type="submit" style="float:right;">新規作成</button>
				</div>

				<form id="formBulkUpdate" class="form-horizontal" method="POST" action="/admin/member/page/voice/update/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span style="margin-left:10px;color:black;font:normal 13px/130% 'メイリオ',sans-serif;">
						全件数：{{$db_data->total()}} 件
						({{$db_data->currentPage()}} / {{$db_data->lastPage()}}㌻)
					</span>
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:20px;">
								<b>ID</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:15px;">
								<b>公開</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>画像</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:80px;">
								<b>タイトル</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								<b>投稿者</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:20px;">
								<b>投稿日時</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:15px;">
								<b>削除</b><br />
								<input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<a href="{{ url('/admin/member/page/voice/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}" target="_blank">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										@if( $lines->open_flg )
											<input id="open_flg" type="checkbox" name="open_flg[]" value="{{ $lines->id }}" checked>								
										@else
											<input id="open_flg" type="checkbox" name="open_flg[]" value="{{ $lines->id }}">										
										@endif
									</td>
									<td style="padding:2px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										@if( !empty($lines->img) )
											<img src="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}/{{ config('const.voice_images_path') }}/{{ $lines->img }}?ver={{$ver}}" height="100px" width="100px">
										@endif
									</td>
									<td style="padding:2px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<a href="{{ url('/admin/member/page/voice/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}" target="_blank">
											@if( empty($lines->title) )
												{{ config('const.none_post_title') }}
											@else
												{{ $lines->title }}
											@endif
										</a>
									</td>
									<td style="padding:2px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										@if( empty($lines->name) )
											{{ config('const.none_post_name') }}
										@else
											{{ $lines->name }}
										@endif
									</td>
									<td style="padding:2px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										{{ $lines->post_date }}
									</td>
									<td style="padding:2px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
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
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</button>
						</div>
					</div>
				</div>
				</form>
			</div>	
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/page/voice/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="start_voice_date" value="">
	<input type="hidden" name="end_voice_date" value="">
	<input type="hidden" name="venue" value="">
	<input type="hidden" name="page_disp_type" value="">
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
		search_win = window.open('/admin/member/page/voice/search/setting', 'voice', 'width=605, height=360');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/page/voice/create', 'create', 'width=500, height=760');
		return false;
	});
	
	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formBulkUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection
