@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>購買動向分析</b>
				</div>
                <div class="panel-body">
                    <form id="formSearchSetting" class="form-horizontal" method="POST" action="/admin/member/analytics/purchasing_trends/search">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="97%">
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">購入金額範囲</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['trends_start_pay']) )
													&nbsp;&nbsp;<input id="start_pay" type="text" name="start_pay" value="{{$session['trends_start_pay']}}" placeholder="例：1000">
												@else
													&nbsp;&nbsp;<input id="start_pay" type="text" name="start_pay" placeholder="例：1000">
												@endif
												@if( !empty($session['trends_end_pay']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_pay" type="text" name="end_pay" value="{{$session['trends_end_pay']}}" placeholder="例：8000">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_pay" type="text" name="end_pay" placeholder="例：8000">
												@endif
												<br />
												※ 購入金額範囲Fromの入力がありません<br />
												※ 購入金額範囲Toの入力がありません<br />
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">購入期間</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['trends_start_purchase']) )
													&nbsp;&nbsp;<input id="start_purchase" type="text" name="start_purchase" value="{{ preg_replace("/(\d{4})(\d{2})(\d{2})/","$1/$2/$3",$session['trends_start_purchase']) }}" placeholder="購入開始日">
												@else
													&nbsp;&nbsp;<input id="start_purchase" type="text" name="start_purchase" placeholder="購入開始日">
												@endif
												@if( !empty($session['trends_end_purchase']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_purchase" type="text" name="end_purchase" value="{{ preg_replace("/(\d{4})(\d{2})(\d{2})/","$1/$2/$3",$session['trends_end_purchase']) }}" placeholder="購入終了日">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_purchase" type="text" name="end_purchase" placeholder="購入終了日">
												@endif
												<br />
												※ 購入期間Fromは有効な日付ではありません<br />
												※ 購入期間Toは有効な日付ではありません<br />
											</td>
										</tr>
<!--
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">集計最終日</td>
											<td colspan="3" style="padding:5px;">
												（範囲は100日以内）
												@if( !empty($session['last_date']) )
													&nbsp;&nbsp;<input id="last_date" type="text" name="last_date" value="{{$session['last_date']}}" placeholder="">
												@else
													&nbsp;&nbsp;<input id="last_date" type="text" name="last_date" placeholder="">
												@endif
												<br />
												※ 集計最終日は有効な日付ではありません
											</td>
										</tr>
-->
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">購入人数</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($db_count) )
												{{ $db_count }}
												@else
												0
												@endif
												件
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="search_setting">検索</button>
								<button type="submit" class="btn btn-primary" id="data_export">顧客別CSV出力</button>
								<button type="submit" class="btn btn-primary" id="data_paycount_export">購入回数別CSV出力</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
</center>

<form name="formSearchExport" class="form-horizontal" method="POST" action="/admin/member/analytics/purchasing_trends/search/export">
	{{ csrf_field() }}
	<input type="hidden" name="start_pay" value="">
	<input type="hidden" name="end_pay" value="">
	<input type="hidden" name="start_purchase" value="">
	<input type="hidden" name="end_purchase" value="">
	<input type="hidden" name="last_date" value="">
</form>

<form name="formSearchPayCountExport" class="form-horizontal" method="POST" action="/admin/member/analytics/purchasing_trends/search/export/paycount">
	{{ csrf_field() }}
	<input type="hidden" name="start_pay" value="">
	<input type="hidden" name="end_pay" value="">
	<input type="hidden" name="start_purchase" value="">
	<input type="hidden" name="end_purchase" value="">
	<input type="hidden" name="last_date" value="">
</form>

<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('[name=start_pay]').focusin(function(){
		$('[name=start_pay]').attr("placeholder","");
	});

	$('[name=start_pay]').focusout(function(){
		$('[name=start_pay]').attr("placeholder","例：1000");
	});
	
	$('[name=end_pay]').focusin(function(){
		$('[name=end_pay]').attr("placeholder","");
	});

	$('[name=end_pay]').focusout(function(){
		$('[name=end_pay]').attr("placeholder","例：8000");
	});
	
	$('[name=start_purchase]').focusin(function(){
		$('[name=start_purchase]').attr("placeholder","");
	});

	$('[name=start_purchase]').focusout(function(){
		$('[name=start_purchase]').attr("placeholder","購入開始日");
	});

	$('[name=end_purchase]').focusin(function(){
		$('[name=end_purchase]').attr("placeholder","");
	});

	$('[name=end_purchase]').focusout(function(){
		$('[name=end_purchase]').attr("placeholder","購入終了日");
	});
	
	$('[name=last_date]').focusin(function(){
		$('[name=last_date]').attr("placeholder","");
	});

	$('[name=last_date]').focusout(function(){
		$('[name=last_date]').attr("placeholder","");
	});
	
	$.datetimepicker.setLocale('ja');
	
	//
	$('#start_purchase').datetimepicker({
		format:'Y/m/d',
		timepicker:false
	});

	//
	$('#end_purchase').datetimepicker({
		format:'Y/m/d',
		timepicker:false
	});

	//
	$('#last_date').datetimepicker();

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

	/*
	 * 検索条件を元に顧客データのエクスポートを行う
	 */
	//顧客データのエクスポートボタン押下
	$('#data_export').on('click', function(){	
		//親ウィンドウのフォームオブジェクトを取得
		var fm = document.formSearchExport;

		//登録日時-開始
		fm.start_pay.value = $('[name="start_pay"]').val();

		//登録日時-終了
		fm.end_pay.value = $('[name="end_pay"]').val();

		//仮登録日時-開始
		fm.start_purchase.value = $('[name="start_purchase"]').val();

		//仮登録日時-終了
		fm.end_purchase.value = $('[name="end_purchase"]').val();

		//顧客データのエクスポートを行う
		fm.submit();

		return false;
	});

	$('#data_paycount_export').on('click', function(){	
		//親ウィンドウのフォームオブジェクトを取得
		var fm = document.formSearchPayCountExport;

		//登録日時-開始
		fm.start_pay.value = $('[name="start_pay"]').val();

		//登録日時-終了
		fm.end_pay.value = $('[name="end_pay"]').val();

		//仮登録日時-開始
		fm.start_purchase.value = $('[name="start_purchase"]').val();

		//仮登録日時-終了
		fm.end_purchase.value = $('[name="end_purchase"]').val();

		//顧客データのエクスポートを行う
		fm.submit();

		return false;
	});

});
</script>

@endsection
