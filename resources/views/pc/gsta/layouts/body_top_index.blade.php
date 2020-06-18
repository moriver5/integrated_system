<body id="state_out">
<header id="header">
<div class="inner">
<h1 class="logo"><a href="/"><img src="/images/h_logo.png" alt="ゴールデンスターズ"></a></h1>
<div class="login view_pc">
<form name="login" id="login" action="{{url('/login')}}" method="post">
{{ !empty(csrf_field()) ? csrf_field():''}}
<ul>
<li>
<input type="text" name="login_id" placeholder="会員ID" maxlength="{{ config('const.login_id_max_length') }}" tabindex="1"></li>
<li>
<input type="password" name="password" placeholder="パスワード" maxlength="{{ config('const.password_max_length') }}" tabindex="2"></li>
</ul>
<p class="btn_login"><input type="submit" value="ログイン" tabindex="3"></p>
</form>
</div><!--/.login view_pc-->
<div class="login view_sp"><p class="btn_login"><a href="/sp/login">ログイン</a></p></div>
</div><!--/.inner-->
</header>
