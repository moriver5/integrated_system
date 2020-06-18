<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('const.html_admin_title') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body>
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>広告コード登録</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">

                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/create/send">
                        {{ csrf_field() }}

                        <div class="form-group">
							<center>
							<table border="1" width="97%">
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">Group</td>
									<td style="width:100px;padding:5px;" colspan="5">
									<select name="group_id" class="form-control">
									@foreach($db_group_data as $lines)
										&nbsp;<option value="{{ $lines->id }}">{{ $lines->name }}</option>
									@endforeach
									</select>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">登録方法</td>
									<td style="width:100px;padding:0px;" colspan=5">
										<div style="margin:12px 8px;;float:left;">シングルオプト</div><input type="checkbox" name="single_opt" value="1" class="form-control" style="float:left;width:50px;">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">広告コード</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="ad_cd" type="text" class="form-control" name="ad_cd" value="" autofocus placeholder="必須入力">
									</td>
									<td class="admin_search" style="width:75px;text-align:center;">代理店ID</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="agency_id" type="text" class="form-control" name="agency_id" value="" autofocus placeholder="">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">区分</td>
									<td style="width:100px;padding:5px;" colspan="3">
									<select name="category" class="form-control">
									@foreach(config('const.ad_category') as $key => $category)
										&nbsp;<option value="{{ $key }}">{{ $category }}</option>
									@endforeach
									</select>
									</td>
									<td class="admin_search" style="width:100px;text-align:center;">集計表示</td>
									<td style="width:100px;padding:5px;" colspan="3">
									<select name="aggregate_flg" class="form-control">
										&nbsp;<option value="1">する</option>
										&nbsp;<option value="0">しない</option>
									</select>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">名称</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<input id="name" type="text" class="form-control" name="ad_name" value="" autofocus placeholder="">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">URL</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<input id="url" type="text" class="form-control" name="url" value="" autofocus placeholder="">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">MEMO</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<textarea cols="90" rows="4" name="description" id="description" class="contents form-control"></textarea>
									</td>
								</tr>
							</table>
							</center>
						</div>

                        <div>
                            <div style="text-align:center;">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;作成する&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		//別ウィンドウを開く
		window.open($('[name="url"]').val(), 'url_preview', 'width=1000, height=600');
		return false;
	});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

	//新規作成ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
		
	});
});
</script>

</body>
</html>

