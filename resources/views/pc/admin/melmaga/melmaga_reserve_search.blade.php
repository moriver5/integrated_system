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
    <link href="{{ asset('css/admin/admin.css') }}" rel="stylesheet" />
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
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>配信先設定</b>
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
											<td class="admin_search" style="width:30px;">抽出項目</td>
											<td style="width:100px;padding:5px;width:40px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($melmaga_search_item as $lines)
													@if( !empty($session['melmaga_search_item']) && $lines[0] == $session['melmaga_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['melmaga_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['melmaga_search_item_value'] }}" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" autofocus>
												@endif
											</td>
											<td style="width:100px;padding:5px;width:40px;">
												<!-- 検索タイプ -->
												<select name="search_type" class="form-control">
												@foreach($melmaga_search_type as $index => $lines)
													@if( !empty($session['melmaga_search_type']) && $index == $session['melmaga_search_type'] )
														<option value="{{ $index }}" selected>{{ $lines[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $lines[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td  class="admin_search">グループ</td>
											<td colspan="3" style="padding:5px;">
												@foreach($db_group_data as $index => $lines)
													@if( $index != 0 && $index % 4 == 0 )
														<br />
													@endif
													@if( !empty($session['melmaga_groups']) && preg_match("/^(".preg_replace("/,/", "|",$session['melmaga_groups']).")$/",$lines[0]) > 0 )
														&nbsp;<input type="checkbox" name="groups" value="{{ $lines[0] }}" checked>{{ $lines[1] }}
													@else
														&nbsp;<input type="checkbox" name="groups" value="{{ $lines[0] }}">{{ $lines[1] }}									
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search">登録状態</td>
											<td colspan="3" style="padding:5px;">
												@foreach($regist_status as $index => $line)
													@if( isset($session['melmaga_status']) && $index == $session['melmaga_status'] )
														&nbsp;&nbsp;<input type="radio" name="status" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="status" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="status" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search">未決済注文</td>
											<td colspan="3" style="padding:5px;">
												@foreach($melmaga_settlement_status as $index => $line)
													@if( isset($session['melmaga_settlement']) && $index == $session['melmaga_settlement'] )
														&nbsp;&nbsp;<input type="radio" name="settlement" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="settlement" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="settlement" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<!--
										<tr>
											<td class="admin_search">送信端末</td>
											<td colspan="3" style="padding:5px 5px 5px 10px;">
												<select name="device">
												@foreach($melmaga_device as $lines)
													@if( !empty($session['melmaga_device']) && $lines[0] == $session['melmaga_device'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
	-->
										<tr>
											<td class="admin_search" style="width:80px;">登録日時</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['melmaga_regist_sdate']) )
													&nbsp;&nbsp;<input id="regist_sdate" type="text" name="regist_sdate" value="{{$session['melmaga_regist_sdate']}}" placeholder="開始表示日時">
												@else
													&nbsp;&nbsp;<input id="regist_sdate" type="text" name="regist_sdate" placeholder="開始表示日時">
												@endif
												@if( !empty($session['melmaga_regist_edate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="regist_edate" type="text" name="regist_edate" value="{{$session['melmaga_regist_edate']}}" placeholder="終了表示日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="regist_edate" type="text" name="regist_edate" placeholder="終了表示日時">
												@endif
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="search_setting">&nbsp;&nbsp;&nbsp;&nbsp;検索&nbsp;&nbsp;&nbsp;&nbsp;</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
</center>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.datetimepicker.setLocale('ja');

	//登録日時-開始日
	$('#regist_sdate').datetimepicker();
	$('#provision_sdate').datetimepicker();

	//登録日時-終了日
	$('#regist_edate').datetimepicker();
	$('#provision_edate').datetimepicker();

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

		//検索項目
		fm.search_item.value = $('[name="search_item"]').val();

		//検索項目
		fm.search_item_value.value = $('[name="search_item_value"]').val();

		//検索項目を含む・含まない
		fm.search_type.value = $('[name="search_type"]').val();

		//カテゴリ
        fm.status.value = $('[name="status"]:checked').val();

		//表示グループ
        fm.groups.value = $('[name="groups"]:checked').map(function(){
            return $(this).val();
        }).get();

		//グループが未チェックの場合、エラー表示
		if( fm.groups.value == '' ){
			swal('{{ __('messages.dialog_none_group_msg') }}');
			return false;
		}

		//登録キャリア
        fm.career.value = $('[name="career"]:checked').val();

		//未決済注文
        fm.settlement.value = $('[name="settlement"]:checked').val();

		//送信端末
        fm.device.value = $('[name="device"]').val();

		//表示日時
        fm.regist_sdate.value = $('[name="regist_sdate"]').val();
        fm.regist_edate.value = $('[name="regist_edate"]').val();

		//公開日時
		fm.provision_sdate.value = $('[name="provision_sdate"]').val();
        fm.provision_edate.value = $('[name="provision_edate"]').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});

});
</script>

</body>
</html>
