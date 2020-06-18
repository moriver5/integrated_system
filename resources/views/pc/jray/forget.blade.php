@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top')
@section('entry')

<div id="container">
<main class="wide">
<h1 class="ttl_01">パスワード再発行</h1>
<div class="area_01">
<div class="box_01 wd_90">
<p class="t_inline"><span>パスワードを再発行しますので</span><span>下記にご登録のメールアドレスを入力して</span><br><span>パスワード再発行ボタンを押してください。</span></p>
</div><!--/.box_01-->
<div>{{ $send_msg }}</div>
<p class="errormsg mb_S">メールアドレスを入力してください</p>
<form action="{{ config('const.forget_send_path') }}" method="post
{{csrf_field()}}
<table class="tbl_01">
<tr>
<th>ご登録のメールアドレス</th>
<td><input type="email" name="mail_address" placeholder="メールアドレスを入力(半角英数字)"></td>
</tr>
</table>
<p class="btn_01"><input type="submit" value="パスワード再発行"></p>
</form>
</div><!--/.area_01-->
</main>
</div><!--/#container-->

@endsection