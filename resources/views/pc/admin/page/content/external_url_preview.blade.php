<div style="margin-top:100px;padding:1px;text-align:center;font:normal 16px/160% 'メイリオ',sans-serif;">
外部URLへリンクされています<br />
リンク先URL：<a href="{{ $html_body }}" target="_blank">{{ $html_body }}</a><br />
@if( !empty($img_url) )
バナー：<img src="/images/{{ $img_url }}?ver={{ $ver }}">
@else
バナー：設定されていません。
@endif
</div>