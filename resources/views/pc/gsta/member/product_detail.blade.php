@extends('layouts.member_base')
@section('member_content')

{!! !empty($product_detail[0]['contents']) ? $product_detail[0]['contents']:'' !!}

<h2 class="ttl_02">プラン詳細</h2>
<div class="area_01">
<table class="tbl_02">
<tr>
<th>ご提供鞍数</th>
<td>{{ !empty($product_detail[0]['saddle']) ? $product_detail[0]['saddle']:'' }}</td>
</tr>
<tr>
<th>券種</th>
<td>{{ !empty($product_detail[0]['tickets']) ? $product_detail[0]['tickets']:'' }}</td>
</tr>
<tr>
<th>ご提供人数</th>
<td>先着{{ !empty($product_detail[0]['quantity']) ? $product_detail[0]['quantity']:'' }}名様</td>
</tr>
<tr>
<th>ご提供価格</th>
<td><span class="f_red f_bold">{{ !empty($product_detail[0]['discount']) ? $product_detail[0]['discount']:'' }}円</span></td>
</tr>
</table>
@if( !empty($product_detail[0]['is_sold_out']) && $product_detail[0]['is_sold_out'] === true )
<p class="btn_02"><a tabindex="-1">完売</a></p>
@else
<p class="btn_02"><a href="/member/settlement/{{ !empty($product_detail[0]['id']) ? $product_detail[0]['id']:'' }}">情報を購入する</a></p>
@endif
</div><!--/.area_01-->
</section>
@endsection