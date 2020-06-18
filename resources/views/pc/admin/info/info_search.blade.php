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
											<td style="text-align:center;background:wheat;font-weight:bold;width:110px;">顧客ID</td>
											<td style="padding:5px;">											
												<!-- 検索タイプの値 -->
												@if( !empty($session['info_search_item']) )
													<input type="text" name="search_item" value="{{$session['info_search_item']}}" size="20" placeholder="コンマ(,)で複数設定可能" class="form-control">
												@else
													<input type="text" name="search_item" value="" size="20" placeholder="コンマ(,)で複数設定可能" class="form-control">
												@endif
											</td>
											<td style="width:115px;padding:5px;">
												<!-- LIKE検索-->
												<select name="search_like_type" class="form-control">
												@foreach($search_like_type as $index => $line)
													@if( !empty($session['info_search_like_type']) && $index == $session['info_search_like_type'] )
														<option value="{{ $index }}" selected>{{ $line[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $line[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">受信日時</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['info_start_receive_date']) )
													&nbsp;&nbsp;<input id="start_receive_date" type="text" name="start_receive_date" value="{{$session['info_start_receive_date']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_receive_date" type="text" name="start_receive_date" placeholder="開始日時">
												@endif
												@if( !empty($session['info_end_receive_date']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_receive_date" type="text" name="end_receive_date" value="{{$session['info_end_receive_date']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_receive_date" type="text" name="end_receive_date" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">返信日時</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['info_start_reply_date']) )
													&nbsp;&nbsp;<input id="start_reply_date" type="text" name="start_reply_date" value="{{$session['info_start_reply_date']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_reply_date" type="text" name="start_reply_date" placeholder="開始日時">
												@endif
												@if( !empty($session['info_end_reply_date']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_reply_date" type="text" name="end_reply_date" value="{{$session['info_end_reply_date']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_reply_date" type="text" name="end_reply_date" placeholder="終了日時">
												@endif
												&nbsp;
												@if( !empty($session['info_reply_flg']) )
													<input type="checkbox" name="reply_flg" value="1" checked>&nbsp;返信あり
												@else
													<input type="checkbox" name="reply_flg" value="1">&nbsp;返信あり
												@endif											</td>
										</tr>
<!--
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['info_reply_flg']) )
													&nbsp;&nbsp;<input type="radio" name="reply_flg" value="0">&nbsp;返信なし&nbsp;
													<input type="radio" name="reply_flg" value="1" checked>&nbsp;返信あり
												@else
													&nbsp;&nbsp;<input type="radio" name="reply_flg" value="0" checked>&nbsp;返信なし&nbsp;
													<input type="radio" name="reply_flg" value="1">&nbsp;返信あり
												@endif
												<br />
											</td>
										</tr>
-->
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">表示件数</td>
											<td colspan="3" style="padding:5px;">
												&nbsp;&nbsp;<select name="search_disp_num" style="padding:3px;">
												@foreach($search_disp_num as $index => $num)
													@if( isset($session['info_search_disp_num']) && $session['info_search_disp_num'] == $index )
														<option value="{{ $index }}" selected>{{ $num }}</option>
													@elseif( $index == 0 )
														<option value="{{ $index }}" selected>{{ $num }}</option>													
													@else
														<option value="{{ $index }}">{{ $num }}</option>													
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

<script type="text/javascript">
$(document).ready(function(){
	$('[name=search_item]').focusin(function(){
		$('[name=search_item]').attr("placeholder","");
	});

	$('[name=search_item]').focusout(function(){
		$('[name=search_item]').attr("placeholder","コンマ(,)で複数設定可能");
	});
	
	$('[name=start_repla_date]').focusin(function(){
		$('[name=start_reply_date]').attr("placeholder","");
	});

	$('[name=start_reply_date]').focusout(function(){
		$('[name=start_reply_date]').attr("placeholder","開始日時");
	});
	
	$('[name=end_reply_date]').focusin(function(){
		$('[name=end_reply_date]').attr("placeholder","");
	});

	$('[name=end_reply_date]').focusout(function(){
		$('[name=end_reply_date]').attr("placeholder","終了日時");
	});
	
	$('[name=start_receive_date]').focusin(function(){
		$('[name=start_receive_date]').attr("placeholder","");
	});

	$('[name=start_receive_date]').focusout(function(){
		$('[name=start_receive_date]').attr("placeholder","開始日時");
	});
	
	$('[name=end_receive_date]').focusin(function(){
		$('[name=end_receive_date]').attr("placeholder","");
	});

	$('[name=end_receive_date]').focusout(function(){
		$('[name=end_receive_date]').attr("placeholder","終了日時");
	});
	
	$.datetimepicker.setLocale('ja');

	//受信日時-開始日
	$('#start_reply_date').datetimepicker();

	//受信日時-終了日
	$('#end_reply_date').datetimepicker();
	
	//返信日時-開始日
	$('#start_receive_date').datetimepicker();

	//返信日時-終了日
	$('#end_receive_date').datetimepicker();

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

		//検索項目の値
		fm.search_item.value = $('[name="search_item"]').val();

		//LIKE検索項目
		fm.search_like_type.value = $('[name="search_like_type"]').val();

		//受信日時-開始
		fm.start_reply_date.value = $('[name="start_reply_date"]').val();

		//受信日時-終了
		fm.end_reply_date.value = $('[name="end_reply_date"]').val();

		//返信日時-開始
		fm.start_receive_date.value = $('[name="start_receive_date"]').val();

		//返信日時-終了
		fm.end_receive_date.value = $('[name="end_receive_date"]').val();

		//返信あり
		if( $('[name="reply_flg"]:checked').val() != undefined ){
			fm.reply_flg.value = $('[name="reply_flg"]:checked').val();
		}

		//表示件数
		fm.search_disp_num.value = $('[name="search_disp_num"]').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});
});
</script>

</body>
</html>
