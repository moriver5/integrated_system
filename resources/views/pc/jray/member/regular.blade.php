@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">情報公開</h1>

<div class="cont">
<section>
	@if( !empty($db_data->html_body) )
		{!! $db_data->html_body !!}
	@endif
</section>
</div>
@endsection
