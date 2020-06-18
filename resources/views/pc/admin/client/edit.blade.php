@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="font:normal 13px/130% 'メイリオ',sans-serif;">
                <div class="panel-heading"><b>顧客情報編集</b></div>
                <div class="panel-body">
                    <form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/client/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label">顧客ID</label>
                            <div class="col-md-6" style="padding-top:7px;">
								{{ $db_data->id }}
                            </div>

                            <div class="col-md-3" style="font:bold 14px/140% メイリオ,sans-serif;">
                                アカウント：@php if( $db_data->disable == 1 ){echo '<span style="color:red">停止中</span>';}else{ echo "稼働中"; }@endphp
                            </div>
                        </div>
						
                        <div class="form-group">
                            <label for="status" class="col-md-2 control-label">登録状態</label>
                            <div class="col-md-2">
								<select id="status" class="form-control" name="status">
									@foreach(config('const.regist_status') as $lines)
										@if( $db_data->status == $lines[0] )
											<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>
										@else
											<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
										@endif
									@endforeach
								</select>
                            </div>

							<label for="mail_status" class="col-md-4 control-label">電話番号</label>
                            <div class="col-md-3">
                                <input id="tel" type="text" class="form-control" name="tel" value="{{ $db_data->credit_certify_phone_no }}">

                                @if ($errors->has('tel'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tel') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						   
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-2 control-label">ログインID</label>

                            <div class="col-md-4">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $db_data->login_id }}" maxlength={{ config('const.login_id_length') }} autofocus>
                            </div>
						 					 
                            <label for="name" class="col-md-2 control-label">パスワード</label>

                            <div class="col-md-3">
                                <input id="password" type="text" class="form-control" name="new_password" value="{{ $db_data->password_raw }}" maxlength={{ config('const.password_max_length') }} autofocus>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ad_cd') ? ' has-error' : '' }}">
                            <label for="ad_cd" class="col-md-2 control-label">広告コード</label>

                            <div class="col-md-4">
                                <input id="ad_cd" type="text" class="form-control" name="ad_cd" value="{{ $db_data->ad_cd }}">
                            </div>
							
                            <label for="point" class="col-md-2 control-label"><a href="/admin/member/client/edit/point/history/{{ $db_data->login_id }}" target="_blank">ポイント</a></label>

                            <div class="col-md-3">
                                <input id="point" type="text" class="form-control" name="point" value="{{ $db_data->point }}">
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-2 control-label">メールアドレス</label>

                            <div class="col-md-4">
                                <input id="email" type="text" class="form-control" name="email" value="{{ $db_data->mail_address }}" maxlength={{ config('const.email_length') }} required autofocus>
                            </div>
							
                            <label for="mail_status" class="col-md-2 control-label">DM購読</label>

                            <div class="col-md-3">
								<select id="mail_status" class="form-control" name="mail_status">
									@foreach(config('const.edit_dm_status') as $lines)
										@if( $db_data->mail_status == $lines[0] )
											<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>
										@else
											<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
										@endif
									@endforeach
								</select>
                            </div>
                        </div>
				
                        <div class="form-group{{ $errors->has('group_id') ? ' has-error' : '' }}">
                            <label for="group_id" class="col-md-2 control-label">グループ</label>

                            <div class="col-md-4">
								<select id="group_id" class="form-control" name="group_id">
									@foreach($db_group_data as $lines)
										@if( $db_data->group_id == $lines[0] )
											<option value='{{ $lines[0] }}' selected>{{ $lines[1] }}</option>
										@else
											<option value='{{ $lines[0] }}'>{{ $lines[1] }}</option>										
										@endif
									@endforeach
								</select>
                            </div>

                        </div>
				
                        <div class="form-group{{ $errors->has('remember_token') ? ' has-error' : '' }}">
                            <label for="remember_token" class="col-md-2 control-label">アクセスキー</label>

                            <div class="col-md-6" style="padding-top:7px;">
                                {{ $db_data->remember_token }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="temporary_datetime" class="col-md-2 control-label">仮登録日時</label>

                            <div class="col-md-4" style="padding-top:7px;">
                                {{ $db_data->temporary_datetime }}
                            </div>
							
                            <label for="created_at" class="col-md-2 control-label">登録日時</label>

                            <div class="col-md-3" style="padding-top:7px;">
                                {{ $db_data->created_at }}
                            </div>
                        </div>
				
                        <div class="form-group">
                            <label for="updated_at" class="col-md-2 control-label">更新日時</label>

                            <div class="col-md-4" style="padding-top:7px;">
                                {{ $db_data->updated_at }}
                            </div>						

                            <label for="last_access_datetime" class="col-md-2 control-label">最終ｱｸｾｽ日時</label>

                            <div class="col-md-3" style="padding-top:7px;">
                                {{ $db_data->last_access_datetime }}<br>
								<a href="/admin/member/client/edit/{{$db_data->id}}/access/history" target="_blank"><span style="font-size:12px;"><b>アクセス履歴</b></span></a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="updated_at" class="col-md-2 control-label">退会日時</label>

                            <div class="col-md-4" style="padding-top:7px;">
                                {{ $db_data->quit_datetime }}
                            </div>

                            <label for="last_access_datetime" class="col-md-2 control-label">最終入金日時</label>

                            <div class="col-md-3" style="padding-top:7px;">
                                {{ $db_data->pay_datetime }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="last_access_datetime" class="col-md-2 control-label">MEMO</label>

                            <div class="col-md-6">
								<textarea cols="72" rows="7" name="description">{{ $db_data->description }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
							<label for="name" class="col-md-2 control-label">アカウント停止</label>
                            <div class="col-md-1">
								@if( $db_data->disable == 1 )
								<input type="checkbox" class="form-control" name="soft_del" value="1" checked>
								@else
								<input type="checkbox" class="form-control" name="soft_del" value="1">
								@endif
                            </div>
                        </div>

                        <div class="form-group">
							<label for="name" class="col-md-2 control-label">アカウント削除</label>
                            <div class="col-md-1">
								<input type="checkbox" class="form-control" name="del" value="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-11 col-md-offset-1">
                                <button id="push_btn" type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;
                                </button>
                                <button id="push_point_btn" type="submit" class="btn btn-primary">
                                    ポイント追加
                                </button>
                                <button id="push_mail_btn" type="submit" class="btn btn-primary">
                                    個別メール
                                </button>
                                <button id="push_order_btn" type="submit" class="btn btn-primary">
                                    注文追加
                                </button>
                                <button id="push_order_history_btn" type="submit" class="btn btn-primary">
                                    注文履歴
                                </button>
                                <button id="push_melmaga_btn" type="submit" class="btn btn-primary">
                                    メルマガ履歴
                                </button>
								@if( $back_btn_flg )
                                <button id="back_btn" type="submit" class="btn btn-primary">
                                   &nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;
                                </button>
								@endif
                            </div>
                        </div>
					<input type='hidden' name='id' value='{{ $db_data->id }}'>
					<input type='hidden' name='page' value='{{ $page }}'>
					<input type='hidden' name='regist_date' value='{{ $db_data->regist_date }}'>
					<input type='hidden' name='temporary_date' value='{{ $db_data->temporary_datetime }}'>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var add_point_win;
var add_order_win;
var order_history_win;
var send_mail_win;
var melmaga_win;
$(document).ready(function(){
	//戻るボタンクリック
	$('#back_btn').click(function(){
		window.location.href = '{{ $back_url }}'
		return false;
	});

	//ポイント手動追加ボタン押下
	$('#push_point_btn').click(function(){
		add_point_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/point/add', 'point_add', 'width=600, height=350');
		return false;
	});

	//個別メールボタン押下
	$('#push_mail_btn').click(function(){
		send_mail_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/mail/view', 'edit_mail', 'width=600, height=850');
		return false;
	});

	//注文追加ボタン押下
	$('#push_order_btn').click(function(){
		add_order_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/order/add', 'point_order', 'width=700, height=550');
		return false;
	});

	//注文履歴ボタン押下
	$('#push_order_history_btn').click(function(){
		order_history_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/order/history', 'order_history', 'width=800, height=700');
		return false;
	});

	//メルマガ履歴ボタン押下
	$('#push_melmaga_btn').click(function(){
		melmaga_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/melmaga/history', 'melmaga_history', 'width=1000, height=500');
		return false;
	});

	//アカウント編集ボタンを押下
	$('#push_btn').click(function(){
		var alert_msg,alert_end_msg;

		//削除チェックボックスの値を取得
		var del_flg = $('[name=del]:checked').val();

		//削除メッセージ設定
		if( del_flg == 1 ){
			alert_msg = '{{ __('messages.dialog_del_alert_msg') }}';
			alert_end_msg = '{{ __('messages.dialog_del_end_msg') }}';

		//編集メッセージ設定
		}else{
			alert_msg = '{{ __('messages.dialog_alert_msg') }}';
			alert_end_msg = '{{ __('messages.account_edit_end') }}';			
		}
		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト、msg非表示フラグ、redirectフラグ、redirect先パス
		submitAlert('formEdit', 'post', '{{ __('messages.dialog_alert_title') }}', alert_msg, alert_end_msg, '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});
});
</script>

@endsection
