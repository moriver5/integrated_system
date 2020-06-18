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
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<br />
<br />
<br />

<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset" style="width:99%;">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>ポイント設定追加</b>
					<span class="category_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
                    <form id="formPointAdd" class="form-horizontal" method="POST" action="/admin/member/master/purchase/point/category/setting/detail/{{ $id }}/add/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>金額</td>
											<td>ポイント</td>
											<td>表示テキスト</td>
											<td>備考</td>
										</tr>
										<tr style="text-align:center;">
											<td><input type="text" class="form-control" name="money" value="" size="10" placeholder="例：10000"></td>
											<td><input type="text" class="form-control" name="point" value="" size="10" placeholder="例：200"></td>
											<td><input type="text" class="form-control" name="disp_msg" value="" size="20" maxlength="{{ config('const.pt_setting_text_max_length') }}" placeholder="例：(ポイント【２倍】サービス中)"></td>
											<td><input type="text" class="form-control" name="remarks" value="" size="20" maxlength="{{ config('const.pt_setting_remarks_max_length') }}"></td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;追加&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('[name=money]').focusin(function(){
		$('[name=money]').attr("placeholder","");
	});

	$('[name=money]').focusout(function(){
		$('[name=money]').attr("placeholder","例：10000");
	});

	$('[name=point]').focusin(function(){
		$('[name=point]').attr("placeholder","");
	});

	$('[name=point]').focusout(function(){
		$('[name=point]').attr("placeholder","例：200");
	});

	$('[name=disp_msg]').focusin(function(){
		$('[name=disp_msg]').attr("placeholder","");
	});

	$('[name=disp_msg]').focusout(function(){
		$('[name=disp_msg]').attr("placeholder","例：(ポイント【２倍】サービス中)");
	});
	
	//閉じるをクリック
	$('.category_windows_close').on('click', function(){
		window.close();
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formPointAdd', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

</body>
</html>
