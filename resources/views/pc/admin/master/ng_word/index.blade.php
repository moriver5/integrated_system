@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>メールアドレス禁止ワード設定</b>
				</div>
                <div class="panel-body">
                    <form id="formNgWord" class="form-horizontal" method="POST" action="/admin/member/master/mailaddress_ng_word/setting/send">
						{{ csrf_field() }}
						<div style="color:red;font-size:12px;font-weight:bold;float:left;">
							・メールアドレスに含まれる禁止ワードを設定します。<br />
							・複数ある場合は改行で区切ってください。<br />
							・正規表現使用可能
						</div>
						<textarea class="form-control" name="ng_word" cols="10" rows="10">{{ $ng_word }}</textarea><br />
						<center><button type="submit" id="push_update" class="btn btn-primary">更新</button></center>
					</form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//更新ボタン押下時に更新用パラメータにデータ設定
	$('#push_update').on('click', function(){

		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formNgWord', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});

});
</script>

@endsection
