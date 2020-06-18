@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">各種お問い合わせ</h1>
<p class="area_01 mb_L">
退会をご希望の方は「退会希望」と記入の上メールを送信して下さい。<br>
ご登録のメールアドレス宛てにご返信致します。<br>
エラーや不具合等に関するお問い合わせについては、具体的に内容をご入力下さい。</p>
<form action="{{ config('const.member_info_confirm_path') }}" method="post">
{{csrf_field()}}
<input type="hidden" name="subject" value="サイトフォームからのお問い合わせ">
<h2 class="ttl_02">お問い合わせ内容</h2>
@if($errors->has('subject'))
	<p>{{ $errors->first('subject') }}</p>
@endif
@if($errors->has('contents'))
	<p>{{ $errors->first('contents') }}</p>
@endif
<div class="area_01">
<textarea name="contents" rows="10" cols="10" maxlength={{ config('const.contents_length') }} placeholder="エラーや、不具合に関するお問合せはご利用環境の情報をなるべく詳細にご入力下さい。" required></textarea>
</div>
<p class="btn_02"><input type="submit" value="確認する"></p>
</form>
@endsection