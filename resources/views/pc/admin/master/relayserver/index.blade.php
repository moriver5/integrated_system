@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>リレーサーバーIP設定</b><br />
					<span style="color:red;font-size:12px;font-weight:bold;">※指定しない場合のサーバーIP：127.0.0.1、ポート番号：25</span>
				</div>
                <div class="panel-body">
                    <form id="formNgWord" class="form-horizontal" method="POST" action="/admin/member/master/relayserver/setting/send">
						{{ csrf_field() }}
						メルマガ用リレーサーバーIPとポート番号
						<div class="form-group">
							<div class="col-md-9">
								<input type="text" class="form-control" name="melmaga_relayserver" value="{{ $db_data['melmaga'][0] }}" placeholder="例：127.0.0.1"><br />
							</div>
							<div class="col-md-3">
								<input type="text" class="form-control" name="melmaga_port" value="{{ $db_data['melmaga'][1] }}" placeholder="例：25">
							</div>
						</div>
						ユーザー用（登録・変更・パス忘れ）リレーサーバーIPとポート番号
						<div class="form-group">
							<div class="col-md-9">
								<input type="text" class="form-control" name="setting_relayserver" value="{{ $db_data['setting'][0] }}" placeholder="例：127.0.0.1"><br />
							</div>
							<div class="col-md-3">
								<input type="text" class="form-control" name="setting_port" value="{{ $db_data['setting'][1] }}" placeholder="例：25">
							</div>
						</div>
						個別用（お問い合わせなど）リレーサーバーIPとポート番号
						<div class="form-group">
							<div class="col-md-9">
								<input type="text" class="form-control" name="personal_relayserver" value="{{ $db_data['personal'][0] }}" placeholder="例：127.0.0.1"><br />
							</div>
							<div class="col-md-3">
								<input type="text" class="form-control" name="personal_port" value="{{ $db_data['personal'][1] }}" placeholder="例：25">
							</div>
						</div>
						<center><button type="submit" id="push_update" class="btn btn-primary">設定</button></center>
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
