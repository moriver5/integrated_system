@extends('layouts.member_base')
@section('member_content')
<h1 class="ttl_01">メールBOX</h1>
<div class="area_01">
<dl class="box_02">
<dt><span class="date">受信日時：{{ preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/", "$1/$2/$3 $4:$5:$6", $db_data->reply_date) }}</span></dt>
<dd>{{ e($db_data->subject) }}</dd>
<dd>{!! preg_replace("/\n/", '<br />', e($db_data->msg)) !!}</dd>
</dl>
</div><!--/.area_01-->
@endsection