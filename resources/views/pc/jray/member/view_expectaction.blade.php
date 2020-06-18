@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">
	@if( $category == 2 )
	情報公開
	@else
	無料予想・ポイント予想
	@endif
</h1>
<div class="area_01">
<dl class="box_02">
<dt>キャンペーン名</dt>
<dd>{{ $db_data->title }}</dd>
<dt>キャンペーン詳細</dt>
<dd>{{ $db_data->comment }}</dd>
<dt>内容</dt>
<dd>{!! preg_replace("/\n/", "<br />", $db_data->detail) !!}</dd>
</dl><!--/.box_02-->
</div><!--/.area_01-->
@endsection