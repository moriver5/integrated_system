@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">{{ $db_data->title }}</h1>
<div class="area_01">
<figure class="mb_M"><img src="/images/top_content/{{ $db_data->img }}" alt="重賞有力情報"></figure>
<dl class="box_02">
<dt>情報内容</dt>
	@if( !empty($db_data->html_body) )
		{!! $db_data->html_body !!}
	@endif
</dl><!--/.box_02-->
</div><!--/.area_01-->
@endsection
