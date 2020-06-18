@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>出力文言設定</b>
				</div>
                <div class="panel-body">
                    <form id="formSentence" class="form-horizontal" method="POST" action="/admin/member/master/sentence/setting/send">
						{{ csrf_field() }}
						<center>
							<!-- タブ -->
							<ul id="tab-menu">
							@foreach($db_data as $index => $lines)
								@if( $index == 0 )
									<li class="active" id="{{ $lines->id }}">{{ $lines->title }}</li>
								@else
									<li id="{{ $lines->id }}">{{ $lines->title }}</li>							
								@endif
							@endforeach
							</ul>

							<!-- タブの中身 -->
							<div id="tab-box">
								@foreach($db_data as $index => $lines)
									<div class="form-group" style="text-align:left;">

										タイトル：<input type="text" name="title{{ $lines->id }}" value="{{ $lines->title }}" size="50" maxlength="{{ config('const.contents_title_max_length') }}">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;表示順：<input type="text" name="sort{{ $lines->id }}" value="{{ $lines->sort }}" size="5" maxlength="{{ config('const.num_digits_max_length') }}"> 番目
										<br />
										<br />
										<textarea id="contents{{ $lines->id }}" class="form-control contents" name="sentence{{ $lines->id }}" rows="10">{{ $lines->contents }}</textarea>
									</div>
								@endforeach
								<button type="submit" id="push_update" class="btn btn-primary">更新</button>
								<button type="submit" id="push_preview" class="btn btn-primary">プレビュー</button>
								<button type="submit" id="convert_table" class="btn btn-primary">変換表</button>
								<button type="submit" id="convert_emoji" class="btn btn-primary">絵文字表</button>
							</div>
						</center>
						<input type="hidden" name="title" value="">
						<input type="hidden" name="sort" value="">
						<input type="hidden" name="sentence" value="">
						<input type="hidden" name="tab" value="{{ $id }}">
					</form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var sub_win;
var prev_win;
$(document).ready(function(){
	//変換表ボタン押下
	$('#convert_table').on('click', function(){
		var id = $('[name="tab"]').val();
		sub_win = window.open('/admin/member/master/sentence/setting/convert/'+id, 'convert_table', 'width=1000, height=300');
		return false;
	});

	$('#convert_emoji').on('click', function(){
		var id = $('[name="tab"]').val();
		sub_win = window.open('/admin/member/master/emoji/convert/'+id, 'convert_emoji', 'width=600, height=300');
		return false;
	});

	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		var id = $('[name="tab"]').val();
		prev_win = window.open('/admin/member/master/sentence/setting/preview/'+id, 'convert_table', 'width=1100, height=500');
		var dom = prev_win.document.getElementById('main_column');
		var contents = $('[name="sentence'+id+'"]').val().replace(/\n/g,'<br />');
		$(dom).html(contents);
		return false;
	});

	//プレビュー機能
	$('.contents').keyup(function(){
		//編集した内容を更新用パラメータに設定
		var id = $('[name="tab"]').val();
		$('[name="sentence"]').val($('[name="sentence'+id+'"]').val());

		//プレビュー処理
		if( prev_win ){
			var dom = prev_win.document.getElementById('main_column');
			$(dom).html($('[name="sentence"]').val());
			
			//プレビュー画面リサイズ
//			prev_win.window.resizeTo(prev_win.document.documentElement.scrollWidth,prev_win.document.documentElement.scrollHeight + 70);
		}
	});

	//更新ボタン押下時に更新用パラメータにデータ設定
	$('#push_update').on('click', function(){
		var id = $('[name="tab"]').val();

		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formSentence', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '/admin/member/master/sentence/setting/redirect/' + id);

		//編集した内容を更新用パラメータに設定
		$('[name="title"]').val($('[name="title'+id+'"]').val());
		$('[name="sentence"]').val($('[name="sentence'+id+'"]').val());
		$('[name="sort"]').val($('[name="sort'+id+'"]').val());

		//previewウィンドウが開いてたら閉じる
		if( prev_win ){
			prev_win.close();
		}
	});

	//タブをクリックしなかったときのデフォルトのIDを設定
	$("input[name^=title]").each(function(index,elem){
		if( $('[name="tab"]').val() == '' ){
			$('[name="tab"]').val(elem.name.replace('title',''));
		}

		//クリックされたタブIDを取得
		var id = $('[name="tab"]').val();
			
		// タブメニュー
		$('#' + id).addClass('active').siblings('li').removeClass('active');

		// タブの中身
		var index = $('#tab-menu li#'+id).index();
		$('#tab-box div').eq(index).addClass('active').siblings('div').removeClass('active');

		//最初の画面読み込み時にプレビュー処理
		if( prev_win ){
			var dom = prev_win.document.getElementById('main_column');
			var contents = $('[name="sentence'+id+'"]').val().replace(/\n/g,'<br />');
			$(dom).html(contents);
		}

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
			var dom = prev_win.document.getElementById('main_column');
			$(dom).html($('[name="sentence'+id+'"]').val());

			//プレビュー画面リサイズ
//			prev_win.window.resizeTo(prev_win.document.documentElement.scrollWidth,prev_win.document.documentElement.scrollHeight + 70);
		
			//ウィンドウが既に開いていたら
			if( sub_win ){
				//クリックしたタブのIDを取得
				$('[name="tab"]').val($(this).attr("id"));
				var id = $('[name="tab"]').val();

				//クリックしたタブのURL先を変更
				sub_win.location.href = '/admin/member/master/sentence/setting/convert/'+id;
			}
		}
	});

});
</script>

@endsection
