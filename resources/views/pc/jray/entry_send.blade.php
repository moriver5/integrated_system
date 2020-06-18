@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top_index')
@section('entry')
<div id="container">
<main class="wide">
<h1 class="ttl_01">会員登録</h1>
<div class="area_01">
<div class="box_01 mb_LL wd_90">
<p class="f_bold f_L">メールをご確認ください</p>
</div><!--/.box_01-->
<figure class="wd_90 mb_L"><img src="./images/mailsend_step_image.png"></figure>
<dl class="list_terms">
<dt class="f_wine">まだ本登録は完了していません</dt>
<dd class="mb_LL">折り返しメールが届くので記載されております<br class="view_sp"><span class="f_bold t_line">メール内のURLにアクセス</span>して<br class="view_sp">登録を完了してください。</dd>
<dt class="f_wine">迷惑メールフィルターや受信メールドメイン設定を確認してください</dt>
<dd>もしメールが届いていない場合は<span class="f_bold t_line">迷惑メールフォルダ</span>に入ってしまってる場合もございますので必ずご確認くださいませ！<br>メールが届かない方はお問い合せよりご連絡頂ますようお願いいたします。</dd>
</dl><!--/.list_terms-->
</div><!--/.area_01-->
</main>
</div><!--/#container-->
@endsection