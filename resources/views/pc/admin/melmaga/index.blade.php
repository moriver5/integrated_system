@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>メルマガ</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">配信先設定</button>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						抽出件数：{{$total }} 件
					</span>
					<table border="1" align="center" width="98%">
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>抽出項目</b>
							</td>
							<td style="width:200px;padding:5px;">
								@if( !empty($items['search_value']) )
								{{ $items['search_item'] }}{{ $items['search_value'] }}{{ $items['search_type'] }}
								@else
									@if( !empty($session) )
									なし
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>グループ</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( !empty($slt_groups) )
								@foreach($slt_groups as $index => $name)
								{{ $name }}<br />
								@endforeach
								@else
									@if( !empty($session) )
									なし
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>登録状態</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( isset($session['melmaga_status']) && $session['melmaga_status'] != '' )
								{{ config('const.regist_status')[$session['melmaga_status']][1] }}
								@else
									@if( !empty($session) )
									なし
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>未決済注文</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( isset($session['melmaga_settlement']) && $session['melmaga_settlement'] != '' )
								{{ config('const.melmaga_settlement_status')[$session['melmaga_settlement']][1] }}
								@else
									@if( !empty($session) )
									関係なく
									@endif
								@endif
							</td>
						</tr>
<!--
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>送信端末</b>
							</td>
							<td style="width:40px;padding:5px;">
							</td>
						</tr>
-->
						<tr>
							<td class="admin_table" style="width:60px;">
								<b>登録日時</b>
							</td>
							<td style="width:40px;padding:5px;">
								@if( !empty($session['melmaga_regist_sdate']) )
									{{ $session['melmaga_regist_sdate'] }} ~ 
								@endif
								@if( !empty($session['melmaga_regist_edate']) )
									{{ $session['melmaga_regist_edate'] }}
								@endif
							</td>
						</tr>
					</table>
				</div>
			</div>	
		</div>

		@if( $total > 0 )
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>即時配信のメルマガ内容&nbsp;&nbsp;&nbsp;&nbsp;</b>
				</div>
				<div style="margin-left:20px;margin-top:10px;font-size:12px;font-weight:bold;">
					※指定フォーマットで記載すると下記に変換されます↓<br>
					&lt;MELMAGA_ID&gt;→メルマガIDへ変換<br>
					&lt;ACCESS_KEY&gt;→ログイン認証キーへ変換<br>
					<br>
					使用例：<br>
					URLの最後に下記を付加すると、どのメルマガIDからのアクセスなのか、<br>ログインしていなくてもログイン後のページが閲覧可能になります<br>
					https://jra-yosou.net/member/setting?mid=&lt;MELMAGA_ID&gt;&ak=&lt;ACCESS_KEY&gt;<br>
					https://jra-yosou.net/member/settlement?mid=&lt;MELMAGA_ID&gt;&ak=&lt;ACCESS_KEY&gt;<br>
					<br>
					midには&lt;MELMAGA_ID&gt;を使用、akには&lt;ACCESS_KEY&gt;を使用してください<br>
					midとak以外は使用できません
				</div>
                <div class="panel-body">
                    <form id="formMelmagaMail" class="form-horizontal" method="POST" action="/admin/member/melmaga/search/mail/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="3"><b>テンプレート</b></td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信者名</b>
											</td>
											<td>
												<input type="text" name="from_name" class="form-control" value="{{ config(Session::get('operation_select_db').'.const.html_title') }}" maxlength="{{ config('const.from_name_length') }}" placeholder="送信者名">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信元メールアドレス</b>
											</td>
											<td>
												<input type="text" name="from_mail" class="form-control" value="{{ config(Session::get('operation_select_db').'.const.mail_from') }}" maxlength="{{ config('const.email_length') }}" placeholder="送信元メールアドレス">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>件名</b>
											</td>
											<td>
												<input type="text" name="subject" class="form-control" value="" maxlength="{{ config('const.subject_length') }}" placeholder="件名">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td colspan="2">
												<textarea cols="60" rows="10" id="text_body" name="body" class="form-control" placeholder="メルマガの内容"></textarea>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td colspan="2">
												<textarea cols="60" rows="10" id="html_body" name="html_body" class="form-control" placeholder="メルマガの内容(HTML)"></textarea>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>ニュースページに表示する</b>
											</td>
											<td>
												<input type="checkbox" name="newspage_flg" class="form-control" value="1" checked>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>履歴&確認アドレスへ送信</b>
											</td>
											<td>
												<input type="checkbox" name="history_flg" class="form-control" value="1" checked>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>リレーサーバーを使用して送信</b><br />
												<spna style="font-size:9px;color:red;">※(リレーサーバーが設定されていない場合は通常のメールサーバーとなります)</span>
											</td>
											<td>
												<input type="checkbox" name="relay_server_flg" class="form-control" value="1" checked>
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">メルマガ送信</button>
								<button type="submit" id="convert_table" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;変換表&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button type="submit" id="html_convert_table" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;変換表(HTML用)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button type="submit" id="html_convert_emoji" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;絵文字表(HTML用)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
							</div>
						</center>
						<input type='hidden' name='id' value=''>
					</form>
                </div>
            </div>
        </div>
		@endif
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/melmaga/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_type" value="">
	<input type="hidden" name="groups" value="">
	<input type="hidden" name="status" value="">
	<input type="hidden" name="career" value="">
	<input type="hidden" name="settlement" value="">
	<input type="hidden" name="device" value="">
	<input type="hidden" name="regist_sdate" value="">
	<input type="hidden" name="regist_edate" value="">
	<input type="hidden" name="provision_sdate" value="">
	<input type="hidden" name="provision_edate" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
var sub_win;
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


	//変換表ボタン押下
	$('#convert_table').on('click', function(){
		sub_win = window.open('/admin/member/page/convert?mode=text', 'convert_table', 'width=1000, height=300');
		return false;
	});

	//変換表(HTML)ボタン押下
	$('#html_convert_table').on('click', function(){
		sub_win = window.open('/admin/member/page/convert', 'convert_table', 'width=1000, height=300');
		return false;
	});

	//絵文字表(HTML)ボタン押下
	$('#html_convert_emoji').on('click', function(){
		sub_win = window.open('/admin/member/melmaga/emoji/convert/html', 'convert_table', 'width=600, height=300');
		return false;
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/melmaga/search/setting', 'convert_table', 'width=805, height=450');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/melmaga/create', 'create', 'width=1000, height=735');
		return false;
	});

	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMelmagaMail', 'post', '{{ __('messages.dialog_alert_send_title') }}{{$total}}件', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_melmaga_wait_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection
