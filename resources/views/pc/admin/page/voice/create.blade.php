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
        <div class="col-md-6 col-md-offset">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>ご利用者の声 新規作成</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">
                    <form id="formImageUpload" class="form-horizontal" method="POST" action="/admin/member/page/voice/upload/send">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="end_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">画像設定</label>
							<table>
								<tr>
									<td>
										<div class="col-md-9"　id="file_upload_section" style="width:190px;">
											<div id="drop" style="text-align:center;width:100px; height:140px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formImageUpload', 'import_file', '{{csrf_token()}}', '{{ __('messages.dialog_img_upload_end_msg') }}', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id'], 'post', '10000', '{{ $redirect_url }}')">
												<div style="font:italic normal bold 12px/120% 'メイリオ',sans-serif;color:silver;">アップロード画像をここに<br />ドラッグアンドドロップ</div>
												<center><div id="result" style="font:italic normal bold 11px/110% 'メイリオ',sans-serif;margin:0px 10px 0px 10px;width:130px;viertival-align:middle;"></div></center>
											</div>
										</div>
									</td>
									<td style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
										@if( !empty($db_data->img) )
											<b>設定済：{{ $db_data->img }}</b><br />
											<img src="{{ config('const.base_url') }}/{{ config('const.voice_images_path') }}/{{ $db_data->img }}?ver={{$ver}}">
										@else
										<div style="width:190px;text-align:center;"><b>画像未設定</b></div>
										@endif
									</td>
								</tr>
							</table>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
					</form>
					
                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/page/voice/create/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">投稿日時</label>

                            <div class="col-md-3">
								<input id="post_date" type="text" class="form-control" name="post_date">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">タイトル</label>
                            <div class="col-md-2">
								<input id="title" type="text" class="form-control" name="title" value="" maxlength="{{ config('const.voice_title_max_length') }}" autofocus>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">投稿者</label>

                            <div class="col-md-3">
								<input id="writer" type="text" class="form-control" name="writer" maxlength="{{ config('const.voice_writer_max_length') }}">
                            </div>
                        </div>

                        <div class="form-group">
							<label for="type" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">コメント(タグ可能)</label>

							<div class="col-md-5">
								<textarea cols="70" rows="4" name="comment" id="html_body" class="contents form-control" maxlength="{{ config('const.voice_comment_max_length') }}"></textarea>
                            </div>
						</div>

                        <div class="form-group">
                            <label for="open_flg" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開</label>
                            <div class="col-md-2">
								<select id="open_flg" class="form-control" name="open_flg">
									@foreach($list_open_flg as $lines)
										<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div>
                            <div style="text-align:center;">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;新規作成&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
                            </div>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
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

	$.datetimepicker.setLocale('ja');

	//投稿日時
	$('#post_date').datetimepicker({format:'Y-m-d'});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	//新規作成ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $redirect_url }}');
		
	});
});
</script>

</body>
</html>

