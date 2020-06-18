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
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>ご利用者の声 編集</b>
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
											<div id="drop" style="text-align:center;width:100px; height:140px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formImageUpload', 'import_file', '{{csrf_token()}}', '{{ __('messages.dialog_img_upload_end_msg') }}', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id'], 'post', '10000')">
												<div style="font:italic normal bold 12px/120% 'メイリオ',sans-serif;color:silver;">アップロード画像をここに<br />ドラッグアンドドロップ</div>
												<center><div id="result" style="font:italic normal bold 11px/110% 'メイリオ',sans-serif;margin:0px 10px 0px 10px;width:130px;viertival-align:middle;"></div></center>
											</div>
										</div>
									</td>
									<td style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
										@if( !empty($db_data->img) )
											<b>設定済：{{ $db_data->img }}</b><br />
											<img src="{{ config('const.list_url_const')[Session::get('operation_select_db')] }}/{{ config('const.voice_images_path') }}/{{ $db_data->img }}?ver={{$ver}}">
										@else
										<div style="width:190px;text-align:center;"><b>画像未設定</b></div>
										@endif
									</td>
								</tr>
							</table>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
					</form>

                    <form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/page/voice/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">投稿日時</label>

                            <div class="col-md-3">
								<input id="post_date" type="text" class="form-control" name="post_date" value="{{$db_data->post_date}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">タイトル</label>
                            <div class="col-md-9">
								<input id="title" type="text" class="form-control" name="title" value="{{ $db_data->title }}" maxlength="{{ config('const.voice_title_max_length') }}" autofocus>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">投稿者</label>

                            <div class="col-md-9">
								<input id="writer" type="text" class="form-control" name="writer" value="{{ $db_data->name }}" maxlength="{{ config('const.voice_writer_max_length') }}">
                            </div>
                        </div>

                        <div class="form-group">
							<label for="type" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">コメント<br />(タグ可能)</label>

							<div class="col-md-9">
								<textarea cols="70" rows="4" name="comment" id="msg" class="contents form-control" maxlength="{{ config('const.voice_comment_max_length') }}">{{ $db_data->msg }}</textarea>
                            </div>
						</div>

                        <div class="form-group">
                            <label for="open_flg" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開</label>
                            <div class="col-md-3">
								<select id="open_flg" class="form-control" name="open_flg">
									@foreach($list_open_flg as $lines)
										@if( $lines[0] == $db_data->open_flg )
											<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>										
										@else
											<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
										@endif
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="url" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">削除</label>

                            <div class="col-md-1">
                                <input id="del" type="checkbox" class="form-control" name="del" value="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-5">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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

	//フォーカスされたら消す
	$('[name=race_date]').focusin(function(){
		$('[name=race_date]').attr("placeholder","");
	});

	//フォーカスが外されたら元に戻す
	$('[name=race_date]').focusout(function(){
		$('[name=race_date]').attr("placeholder","必須入力");
	});

	$.datetimepicker.setLocale('ja');

	//公開開始日時
	$('#post_date').datetimepicker({format:'Y-m-d'});

	//カーソルがフォーカスされたら日付を消す	
	$('#post_date').focus(function(){
		$('#race_date').val('');
	});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	//更新ボタンを押下
	$('#push_btn').click(function(){
		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formEdit', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
		
	});
});
</script>

</body>
</html>

