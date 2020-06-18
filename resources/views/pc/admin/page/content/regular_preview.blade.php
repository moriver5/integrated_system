<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta name="robots" content="noindex,nofollow">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
<meta name="format-detection" content="telephone=no">
<title>{{ config('const.html_title') }}</title>
<link rel="stylesheet" href="/css/common/style.css">
<link rel="stylesheet" href="/css/common/pushy.css">
</head>
<body id="state_in" class="index">
<div id="container">
	<h1 class="ttl_01">情報公開</h1>
	<div class="cont">
	<section>
		@if( !empty($html_body) )
		{!! $html_body !!}
		@else
		<div style="margin-top:10px;padding:1px;text-align:center;font:normal 16px/160% 'メイリオ',sans-serif;">
		コンテンツがありません。<br />
		コンテンツを作成してください。
		</div>
		@endif
	</section>
	</div>
</div>
</body>
</html>
