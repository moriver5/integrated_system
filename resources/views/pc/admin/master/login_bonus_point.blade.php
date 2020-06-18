@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-7 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:wheat;"><b>ログインボーナスの付与ポイント</b><br>
				<b style="font:normal 12px/120% 'メイリオ',sans-serif;color:red;">現在の付与ポイント：{{ $point }}</b>
				</div>
                <div class="panel-body">
                    <form id="formGrantPoint" class="form-horizontal" method="POST" action="/admin/member/master/grant/login/bonus/point/send">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('point') ? ' has-error' : '' }}">
                            <label for="point" class="col-md-2 control-label">ポイント</label>

                            <div class="col-md-6">
								<table>
									<tr>
										<td style="text-align:center;font-weight:bold;">
			                                <input id="point" type="text" class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="point" value="{{ $point }}" placeholder="" required autofocus>
										</td>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;">
											Pt
										</td>
									</tr>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('point') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('dispmsg') ? ' has-error' : '' }}">
                            <label for="point" class="col-md-2 control-label">POPUP<br>文言</label>

                            <div class="col-md-10">
								<table width="100%">
									<tr>
										<td style="text-align:left;font-weight:bold;">
											<span style="color:red;font-size:14px;">注意：id="loginbonus"とclass="white-popup"は削除しないでください。<br>POPUPしなくなります。</span>
											<textarea rows="10"class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="dispmsg">{{ $dispmsg }}</textarea>
										</td>
									</tr>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dispmsg') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

						<div class="form-group{{ $errors->has('disptime') ? ' has-error' : '' }}">
                            <label for="point" class="col-md-2 control-label">表示時間</label>

                            <div class="col-md-6">
								<table>
									<tr>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="disptime" value="{{ $disptime }}" placeholder="" autofocus>
										</td>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;">
											秒
										</td>
									</tr>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('disptime') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

						<div class="form-group{{ $errors->has('disp_flg') ? ' has-error' : '' }}">
                            <label for="point" class="col-md-2 control-label">表示する</label>

                            <div class="col-md-1">
								<table width="100%">
									<tr>
										<td style="text-align:center;font-weight:bold;">
											@if( $disp_flg )
				                                <input type="checkbox" name="disp_flg" value="1" class="form-control" checked>
											@else
				                                <input type="checkbox" name="disp_flg" value="1" class="form-control">
											@endif
										</td>
									</tr>
								</table>

                                @if ($errors->has('disp_flg'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('disp_flg') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    ポイントを設定する
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
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formGrantPoint', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_setting_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection
