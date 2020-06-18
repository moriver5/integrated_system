@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top')
@section('entry')

<div id="container">
<main class="wide">
<h1 class="ttl_01">お問い合わせ</h1>
<div class="area_01">
<form action="/info/send" method="post">
	{{ csrf_field() }}
<ul>
@if($errors->has('subject'))
<li>{{ $errors->first('subject') }}</li>
@endif
@if($errors->has('email'))
<li>{{ $errors->first('email') }}</li>
@endif
@if($errors->has('contents'))
<li>{{ $errors->first('contents') }}</li>
@endif
</ul>
<input type="hidden" name="subject" value="サイトフォームからのお問い合わせ">
<table class="tbl_01">
<tr>
<th>メールアドレス</th>
<td><input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレスを入力(半角英数字)"></td>
</tr>
<tr>
<th>お問い合わせ内容</th>
<td><textarea name="contents" rows="15" placeholder="エラーや、不具合に関するお問合せはご利用環境の情報をなるべく詳細にご入力下さい。">{{ old('contents') }}</textarea></td>
</tr>
</table>
<p class="btn_01"><input type="submit" value="確認する"></p>
</form>
</div><!--/.area_01-->
</main>
</div><!--/#container-->

@endsection