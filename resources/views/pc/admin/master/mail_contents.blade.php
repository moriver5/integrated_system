@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>自動メール文設定</b>
				</div>
                <div class="panel-body">
                    <form id="formSentence" class="form-horizontal" method="POST" action="/admin/member/master/mail_sentence/setting/send">
						{{ csrf_field() }}
						<center>
							<!-- タブ -->
							<ul id="tab-menu">
							@foreach($db_data as $index => $lines)
								@if( $index == 0 )
									<li class="active" id="{{ $lines->id }}">{{ $lines->name }}</li>
								@else
									<li id="{{ $lines->id }}">{{ $lines->name }}</li>							
								@endif
							@endforeach
							</ul>

							<!-- タブの中身 -->
							<div id="tab-box">
								@foreach($db_data as $index => $lines)
									<div class="form-group" style="text-align:left;">
										送信者：<input type="text" name="from{{ $lines->id }}" value="{{ $lines->from }}" size="30" maxlength="{{ config('const.from_name_length') }}">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										送信元メール：<input type="text" name="from_mail{{ $lines->id }}" value="{{ $lines->from_mail }}" size="30" maxlength="{{ config('const.email_length') }}">
										<br />
										<br />
										件名：<input type="text" name="subject{{ $lines->id }}" value="{{ $lines->subject }}" size="60" maxlength="{{ config('const.subject_length') }}">
										<br />
										<br />
										<textarea id="contents{{ $lines->id }}" class="form-control mail_body" name="body{{ $lines->id }}" rows="10">{{ $lines->body }}</textarea>
									</div>
								@endforeach
								<button type="submit" id="push_update" class="btn btn-primary">更新</button>
								<button type="submit" id="convert_table" class="btn btn-primary">変換表</button>
							</div>
						</center>
						<input type="hidden" name="from" value="">
						<input type="hidden" name="from_mail" value="">
						<input type="hidden" name="subject" value="">
						<input type="hidden" name="body" value="">
						<input type="hidden" name="tab" value="{{ $id }}">
					</form>
                </div>
            </div>
				
			<b>Preview</b>
			<div class="panel panel-default" style="background:papayawhip;color:black;">
				<div id="all">
					<div id="page">
						<div id="container" class="clearfix">
							<div id="main_column">
								<div class="main_column_inner09 float_l pad_b25 pad_t25">
									<div id="main_column_inner09_box">
										<div id="realtime_preview" class="info_middle">

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/utility.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var sub_win;
$(document).ready(function(){

	$('#convert_table').on('click', function(){
		var id = $('[name="tab"]').val();
		sub_win = window.open('/admin/member/master/mail_sentence/setting/convert/'+id, 'convert_table', 'width=1000, height=300');
		return false;
	});

	//プレビュー機能
	$('.mail_body').keyup(function(){
		//編集した内容を更新用パラメータに設定
		var id = $('[name="tab"]').val();
		$('[name="body"]').val($('[name="body'+id+'"]').val());

		//プレビュー処理
		var dom = document.getElementById('realtime_preview');
		var contents = escapeJsTag($('[name="body"]').val()).replace(/\n/g,'<br />');
		$(dom).html(contents);
	});

	//更新ボタン押下時に更新用パラメータにデータ設定
	$('#push_update').on('click', function(){
		var id = $('[name="tab"]').val();

		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formSentence', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '/admin/member/master/mail_sentence/setting/redirect/' + id);

		//編集した内容を更新用パラメータに設定
		$('[name="from"]').val($('[name="from'+id+'"]').val());
		$('[name="from_mail"]').val($('[name="from_mail'+id+'"]').val());
		$('[name="subject"]').val($('[name="subject'+id+'"]').val());
		$('[name="body"]').val($('[name="body'+id+'"]').val());
	});

	//タブをクリックしなかったときのデフォルトのIDを設定
	$("input[name^=subject]:first").each(function(index,elem){
		if( $('[name="tab"]').val() == '' ){
			$('[name="tab"]').val(elem.name.replace('subject',''));
		}

		//クリックされたタブIDを取得
		var id = $('[name="tab"]').val();
			
		// タブメニュー
		$('#' + id).addClass('active').siblings('li').removeClass('active');

		// タブの中身
		var index = $('#tab-menu li#'+id).index();
		$('#tab-box div').eq(index).addClass('active').siblings('div').removeClass('active');

		//最初の画面読み込み時にプレビュー処理
		var dom = document.getElementById('realtime_preview');
		var contents = $('[name="body'+id+'"]').val().replace(/\n/g,'<br />');
		$(dom).html(contents);

		return false;
	});

	//出力文言のタブ切り替え
	$('#tab-menu li').on('click', function(){

		//編集したIDがわかるようにIDをパラメータに設定
		$('[name="tab"]').val($(this).attr("id"));

		if($(this).not('active')){
			// タブメニュー
			$(this).addClass('active').siblings('li').removeClass('active');

			// タブの中身
			var index = $('#tab-menu li').index(this);
			$('#tab-box div').eq(index).addClass('active').siblings('div').removeClass('active');

			//クリックされたタブIDを取得
			var id = $('[name="tab"]').val();

			//プレビュー処理
			var dom = document.getElementById('realtime_preview');
			var contents = $('[name="body'+id+'"]').val().replace(/\n/g,'<br />');
			$(dom).html(contents);
		
			//ウィンドウが既に開いていたら
			if( sub_win ){
				//クリックしたタブのIDを取得
				$('[name="tab"]').val($(this).attr("id"));
				var id = $('[name="tab"]').val();

				//クリックしたタブのURL先を変更
				sub_win.location.href = '/admin/member/master/mail_sentence/setting/convert/'+id;
			}
		}
	});

});
</script>

@endsection
