@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top_index')
@section('entry')
<div id="container">
<main class="wide">
<h1 class="ttl_01">会員登録完了</h1>
<div class="area_01">
<div class="box_01 mb_LL wd_90">
<p>本登録が完了しました<br>下記のログインから<br class="view_sp">会員ページをご利用ください</p>
</div><!--/.box_01-->
<p class="f_wine f_bold mb_L">ゴールデン★スターズでは豊富な無料コンテンツをご用意しております</p>
<dl class="list_terms">
<dt>◼️厳選無料情報（更新日/中央競馬開催前日：通常金曜・土曜）</dt>
<dd>各日１レースずつ厳選された買い目情報を公開します。</dd>
<dt>◼️重賞有力情報（更新日/毎週火曜日）</dt>
<dd>今週の重賞を徹底攻略するための独占情報を公開します。</dd>
<dt>◼️追い切り速報（更新日/毎週木曜日）</dt>
<dd>最新の追い切り情報を公開（無料情報だけではなく、より確実に、圧倒的に稼ぎたい会員様には、各種有料情報もお届けさせて頂いております）</dd>
<dt>◼️ポイント情報</dt>
<dd>当社でご利用頂けるポイントを消費して閲覧可能になる買い目情報。中央競馬開催(通常土曜/日曜)に各日1～4鞍ずつ公開させて頂いております。※ポイントは直接お買い求め頂くか、キャンペーンのご参加特典、新規ご登録特典等で追加されます。</dd>
<dt>◼️キャンペーン情報</dt>
<dd>開催毎に直接購入してご参加頂ける買い目情報<br>
的中＆回収の期待は当社情報の中でも最も高いものとなっております。<br>
当社独自の情報収集とデータ分析、及び当社専属のエージェント達による厳選された買い目情報を公開させて頂いております。圧倒的プラス収支をご希望の方はキャンペーン情報のご購入をお勧め致します。</dd>
</dl><!--/.list_terms-->
</div><!--/.area_01-->
<form action="{!! url($login_url) !!}" method="post">
{{csrf_field()}}
<input type="hidden" name="login_id" value="{{ $login_id }}" />
<input type="hidden" name="password" value="{{ $password }}" />
<p class="btn_01"><input type="submit" value="ログインする"></p>
</form>
</main>
</div><!--/#container-->
@endsection