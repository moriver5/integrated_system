@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top')
@section('entry')

<main>
<h1 class="ttl_01">ログインID・パスワードをお忘れの方</h1>

<section>
<h2 class="ttl_02">ログインID・パスワードをお忘れの方はこちら</h2>
<div class="cont">
	<form action="{{ config('const.forget_send_path') }}" method="post" name="forget">
		{{csrf_field()}}
		<div class="info_box_p" >
			<p>パスワードをお忘れの方は、ご登録のメールアドレスを下記フォームに入力してください。<br />
			折り返しご登録のメールアドレスにパスワードを送信します。</p>
		</div>
		<div align="center"> <span>▼ご登録のメールアドレス▼</span><br />
			<input name="email" type="text" class="" value="" size="45" />
		</div>
		<p>「送信」をクリックすると、GOLDEN★STARSへ送信されます。</p>
		
		<p class="btn_red_02 size_S"><input type="submit" value="送信" tabindex="3"></p>
	</form>
	<div>{{ $send_msg }}</div>
</div>
</section>

</main>
@endsection