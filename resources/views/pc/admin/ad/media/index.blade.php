@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-11 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>媒体集計</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:18%;">
								<b>広告コード</b>
							</td>
							<td class="admin_table" style="width:18%;">
								<b>広告コード名称</b>
							</td>
							<td class="admin_table" style="width:10%;">
								<b>アクセス</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>仮登録者数</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>登録者数</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>退会者数</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>ｱｸﾃｨﾌﾞ数</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>注文数</b>
							</td>
							<td class="admin_table" style="width:7%;">
								<b>購入数</b>
							</td>
							<td class="admin_table" style="width:12%;">
								<b>売上金額</b>
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $ad_cd => $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
									<a href="#" target="_blank" class="ad_link">{{ $lines->ad_cd }}</a>
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->name }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->pv }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->temp_reg }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->reg }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->quit }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->active }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->order_num }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->pay }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->amount }}
									</td>
								</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>	
		</div>	
	</div>	

</div>

<!-- 検索 -->
<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/media/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="start_date" value="">
	<input type="hidden" name="end_date" value="">
	<input type="hidden" name="category" value="">
	<input type="hidden" name="disp_type" value="">
	<input type="hidden" name="action_flg" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 広告コードのリンクをクリックしたら顧客検索へアクセス -->
<form name="formAdSearch" class="form-horizontal" method="POST" action="/admin/member/client/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_type" value="ad_cd">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="0">
	<input type="hidden" name="search_disp_num" value="0">
	<input type="hidden" name="sort" value="0">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/ad/media/search/setting', 'convert_table', 'width=680, height=380');
		return false;
	});

	//クライアント検索
	$('.ad_link').on('click', function(){
		var fm = document.formAdSearch;

		//広告コードのリンクテキストを取得
		fm.search_item.value = $(this).text();

		fm.target = '_blank';

		//検索を行う
		fm.submit();
		return false;
	});
});
</script>

@endsection
