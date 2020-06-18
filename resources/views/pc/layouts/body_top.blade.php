<body id="state_out">
<header id="header">
<div class="inner">
<p class="logo"><a href="/"><img src="/css/common/images/logo.png" alt="{{ config('const.html_title') }}"></a></p>

<form name="login" id="login" action="{{url('/login')}}" method="post">
{{csrf_field()}}
<ul>
<li>
	@if($errors->has('login_id'))
		{{ $errors->first('login_id') }}<br />
	@endif
	<input name="login_id" type="text" placeholder="会員ID" maxlength={{ config('const.login_id_max_length') }} tabindex="1">
</li>
<li>
	@if($errors->has('password'))
		{{ $errors->first('password') }}<br />
	@endif
	<input name="password" type="password" placeholder="パスワード" maxlength={{ config('const.password_max_length') }} tabindex="2">
</li>
</ul>
<p class="btn"><input type="submit" value="ログイン" tabindex="3"></p>
<p class="f_15"><a href="/forget">ログインID・パスワードをお忘れの方はこちら</a></p>
</form>
<!-- /.inner --></div>
</header>

