@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:wheat;"><b>お知らせ設定一覧</b></div>
                <div class="panel-body">
                    <form id="formGrantPoint" class="form-horizontal" method="POST" action="/admin/member/master/info/edit/{{ $id }}/send">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('userinfo') ? ' has-error' : '' }}">
                            <div class="col-md-12">
								<table width="100%">
									<tr>
										<td style="text-align:left;font-weight:bold;">
											お知らせメッセージ<br>
											<span style="color:red;font-size;16px;">※注意：id="userinfo&lt;!--index--&gt;"とclass="white-popup disptime_&lt;!--disptime--&gt;"は削除しないでください。<br>POPUPしなくなります。</span>
										</td>
									</tr>
									<tr>
										<td style="text-align:center;font-weight:bold;">
											<textarea id="userinfo" cols="40" rows="10" class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="userinfo" placeholder="" autofocus>{{ $userinfo }}</textarea>
										</td>
									</tr>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('userinfo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('userinfo') ? ' has-error' : '' }}">
                            <div class="col-md-7">
								<table width="100%">
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:80px;">
											表示順
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="order" value="{{ $order }}" placeholder="" autofocus>
										</td>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:120px;">
											番目に表示
										</td>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('userinfo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('userinfo') ? ' has-error' : '' }}">
                            <div class="col-md-5">
								<table width="100%">
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:80px;">
											表示時間
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="disptime" value="{{ $disptime }}" placeholder="" autofocus>
										</td>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:35px;">
											秒
										</td>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('userinfo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('userinfo') ? ' has-error' : '' }}">
                            <div class="col-md-3">
								<table width="100%">
									<tr>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:80px;">
											表示する
										</td>
										<td style="text-align:center;font-weight:bold;">
											@if( $disp_flg )
				                                <input type="checkbox" name="disp_flg" value="1" class="form-control" checked>
											@else
				                                <input type="checkbox" name="disp_flg" value="1" class="form-control">
											@endif
										</td>
									</tr>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('userinfo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">
                                    お知らせを設定する
                                </button>
                                <a href="/admin/member/master/info" class="btn btn-primary">戻る</a>
                            </div>
                        </div>
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
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formGrantPoint', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_setting_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection
