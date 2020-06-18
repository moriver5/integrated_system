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
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>予想登録</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">

                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/forecast/create/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="disp_date" class="col-md-2 control-label admin_default" style="color:black;">表示日時</label>

                            <div class="col-md-3">
								<input id="disp_sdate" type="text" class="form-control" name="disp_sdate" placeholder="開始表示日時">
                            </div>

                            <div class="col-md-3">
								<input id="disp_edate" type="text" class="form-control" name="disp_edate" placeholder="終了表示日時">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="open_date" class="col-md-2 control-label admin_default" style="color:black;">公開日時</label>

                            <div class="col-md-3">
								<input id="open_sdate" type="text" class="form-control" name="open_sdate" placeholder="開始公開日時">
                            </div>

                            <div class="col-md-3">
								<input id="open_edate" type="text" class="form-control" name="open_edate" placeholder="終了公開日時">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="category" class="col-md-2 control-label admin_default" style="color:black;">カテゴリ</label>
                            <div class="col-md-2">
								<select id="category" class="form-control" name="category">
									@foreach($forecast_category as $lines)
										<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="disp_range" class="col-md-2 control-label admin_default" style="color:black;">
								表示範囲設定<br />
								<a href ="{{ config('const.base_admin_url') }}/{{ config('const.group_url_path') }}" target="_blank">グループID参照</a><br />
							</label>
                            <div class="col-md-6">
								<input id="groups" type="text" class="form-control" name="groups" value="" autofocus placeholder="グループID (複数ある場合は,(半角カンマ)区切り)">
                            </div>
                        </div>

						<div class="form-group">
                            <label for="product_id" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">商品選択</label>
                            <div class="col-md-4">
								<select id="product_id" class="form-control" name="product_id">
									<option value="0">未選択</option>
									@foreach($list_product_data as $lines)
										<option value='{{$lines->id}}'>{{$lines->title}}(商品ID:{{ $lines->id }} )</option>										
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="disp_range" class="col-md-2 control-label admin_default" style="color:black;">
								<a href ="{{ config('const.base_admin_url') }}/{{ config('const.top_content_url_path') }}" target="_blank">キャンペーンID参照</a>
							</label>
                            <div class="col-md-3">
								<select id="campaigns" name="campaigns" class="form-control">
								@if( count($list_campaign_data) > 0 )
									<option value="">未選択</option>
									@foreach($list_campaign_data as $lines)
									<option value="{{ $lines->id }}">{{ $lines->title }}（キャンペーンID：{{ $lines->id }}）</option>
									@endforeach
								@endif
								</select>
							</div>
                        </div>

                        <div class="form-group">
                            <label for="open_flg" class="col-md-2 control-label admin_default" style="color:black;">公開</label>
                            <div class="col-md-2">
								<select id="open_flg" class="form-control" name="open_flg">
									@foreach($list_open_flg as $lines)
										<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="point" class="col-md-2 control-label admin_default" style="color:black;">ポイント</label>
                            <div class="col-md-2">
								<input id="point" type="text" class="form-control" name="point" value="" autofocus placeholder="例：100">								
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label admin_default" style="color:black;">見出し<br>(HTMLタグ)</label>
                            <div class="col-md-6">
								<textarea id="headline" class="form-control" name="headline" cols="5" rows="5" autofocus placeholder="必須入力"></textarea>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label admin_default" style="color:black;">タイトル</label>
                            <div class="col-md-6">
								<input id="title" type="text" class="form-control" name="title" value="" maxlength="{{ config('const.forecast_title_max_length') }}" autofocus placeholder="必須入力">
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="comment" class="col-md-2 control-label admin_default" style="color:black;">コメント</label>

                            <div class="col-md-6">
								<input id="comment" type="text" class="form-control" name="comment" value="" size=22 maxlength="{{ config('const.forecast_comment_max_length') }}" autofocus placeholder="必須入力">
                            </div>
                        </div>

                        <div class="form-group">
							<label for="type" class="col-md-2 control-label admin_default" style="color:black;">内容</label>

							<div class="col-md-6">
								<textarea cols="90" rows="4" name="detail" id="detail" class="contents form-control" placeholder="必須入力"></textarea>
                            </div>
						</div>

                        <div>
                            <div style="text-align:center;">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;作成実行&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
                                <button id="push_preview" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;プレビュー&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
                                <button id="push_calc" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;組合せ数計算&nbsp;&nbsp;&nbsp;
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
<script src="{{ asset('js/admin/utility.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	var prev_win;
	var calc_win;
	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		//別ウィンドウを開く
		prev_win = window.open('/admin/member/forecast/create/preview', 'forecast_preview', 'width=1250, height=900');

		//別ウィンドウ読み込み後に実行
		prev_win.onload = function(){
			var dom = prev_win.document;
			var title = dom.getElementById('create_title');
			var comment = dom.getElementById('create_comment');
			var detail = dom.getElementById('create_preview');

			//html_bodyを別ウィンドウに反映
			$(title).html(escapeHtml($('[name="title"]').val()));
			$(comment).html(escapeHtml($('[name="comment"]').val()));
			$(detail).html(escapeHtml($('[name="detail"]').val()).replace(/\n/g,'<br />'));
		};
		return false;
	});

	//プレビュー機能
	$('.contents').keyup(function(){
		//編集した内容を更新用パラメータに設定
		$('[name="detail"]').val($('[name="detail"]').val());

		//プレビュー処理
		if( prev_win ){
			var dom = prev_win.document.getElementById('create_preview');

			$(dom).html(escapeHtml($('[name="detail"]').val()).replace(/\n/g,'<br />'));
		}
	});

	$('#push_calc').on('click', function(){
		//別ウィンドウを開く
		calc_win = window.open('/admin/member/forecast/create/calc', 'forecast_calc', 'width=1200, height=675');

		return false;
	});

	//フォーカスが外されたら元に戻す
	$('[name=disp_sdate]').focusout(function(){
		$('[name=disp_sdate]').attr("placeholder","開始表示日時");
	});

	$('[name=disp_edate]').focusout(function(){
		$('[name=disp_edate]').attr("placeholder","終了表示日時");
	});

	$('[name=open_sdate]').focusout(function(){
		$('[name=open_sdate]').attr("placeholder","開始公開日時");
	});

	$('[name=open_edate]').focusout(function(){
		$('[name=open_edate]').attr("placeholder","終了公開日時");
	});

	$.datetimepicker.setLocale('ja');

	//開催日
	$('#disp_sdate').datetimepicker();
	$('#disp_edate').datetimepicker();
	$('#open_sdate').datetimepicker();
	$('#open_edate').datetimepicker();

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

