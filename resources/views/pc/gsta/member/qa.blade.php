@extends('layouts.member_base')
@section('member_content')
	@if( !empty($contents) )
		{!! $contents !!}
	@else
		マスタ管理の文言出力設定から<br />コンテンツを設定してください
	@endif
@endsection