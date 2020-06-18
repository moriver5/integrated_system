<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('const.html_admin_title') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body>
<br />
<center>
<div class="container" style="width:100%;">
    <div class="col">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>検索設定</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">
                    <form id="formSearchSetting" class="form-horizontal" method="POST">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="97%">
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;width:110px;">決済種別</td>
											<td style="padding:5px;" colspan="2">
												<!-- 検索タイプ -->
												@foreach($list_pay_type as $index => $name)
													&nbsp;&nbsp;<input type="checkbox" name="pay_type" value="{{ $index }}" checked> {{ $name }}&nbsp;
												@endforeach
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">登録日時</td>
											<td style="padding:5px;" colspan="2">
												@if( !empty($session['start_regdate']) )
													&nbsp;&nbsp;<input id="start_regdate" type="text" name="start_regdate" value="{{$session['start_regdate']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_regdate" type="text" name="start_regdate" placeholder="開始日時">
												@endif
												@if( !empty($session['end_regdate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_regdate" type="text" name="end_regdate" value="{{$session['end_regdate']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_regdate" type="text" name="end_regdate" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">決済日時</td>
											<td style="padding:5px;" colspan="2">
												@if( !empty($session['start_paydate']) )
													&nbsp;&nbsp;<input id="start_paydate" type="text" name="start_paydate" value="{{$session['start_paydate']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_paydate" type="text" name="start_paydate" placeholder="開始日時">
												@endif
												@if( !empty($session['end_paydate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_paydate" type="text" name="end_paydate" value="{{$session['end_paydate']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_paydate" type="text" name="end_paydate" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">決済回数</td>
											<td style="padding:5px;" colspan="2">
												@if( !empty($session['start_paynum']) )
													&nbsp;&nbsp;<input type="text" name="start_paynum" value="{{ $session['start_paynum'] }}">
												@else
													&nbsp;&nbsp;<input type="text" name="start_paynum">
												@endif
												@if( !empty($session['end_paynum']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_paynum" value="{{ $session['end_paynum'] }}">&nbsp;回
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_paynum">&nbsp;回
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;width:110px;">広告コード</td>
											<td style="padding:5px;">
												@if( !empty($session['ad_cd']) )
													&nbsp;&nbsp;<input type="text" name="ad_cd" value="{{ $session['ad_cd'] }}" size="35">
												@else
													&nbsp;&nbsp;<input type="text" name="ad_cd" value="" size="35">
												@endif
											</td>
											<td style="padding:5px;">
												<select name="search_like_type" class="form-control">
												@foreach($search_like_type as $index => $line)
													@if( !empty($session['search_like_type']) && $index == $session['search_like_type'] )
														<option value="{{ $index }}" selected>{{ $line[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $line[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="search_setting">検索</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
</center>

<form name="formSearchExport" class="form-horizontal" method="POST" action="/admin/member/client/search/export">
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

<script type="text/javascript">
$(document).ready(function(){
	$('[name=start_regdate]').focusin(function(){
		$('[name=start_regdate]').attr("placeholder","");
	});

	$('[name=start_regdate]').focusout(function(){
		$('[name=start_regdate]').attr("placeholder","開始日時");
	});
	
	$('[name=end_regdate]').focusin(function(){
		$('[name=end_regdate]').attr("placeholder","");
	});

	$('[name=end_regdate]').focusout(function(){
		$('[name=end_regdate]').attr("placeholder","終了日時");
	});

	$('[name=start_paydate]').focusin(function(){
		$('[name=start_paydate]').attr("placeholder","");
	});

	$('[name=start_paydate]').focusout(function(){
		$('[name=start_paydate]').attr("placeholder","開始日時");
	});

	$('[name=end_paydate]').focusin(function(){
		$('[name=end_paydate]').attr("placeholder","");
	});

	$('[name=end_paydate]').focusout(function(){
		$('[name=end_paydate]').attr("placeholder","終了日時");
	});

	$.datetimepicker.setLocale('ja');

	//登録日時-開始日
	$('#start_regdate').datetimepicker();

	//登録日時-終了日
	$('#end_regdate').datetimepicker();
	
	//入金日時-開始日
	$('#start_paydate').datetimepicker();

	//入金日時-終了日
	$('#end_paydate').datetimepicker();

	//ダブルクリックで現在時刻入力
	$("#start_regdate").dblclick(function () {
	  $('[name=start_regdate]').val(dateFormat.format(new Date(), 'yyyy/MM/dd hh:mm'));
	});

	$("#end_regdate").dblclick(function () {
	  $('[name=end_regdate]').val(dateFormat.format(new Date(), 'yyyy/MM/dd hh:mm'));
	});

	$("#start_paydate").dblclick(function () {
	  $('[name=start_paydate]').val(dateFormat.format(new Date(), 'yyyy/MM/dd hh:mm'));
	});

	$("#end_paydate").dblclick(function () {
	  $('[name=end_paydate]').val(dateFormat.format(new Date(), 'yyyy/MM/dd hh:mm'));
	});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	/*
	 * 親ウィンドウ側のフォーム値を設定し検索を行う
	 */
	//検索ボタン押下
	$('#search_setting').on('click', function(){	
		//親ウィンドウのフォームオブジェクトを取得
		var fm = window.opener.document.formSearch;

		//決済種別
        fm.pay_type.value = $('[name="pay_type"]:checked').map(function(){
            return $(this).val();
        }).get();
		
		//登録日時-開始
		fm.start_regdate.value = $('[name="start_regdate"]').val();

		//登録日時-終了
		fm.end_regdate.value = $('[name="end_regdate"]').val();

		//入金日時-開始
		fm.start_paydate.value = $('[name="start_paydate"]').val();

		//入金日時-終了
		fm.end_paydate.value = $('[name="end_paydate"]').val();

		//入金回数-開始
		fm.start_paynum.value = $('[name="start_paynum"]').val();

		//入金回数-終了
		fm.end_paynum.value = $('[name="end_paynum"]').val();

		//広告コード
		fm.ad_cd.value = $('[name="ad_cd"]').val();

		//LIKE検索項目
		fm.search_like_type.value = $('[name="search_like_type"]').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});
});
</script>

</body>
</html>
