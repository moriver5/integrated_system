@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">メールBOX</h1>
<div class="area_01">
<dl class="box_02">

	@if( !empty($db_data) )
		<dt><span class="date">{{ preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/", "$1/$2/$3 $4:$5:$6", $db_data->send_date) }}</span></dt>
		<dd>{{ $db_data->subject }}</dd>
		<dd>
			@if( empty($db_data->html_body) )
				{!! preg_replace("/\n/", '<br />', $db_data->text_body) !!}
			@else
				{!! $db_data->html_body !!}				
			@endif
		</dd>
	@endif
</dl>
</div><!--/.area_01-->
@endsection