@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top_index')

@section('entry')
<div id="top_container">
<section class="block_main">
<div class="main_visual animated rubberBand">
<h2><img src="/images/top_main_anticipator_ttl.png" alt="前回の3つ星予想師"></h2>
@if( empty($tipster->id) )
<figure class="anticipator"><img src="./images/top_main_anticipator_image01.png" alt="予想師"></figure>
@else
<figure class="anticipator"><img src="/images/tipster/{{ $tipster->id }}_hit.png" alt="予想師"></figure>
@endif
<p class="pay">{{ $list_convert_data['-%month_dividend-'] }}</p>
</div><!--/.main_visual-->
</section><!--/.block_main-->

<section class="block_form form1">
<div class="inner">
<h2><img src="./images/top_logo.png" alt="ゴールデン★スターズ"></h2>
<p class="tx">土日はもちろん、平日も厳選情報無料公開中!</p>
<div class="area_form">
<form action="{{ url('regist') }}" method="post">
{{ !empty(csrf_field()) ? csrf_field():''}}
@if($errors->has('email'))
{{ $errors->first('email') }}
@endif
<p class="user_address"><input type="email" name="email" maxlength="{{ config('const.email_length') }}" placeholder="メールアドレスを入力(半角英数字)"></p>
<p class="btn_regist"><input type="submit" value="限定無料登録"></p>
<input type="hidden" name="ad_cd" value="{{!empty($ad_cd) ? $ad_cd:""}}">
</form>
<ul class="list_notes">
<li>※利用規約とメール配信に同意の上ご登録下さい。</li>
<li>※ご登録に個人情報（性別・年齢・住所・職業・電話番号）は必要ありません。</li>
<li>※携帯でご登録の方はメールが確実に届かれますよう事前に【+ドメイン+】の指定解除受信設定をお願い致します。</li>
</ul><!--/.list_notes-->
</div><!--/.area_form-->
</div><!--/,inner-->
</section><!--/.block_form-->
    
<section class="block_race">
<div class="inner js_inview animated_fadeInUp">
@if( !empty($sp_race_data) )
{!! $sp_race_data !!}
@endif
</div><!--/.inner-->
</section><!--/.block_race-->
    
<section class="block_privilege">
<div class="inner">
<h2 class="ttl_frame js_inview animated_fadeInUp">当サイトにしかできない「３大特典」</h2>
<ul class="list_privilege">
<li class="js_inview animated_fadeInRightBound"><dl>
<dt><img src="./images/top_privilege_01.png" alt="特典01.的中保証情報付き"></dt>
<dd>万が一、提供情報が推奨投資額にてTOTAL20万円以下だった場合、資金を回収できるまで無期限で補填情報を提供致します。</dd>
</dl></li>
<li class="js_inview animated_fadeInRightBound" data-delay="500"><dl>
<dt><img src="./images/top_privilege_02.png" alt="特典02.返金保証"></dt>
<dd>ご提供のキャンペーンにて合計獲得配当金が20万円以下だった場合、ご参加費用全額分をPTでお返しします。</dd>
</dl></li>
<li class="js_inview animated_fadeInRightBound" data-delay="800"><dl>
<dt><img src="./images/top_privilege_03.png" alt="特典03.ご登録で1万円分"></dt>
<dd>今ご登録で1万円分PTをプレゼント！無料情報だけでなく無料で有料情報もご利用頂けるのでお気軽に始められます。</dd>
</dl></li>
</ul><!--/.list_privilege-->
</div><!--/.inner-->
</section><!--/.block_privilege-->
    
<section class="block_hitresult">
<h2 class="js_inview animated_fadeInUp">主な的中実績</h2>
<ul class="list_hitresult slick_slider js_inview animated_fadeInUp">
@if( count($list_hit_data) > 0 ){
@foreach($list_hit_data as $lines)
@if( !empty($lines['img']) )
<li>
<p class="date">{{ $lines['date'] }}</p>
<figure><img src="/images/top_content/{{ $lines['img'] }}" alt="予想師"></figure>
<p class="pay">￥{{ number_format($lines['msg1']) }}</p>
<p class="campaign">『{{ $lines['tipster'] }}』</p>
<p class="race">{{ $lines['name'] }} {{ $lines['msg2'] }}</p>
</li>
@else
<li>
<p class="date">{{ $lines['date'] }}</p>
<figure><img src="/images/tipster/{{ $lines['tipster_id'] }}_hit.png" alt="予想師"></figure>
<p class="pay">￥{{ number_format($lines['msg1']) }}</p>
<p class="campaign">『{{ $lines['tipster'] }}』</p>
<p class="race">{{ $lines['name'] }} {{ $lines['msg2'] }}</p>
</li>
@endif
@endforeach
@endif
</ul><!--/.list_hitresult-->
</section><!--/.block_hitresult-->

<section class="block_research">
<div class="inner">
<h2 class="ttl_frame js_inview animated_fadeInUp">会員様の満足度調査結果</h2>
<div class="research_wrap">
<figure class="js_inview animated_fadeInUp"><img src="./images/top_research_graph.png" alt="調査結果"></figure>
<div class="research_detail js_inview animated_fadeInUp">
<ul class="evalitem">
<li><span>■</span>非常に満足‥57％</li>
<li><span>■</span>満足‥33％</li>
<li><span>■</span>不満‥5.8.％</li>
<li><span>■</span>非常に不満‥4.2％</li>
</ul>
<p>既に多くの会員様よりご満足のお声を頂いておりますが、ご利用頂いたからには全ての会員様にご満足頂けるよう、精度の向上に尽力しております。</p>
<p class="date">※<span class="js_date">2020年3月8日</span>現在</p>
</div><!--/.research_detail-->
</div><!--/.research_wrap-->
</div><!--/.inner-->
</section><!--/.block_research-->

<section class="block_reason">
<div class="inner">
<h2 class="ttl_frame js_inview animated_fadeInUp">なぜ当サイトが選ばれるのか？</h2>
<figure class="view_pc js_inview animated_fadeInUp"><img src="./images/all_anticipator_wide.png" alt="予想師"></figure>
<figure class="view_sp js_inview animated_fadeInUp"><img src="./images/all_anticipator_narrow.png" alt="予想師"></figure>
<ul class="list_reason">
<li class="js_inview animated_fadeInRightBound"><dl>
<dt><span>01</span><p>国内トップレベルの予想師</p></dt>
<dd>全ての予想師は、元競馬関係者や、業界の裏事情に精通する精鋭のみ。
結果をお届けしてきた予想師しか取り揃えておりません。「惜しかった」や「今回は不的中でした」等の言い訳は一切いたしません。「結果が全て」です。</dd>
</dl></li>
<li class="js_inview animated_fadeInRightBound" data-delay="500"><dl>
<dt><span>02</span><p>確実な実績</p></dt>
<dd>ゴールデン★スターズの予想師になる条件は、一定以上の成績を上げる事。何百、何千もの予想をし、実際に的中した実績のある者しか予想師になる事は許されません。万が一ご満足頂ける結果をお届けできなかった予想師は、以後の弊社との契約を打ち切ります。</dd>
</dl></li>
<li class="js_inview animated_fadeInRightBound" data-delay="800"><dl>
<dt><span>03</span><p>万全のサポート体制</p></dt>
<dd>ゴールデン★スターズは安定増資を確実に実現する事を目的とした方を対象としております。つまりは競馬というより投資という考え方でご利用頂きたく思っております。
競馬の知識が全くなくとも、安定増資をを確実に実現できるよう、万全のサポートを心がけております。</dd>
</dl></li>
</ul><!--/.list_reason-->
</div><!--/.inner-->
</section><!--/.block_reason-->

<section class="block_voice">
<div class="inner">
<h2 class="ttl_frame js_inview animated_fadeInUp">お喜びの声も多数</h2>
<ul class="list_voice">
@if( !empty($list_voice) && count($list_voice) > 0 )
@foreach($list_voice as $lines)
<li class="js_inview animated_fadeInUp"><dl><dt>投稿日時　{{ $lines['post_date'] }}</dt><dd>{{ $lines['msg'] }}</dd></dl></li>
@endforeach
@endif
</ul><!--/.list_voice-->
</div><!--/.inner-->
</section><!--.block_voice-->

<section class="block_form">
<div class="inner js_inview animated_fadeInUp">
<h2>土日はもちろん、平日も厳選情報無料公開中!</h2>
<div class="area_form">
<form action="{{ url('regist') }}" method="post">
{{ !empty(csrf_field()) ? csrf_field():''}}
@if($errors->has('email'))
{{ $errors->first('email') }}
@endif
<p class="user_address"><input type="email" name="email" maxlength="{{ config('const.email_length') }}" placeholder="メールアドレスを入力(半角英数字)"></p>
<p class="btn_regist"><input type="submit" value="限定無料登録"></p>
<input type="hidden" name="ad_cd" value="{{!empty($ad_cd) ? $ad_cd:""}}">
</form>
<ul class="list_notes">
<li>※利用規約とメール配信に同意の上ご登録下さい。</li>
<li>※ご登録に個人情報（性別・年齢・住所・職業・電話番号）は必要ありません。</li>
<li>※携帯でご登録の方はメールが確実に届かれますよう事前に【{{ $list_convert_data['-%domain-'] }}】の指定解除受信設定をお願い致します。</li>
</ul><!--/.list_notes-->
</div><!--/.area_form-->
</div><!--/,inner-->
</section><!--/.block_form-->

<section class="block_qa">
<div class="inner">
<h2 class="ttl_frame js_inview animated_fadeInUp">よくある質問</h2>
<dl class="list_qa">
<dt class="js_inview animated_fadeInUp">Q.ゴールデン★スターズとは何ですか？</dt>
<dd>A.国内トップレベルの予想師が、国内最高精度の情報をご提供している競馬情報サイトです。</dd>
<dt class="js_inview animated_fadeInUp">Q.入会金、年会費はかかりますか？</dt>
<dd>A.入会金、年会費は一切必要ございません。無料登録して頂きますと、毎週無料情報をご覧頂けます。</dd>
<dt class="js_inview animated_fadeInUp">Q.メールが届かない</dt>
<dd>A. ごくまれにフィルタリング機能等によって、迷惑メールフォルダに入ってしまう事や、配信されない場合がございます。<br><br>
<span class="f_bold">■登録メールが届かない場合</span><br>
「お問い合わせ」より、ご連絡頂ければ本登録にさせて頂きます。<br><br>
<span class="f_bold">■メールマガジンや情報公開メールが届かない場合</span><br>
まずは「迷惑メールフォルダ」をご確認の上、当サイトからのメールを「迷惑メールではない」設定に変更してください。<br><br>
<span class="f_bold">■「迷惑メールフォルダ」にも受信されていない場合</span><br>
お使いの携帯電話等の迷惑メールフィルター設定（ドメイン指定受信等）をご確認ください。ご確認頂いても改善されない場合は、お使いの携帯電話会社様等にお問い合わせください。</dd>
<dt class="js_inview animated_fadeInUp">Q.どうやったら退会できますか？</dt>
<dd>A.『退会希望』とご連絡を頂ければ、退会処理をさせて頂きます。<br>
以後メールマガジンの配信はいたしませんのでご安心ください。システムの都合上、退会ご希望のご連絡を頂いてから一定期間配信される場合がございますが、48時間経過してもメールマガジンが配信される場合は、お手数ですが再度ご連絡ください。</dd>
<dt class="js_inview animated_fadeInUp">Q.商品はどうやったら購入できますか？</dt>
<dd>A.ご購入ご希望の商品が決まりましたら、『商品購入』ページよりご購入のお手続きに進んで頂き、銀行振込、クレジットカード決済にてご決済ください。</dd>
<dt class="js_inview animated_fadeInUp">Q.購入した情報はどのように確認するのでしょうか？</dt>
<dd>A.ログインして頂き、情報公開ページよりご確認頂けます。買い目の公開は、レース前日の17時頃～当日13時を予定しております。プラン等により公開時間が異なりますので、ご購入されたプランの詳細をご確認ください。</dd>
<dt class="js_inview animated_fadeInUp">Q.馬券代はどれくらいかかりますか？</dt>
<dd>A.推奨馬券代は、プラン等にもよりますが、1レース3000円～10000円くらいとお考えください。あくまでも推奨ですので、会員様のご予算に合わせて頂いて結構です。ですが推奨額を大きく上回る金額をご購入頂きますと、オッズの低下が可能性あり、推定配当を手にする事ができない場合がございますので、ご注意くださいませ。</dd>
<dt class="js_inview animated_fadeInUp">Q.必ず儲かりますか？</dt>
<dd>A. 大変申し訳ございませんが競馬において必ず当たると保証できる情報は、世界中探して頂いても存在いたしません。ですが、そんな中でもレース結果を大きく左右する重要な情報は間違いなく存在します。情報元が厳選したレース、そして精査を重ねた情報をご提供する事によって、高額的中が可能となっております。事実、継続利用されている当サイトご登録の会員様の大半が大幅プラス収支となっております。</dd>
</dl><!--/.list_qa-->
</div><!--/.inner-->
</section><!--/.block_qa-->


</div><!--/#top_container-->
@endsection