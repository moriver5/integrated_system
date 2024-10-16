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
	<link href="{{ asset('css/admin/colorbox.css') }}" rel="stylesheet" />

	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/admin/jquery.colorbox-min.js') }}"></script>

</head>
<body>
<br />
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>LP編集 [LP{{$id}} - {{config('const.list_career_lp_dir')[$type]}}]</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>

                <div class="panel-heading">
					<div style="float:left;"><b>ページ名：</b></div>
					<!-- ページ名 -->
					@if( !empty($db_data) )
					@foreach($lp_default_page as $index => $line)
						@if( $current_page == $line->name )
						<span style="font-size:13px;background:gainsboro;padding:4px 5px 0px 5px;"><b>{{ $line->name }}</b></span>
						@elseif( empty($current_page) && $index == 0 )
						<span style="font-size:13px;background:gainsboro;padding:4px 5px 0px 5px;"><b>{{ $line->name }}</b></span>
						@else
						<span style="padding:5px 5px 0px 5px;"><b><a href="/admin/member/lp/create/content/{{ $id }}/{{ $line->type }}/{{ $line->name }}">{{ $line->name }}</a></b></span>						
						@endif
					@endforeach
					@endif
				</div>


                <div class="panel-heading">
					<div class="panel-body">
                    <form id="formAddPage" class="form-horizontal" method="POST" action="{{ $add_page_post_url }}">
                        {{ csrf_field() }}
						<div class="form-group">
							<div style="float:left;padding-top:10px;"><b>ページ名：</b></div>
							<div class="col-md-2">
								<input type="text" name="page" class="contents form-control" value="" placeholder="例：sitemap">
							</div>
							<button id="push_add_btn" type="submit" class="btn btn-primary">
								&nbsp;&nbsp;ページ追加&nbsp;&nbsp;
							</button>
						</div>
					</form>
					</div>
				</div>

                <div class="panel-body">
                    <form id="formEdit" class="form-horizontal" method="POST" action="{{ $post_url }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="open_flg" class="col-md-1 control-label admin_default" style="color:black;">公開</label>
                            <div class="col-md-2">
								<select id="open_flg" class="form-control" name="open_flg">
									@foreach($list_open_flg as $lines)
										@if( $db_data->url_open_flg == $lines[0] )
										<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>
										@else
										<option value='{{$lines[0]}}'>{{$lines[1]}}</option>
										@endif
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
							<label for="type" class="col-md-1 control-label admin_default" style="color:black;">Link</label>

							<div class="col-md-6" style="top:8px;">
								<b>{{ $link_url }}</b>
                            </div>
						</div>

                        <div class="form-group">
							<label for="type" class="col-md-1 control-label admin_default" style="color:black;">Content</label>

							<div class="col-md-10" id="lp_div">
								<textarea cols="60" rows="15" name="lp_content" id="lp_content" class="contents form-control">{{ $db_data->content }}</textarea>
                            </div>
							<div class="col-md-5" style="display:none;height:350px;width:500px;" id="outer_preview">
								<iframe src="" width="500px" height="345px" id="iframe_preview"></iframe>
                            </div>
						</div>

                        <div class="form-group">
                            <label for="url" class="col-md-1 control-label admin_default" style="color:black;">削除</label>

                            <div class="col-md-1">
                                <input id="del" type="checkbox" class="form-control" name="del" value="1">
                            </div>
                        </div>

                        <div>
                            <div style="text-align:center;">
								<a href="/admin/member/master/convert/setting" class="btn btn-primary" target="_blank">変換表</a>
                                <button id="lp_preview" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;プレビュー&nbsp;&nbsp;
                                </button>
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
var prev_win;
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

	//プレビュー表示
	$('#lp_preview').click(function () {
		if( $('#lp_div').hasClass('col-md-10') ){
			$.ajax({
				url:"/admin/member/lp/create/content/{{ $id }}/{{ $type }}/{{ $current_page }}/preview",
				type:'POST',
				data:$("#formEdit").serialize(),
				headers:{'X-CSRF-TOKEN':'{{ $csrf_token }}'},
				success:function() {
					$("#iframe_preview").attr("src", '{{ $preview_url }}');
				},
				error:function(error) {
				}
			});

			$('#outer_preview').toggle();
			$('#lp_div').removeClass('col-md-10');
			$('#lp_div').addClass('col-md-6');
		}else{
			$.ajax({
				url:"/admin/member/lp/create/content/{{ $id }}/{{ $type }}/{{ $current_page }}/preview",
				type:'POST',
				data:$("#formEdit").serialize(),
				headers:{'X-CSRF-TOKEN':'{{ $csrf_token }}'},
				success:function() {
					$("#iframe_preview").attr("src", '{{ $preview_url }}');
				},
				error:function(error) {

				}
			});
		}
		return false;
	});

	$('.btn').click(function () {
		parent.$.colorbox.close();
	});

	//更新ボタンを押下
	$('#push_btn').click(function(){
		var del = $('[name="del"]:checked').val();
		if( del == 1 ){
			var file_name = $(this).parents('form').attr('action').replace(/\/send/,'');
			//indexを削除しようとした場合
			if( file_name == 'index' ){
				//エラーメッセージ表示
				swal({
					title: "{{ __('messages.dialog_alert_err_title') }}",
					text: "indexは削除できません",
					icon: "warning",
					buttons: false,
					dangerMode: true
				});
				return false;
			}
		}

		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formEdit', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});

	//ページ追加ボタンを押下
	$('#push_add_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formAddPage', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});

});
</script>

</body>
</html>

