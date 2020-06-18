@extends('layouts.app')

@section('content')
<br>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading" style="color:red;"><b>操作するサイト名を選択してください</b></div>
                <div class="panel-body">
                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/site/select/send">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-2 control-label">サイト名：</label>

                            <div class="col-md-7">
								<select name="select_db" class="form-control">
								@foreach($list_db as $lines)
									@if( $select_db == $lines->db )
									<option value="{{ $lines->db }}" selected>{{ $lines->name }}</option>
									@else
									<option value="{{ $lines->db }}">{{ $lines->name }}</option>
									@endif
								@endforeach
								</select>
                            </div>

                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;決定&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
<script src="{{ asset('js/admin/alert.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('[name=email]').focusin(function(){
		$('[name=email]').attr("placeholder","");
	});

	$('[name=email]').focusout(function(){
		$('[name=email]').attr("placeholder","登録したメールアドレスがログインIDとなります");
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.change_site_end_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection
