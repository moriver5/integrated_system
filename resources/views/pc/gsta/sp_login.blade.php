@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top_index')

@section('entry')
<div id="container">
<main class="wide">
<h1 class="ttl_01">ログイン</h1>
<div class="area_01">
<div class="box_01 wd_90">
<p class="t_inline"><span>会員番号ID・パスワードを入力して</span><span>ログインしてください。</span></p>
</div><!--/.box_01-->
	@if($errors->has('login_id'))
		<p class="errormsg">{{ $errors->first('login_id') }}<br></p>
	@endif
	@if($errors->has('password'))
		<p class="errormsg">{{ $errors->first('password') }}<br></p>
	@endif
<form action="{{url('/login')}}" method="post">
{{csrf_field()}}
<table class="tbl_01">
<tr>
<th>会員番号ID</th>
<td><input type="text" name="login_id" maxlength="{{ config('const.login_id_max_length') }}" placeholder="会員ID"></td>
</tr>
<tr>
<th>パスワード</th>
<td><input type="password" name="password" maxlength="{{ config('const.password_max_length') }}" placeholder="パスワード"></td>
</tr>
</table>
<p class="btn_01"><input type="submit" value="ログイン"></p>
</form>
<p class="t_center"><a href="/forget" class="f_red t_line">ID・パスワードをお忘れの方はこちら</a></p>
</div><!--/.area_01-->
</main>
</div><!--/#container-->
@endsection