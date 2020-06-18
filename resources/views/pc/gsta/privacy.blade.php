@include('layouts.header')
@extends('layouts.entry_base')
@extends('layouts.body_top')
@section('entry')
	@if( !empty($contents) )
		{!! $contents !!}
	@else
		マスタ管理の文言出力設定から<br />コンテンツを設定してください
	@endif
@endsection