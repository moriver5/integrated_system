@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-3 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:wheat;"><b>付与ポイント</b>&nbsp;&nbsp;&nbsp;
				<b style="font:normal 12px/120% 'メイリオ',sans-serif;color:red;">現在の付与ポイント：{{ $point }}</b>
				</div>
                <div class="panel-body">
                    <form id="formGrantPoint" class="form-horizontal" method="POST" action="/admin/member/master/grant/point/send">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="point" class="col-md-4 control-label">ポイント</label>

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

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-2">
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
