@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">会員情報変更</h1>
<p class="area_01 mb_L">以下よりパスワードの変更とメールアドレスの変更を行えます。</p>

<section>
<h2 class="ttl_02">パスワードの変更</h2>
<form action="{{ config('const.member_setting_password_path') }}" method="post">
{{csrf_field()}}
<div class="area_01">
@if($errors->has('new_password'))
	<p>{{ $errors->first('new_password') }}</p>
@endif
@if($errors->has('new_password_confirmation'))
	<p>{{ $errors->first('new_password_confirmation') }}</p>
@endif
@if( !$errors->has('new_password') && !$errors->has('new_password_confirmation') && !empty($send_msg) )
	<p>{{ $send_msg }}</p>
@endif
<p class="mb_S">現在のパスワード：<span class="f_red">{{ $password_raw }}</span></p>
<table class="tbl_01">
<colgroup width="40%"></colgroup>
<tr>
<th>新しいパスワード(半角英数字{{ config('const.password_length') }}桁以上)</th>
<td><input type="password" name="new_password" maxlength="{{ config('const.password_max_length') }}" required></td>
</tr>
<tr>
<th>確認用パスワード</th>
<td><input type="password" name="new_password_confirmation" maxlength="{{ config('const.password_max_length') }}" required></td>
</tr>
</table>
<p class="btn_02"><input type="submit" value="変更する"></p>
</div><!--/.area_01-->
</form>
</section>

<section>
<h2 class="ttl_02">メールアドレスの変更</h2>
<form name="form2" action="{{ config('const.member_setting_email_path') }}" method="post">
{{csrf_field()}}
<div class="area_01">
<p class="mb_S">現在のメールアドレス：<span class="f_red">{{ $email }}</span></p>
@if($errors->has('pc_email'))
	<p>{{ $errors->first('pc_email') }}</p>
@endif
@if( !empty($mail_send_msg) )
	<p>{{ $mail_send_msg }}</p>
@endif
@if( !empty($pc_email_status_msg) )
	{{ $pc_email_status_msg }}
@endif
<table class="tbl_01">
<colgroup width="40%"></colgroup>
<tr>
<th>変更先のメールアドレス</th>
<td><input type="email" name="pc_email" maxlength="{{ config('const.email_length') }}" required></td>
</tr>
</table>
<p class="btn_02"><input type="submit" value="変更する" id="user_setting_btn"></p>
</div><!--/.area_01-->
</form>
<script>
<!-- アドレス変更用のjavascript -->
$(document).ready(function () {
	アドレス変更ボタンのクリック
	$("#user_setting_btn").on("click", function () {
		var fm = document.form2;
		fm.submit();

		return false;
	});
});
</script>
</section>
@endsection