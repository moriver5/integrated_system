@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col" style="width:1300px;">
        <div class="col-md-13 col-md-offset">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>商品設定</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
					<button id="create" type="submit" style="float:right;">新規作成</button>
				</div>

				<form id="formBulkUpdate" class="form-horizontal" method="POST" action="/admin/member/page/product/update/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span style="margin-left:10px;color:black;font:normal 13px/130% 'メイリオ',sans-serif;">
						全件数：{{$db_data->total()}} 件
						({{$db_data->currentPage()}} / {{$db_data->lastPage()}}㌻)
					</span>
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								<b>ID</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:35px;">
								<b>表示順</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:25px;">
								<b>公開</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:32px;">
								<b>金額</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:45px;">
								<b>ポイント</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:200px;">
								<b>予想師</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:350px;">
								<b>商品名</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:110px;">
								<b>公開開始日時</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:110px;">
								<b>公開終了日時</b>
							</td>
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:25px;">
								<b>削除</b><br />
								<input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
<!--
							<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:25px;">
								<b>Preview</b>
							</td>
-->
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<a href="{{ url('/admin/member/page/product/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}" target="_blank">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<select id="order" name="order[]" style="padding:1px;width:100%;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
											@foreach($page_order as $order)
												@if( $order == $lines->order_num )
													<option value='{{$order}}' selected>{{$order}}</option>
												@else
													<option value='{{$order}}'>{{$order}}</option>										
												@endif
											@endforeach
										</select>
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										@if( $lines->open_flg )
											<input id="open_flg" type="checkbox" name="open_flg[]" value="{{ $lines->id }}" checked>								
										@else
											<input id="open_flg" type="checkbox" name="open_flg[]" value="{{ $lines->id }}">										
										@endif
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<input id="money" type="text" name="money[]" value="{{ $lines->money }}" size="5"style="width:96%;">
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<input id="point" type="text" name="point[]" value="{{ $lines->point }}" size="5" style="width:96%;">
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<a href="{{ url('/admin/member/master/tipster/setting/edit/') }}/{{$lines->tipster_id}}" target="_blank">{{ $lines->name }}</a>
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<a href="{{ url('/admin/member/page/product/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}" target="_blank">{{ $lines->title }}</a>
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										@if( !empty($lines->start_date) )
											<input id="start_date{{$lines->id}}" class="start_date" type="text" style="width:100%;height:100%;" name="start_date[]" value="{{ $lines->start_date }}" placeholder="必須入力">
										@else
											<input id="start_date{{$lines->id}}" class="start_date" type="text" style="width:100%;height:100%;" name="start_date[]" placeholder="必須入力">
										@endif
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										@if( !empty($lines->end_date) )
											<input id="end_date{{$lines->id}}" class="end_date" type="text" style="width:100%;height:100%;" name="end_date[]" value="{{ $lines->end_date }}" placeholder="必須入力">
										@else
											<input id="end_date{{$lines->id}}" class="end_date" type="text" style="width:100%;height:100%;" name="end_date[]" placeholder="必須入力">
										@endif
									</td>
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<input id="del_flg" class="del del_group" type="checkbox" name="del_flg[]" value="{{ $lines->id }}">
									</td>
<!--
									<td style="padding:1px;text-align:center;font:normal 11px/110% 'メイリオ',sans-serif;">
										<a href="{{ $preview_url }}{{ $lines->id }}/{{ $lines->type }}" target="_blank">PC/SP</a>
									</td>
-->
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

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/page/product/search">
	{{ csrf_field() }}
	<input type="hidden" name="title" value="">
	<input type="hidden" name="search_disp_num" value="">
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

	$('.end_date').each(function(){
		$('#'+this.id).datetimepicker();
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/page/product/search/setting', 'product_search', 'width=605, height=250');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/page/product/create/{{$db_data->currentPage()}}', 'create', 'width=1050, height=580');
		return false;
	});
	
	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formBulkUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection
