@extends('layouts.member_base')
@section('member_content')

{!! $product_detail[0]['contents'] !!}

<h2 class="ttl_02">プラン詳細</h2>
<div class="area_01">
<table class="tbl_02">
<tr>
<th>ご提供鞍数</th>
<td>{{ $convert_data['-%tipster_saddles-'] }}</td>
</tr>
<tr>
<th>券種</th>
<td>{{ $convert_data['-%tipster_type-'] }}</td>
</tr>
<tr>
<th>ご提供人数</th>
<td>先着{{ $product_detail[0]['quantity'] }}名様</td>
</tr>
<tr>
<th>ご提供価格</th>
<td><span class="f_red f_bold">{{ $product_detail[0]['discount'] }}円</span></td>
</tr>
</table>
<p class="btn_02"><a href="/member/settlement/{{ $product_detail[0]['id'] }}">情報を購入する</a></p>
</div><!--/.area_01-->
</section>
@endsection