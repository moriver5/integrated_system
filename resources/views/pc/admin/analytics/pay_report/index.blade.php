@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>決済レポート</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
				</div>
				<form id="formMailDel" class="form-horizontal" method="POST" action="/admin/member/client/del/send">
					{{ csrf_field() }}
				<div class="panel-body">
					<table border="1" align="center" width="100%">
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								決済手段
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>決済回数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>決済ユーザー数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>決済額</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
								<b>付与Pt</b>
							</td>
						</tr>
						@foreach($db_data as $lines)
							<tr>
								<td style="padding:5px;text-align:center;">
									{{ config('const.list_pay_type')[$lines->pay_type] }}
								</td>
								<td style="padding:5px;text-align:center;" class="pay_count">
									{{ $lines->pay_count }}
								</td>
								<td style="padding:5px;text-align:center;" class="pay_user">
									{{ $lines->pay_user_count }}
								</td>
								<td style="padding:5px;text-align:center;" class="amount">
									{{ $lines->pay_amount }}円
								</td>
								<td style="padding:5px;text-align:center;" class="add_pt">
									{{ $lines->total_add_pt }}
								</td>
							</tr>
						@endforeach
							<tr style="background-color:gray;">
								<td style="padding:1px;" colspan="5"></td>
							</tr>
							<tr style="font:normal 12px/120% 'メイリオ',sans-serif;background-color:#ffff99;">
								<td style="padding:3px;text-align:center;font-weight:bold;">
									合計
								</td>
								<td style="padding:3px;text-align:center;" id="pay_count">

								</td>
								<td style="padding:3px;text-align:center;" id="pay_user">

								</td>
								<td style="padding:3px;text-align:center;" id="amount">

								</td>
								<td style="padding:3px;text-align:center;" id="add_pt">

								</td>
							</tr>
	
					</table>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/analytics/payment/report/search/send">
	{{ csrf_field() }}
	<input type="hidden" name="pay_type" value="">
	<input type="hidden" name="start_regdate" value="">
	<input type="hidden" name="end_regdate" value="">
	<input type="hidden" name="start_paydate" value="">
	<input type="hidden" name="end_paydate" value="">
	<input type="hidden" name="start_paynum" value="">
	<input type="hidden" name="end_paynum" value="">
	<input type="hidden" name="ad_cd" value="">
	<input type="hidden" name="search_like_type" value="">

</form>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/analytics/payment/report/search/setting', 'report_search_setting', 'width=620, height=370');
		return false;
	});

	//合計/平均算出のための変数
	var pay_count	 = 0;
	var pay_user	 = 0;
	var amount		 = 0;
	var add_pt		 = 0;
	
	$.when(
		//
		$('.pay_count').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_count += parseInt(count);
		}),
		//
		$('.pay_user').each(function(){
			var count = $(this).text().replace(/,/g,"");
			pay_user += parseInt(count);
		}),
		//
		$('.amount').each(function(){
			var count = $(this).text().replace(/,/g,"");
			count = count.replace(/円/,"");
			amount += parseInt(count);
		}),
		//
		$('.add_pt').each(function(){
			var count = $(this).text().replace(/,/g,"");
			add_pt += parseInt(count);
		})

	).done(function(){
		//合計
		$('#pay_count').text(pay_count.toLocaleString());
		//合計
		$('#pay_user').text(pay_user.toLocaleString());
		//合計
		$('#amount').text(amount.toLocaleString()+"円");
		//合計
		$('#add_pt').text(add_pt.toLocaleString());
	});
});
</script>

@endsection
