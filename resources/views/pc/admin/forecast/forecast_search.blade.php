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
    <title>{{ config('const.html_title') }}</title>

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
											<td class="admin_search" style="width:30px;">検索項目</td>
											<td style="width:100px;padding:5px;width:40px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($forecast_search_item as $lines)
													@if( !empty($session['forecast_search_item']) && $lines[0] == $session['forecast_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['forecast_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['forecast_search_item_value'] }}" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" autofocus>
												@endif
											</td>
										</tr>
										<tr>
											<td  class="admin_search">カテゴリ</td>
											<td colspan="2" style="padding:5px;">
												@foreach($forecast_search_category as $index => $line)
													@if( isset($session['forecast_category']) && $index == $session['forecast_category'] )
														&nbsp;&nbsp;<input type="radio" name="category" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="category" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="category" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search">表示グループ</td>
											<td colspan="2" style="padding:5px;">
												@foreach($db_group_data as $index => $lines)
													@if( $index != 0 && $index % 2 == 0 )
														<br />
													@endif
													@if( !empty($session['forecast_groups']) && preg_match("/^(".preg_replace("/,/", "|",$session['forecast_groups']).")$/",$lines->id) > 0 )
														&nbsp;<input type="checkbox" name="groups" value="{{ $lines->id }}" checked>{{ $lines->name }}
													@else
														&nbsp;<input type="checkbox" name="groups" value="{{ $lines->id }}">{{ $lines->name }}									
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search" style="width:80px;">キャンペーンID</td>
											<td colspan="2" style="padding:5px;">
												@if( !empty($session['forecast_campaigns']) )
													<input id="campaigns" type="text" class="form-control" name="campaigns" value="{{$session['forecast_campaigns']}}" placeholder="">
												@else
													<input id="campaigns" type="text" class="form-control" name="campaigns" placeholder="">
												@endif
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:80px;">表示日時</td>
											<td colspan="2" style="padding:5px;">
												@if( !empty($session['forecast_disp_sdate']) )
													&nbsp;&nbsp;<input id="disp_sdate" type="text" name="disp_sdate" value="{{$session['forecast_disp_sdate']}}" placeholder="開始表示日時">
												@else
													&nbsp;&nbsp;<input id="disp_sdate" type="text" name="disp_sdate" placeholder="開始表示日時">
												@endif
												@if( !empty($session['forecast_disp_edate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="disp_edate" type="text" name="disp_edate" value="{{$session['forecast_disp_edate']}}" placeholder="終了表示日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="disp_edate" type="text" name="disp_edate" placeholder="終了表示日時">
												@endif
											</td>
										</tr>
														<tr>
											<td class="admin_search" style="width:30px;">公開日時</td>
											<td colspan="2" style="padding:5px;">
												@if( !empty($session['forecast_open_sdate']) )
													&nbsp;&nbsp;<input id="open_sdate" type="text" name="open_sdate" value="{{$session['forecast_open_sdate']}}" placeholder="開始公開日時">
												@else
													&nbsp;&nbsp;<input id="open_sdate" type="text" name="open_sdate" placeholder="開始公開日時">
												@endif
												@if( !empty($session['forecast_open_edate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="open_edate" type="text" name="open_edate" value="{{$session['forecast_open_edate']}}" placeholder="終了公開日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="open_edate" type="text" name="open_edate" placeholder="終了公開日時">
												@endif
											</td>
										</tr>
										<tr>
											<td class="admin_search">公開</td>
											<td colspan="2" style="padding:5px;">
												@foreach($forecast_disp_type as $index => $line)
													@if( isset($session['forecast_disp_type']) && $index == $session['forecast_disp_type'] )
														&nbsp;&nbsp;<input type="radio" name="disp_type" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="disp_type" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="disp_type" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search">表示件数</td>
											<td colspan="2" style="padding:5px;">
												&nbsp;&nbsp;<select name="search_disp_num" style="padding:3px;">
												@foreach($search_disp_num as $index => $num)
													@if( isset($session['forecast_search_disp_num']) && $session['forecast_search_disp_num'] == $index )
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
										<tr>
											<td  class="admin_search">ソート</td>
											<td colspan="2" style="padding:5px;">
												@foreach($sort_list as $index => $line)
													@if( isset($session['forecast_sort']) && $index == $session['forecast_sort'] )
														&nbsp;&nbsp;<input type="radio" name="sort" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="sort" value="{{ $index }}" checked>{{ $line[1] }}													
													@else
														&nbsp;<input type="radio" name="sort" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
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

<script type="text/javascript">
$(document).ready(function(){
	$.datetimepicker.setLocale('ja');

	//登録日時-開始日
	$('#disp_sdate').datetimepicker();
	$('#open_sdate').datetimepicker();

	//登録日時-終了日
	$('#disp_edate').datetimepicker();
	$('#open_edate').datetimepicker();

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

		//カテゴリ
        fm.category.value = $('[name="category"]:checked').val();

		//表示グループ
        fm.groups.value = $('[name="groups"]:checked').map(function(){
            return $(this).val();
        }).get();

		//キャンペーン
        fm.campaigns.value = $('[name="campaigns"]').val();
		
		//表示日時
        fm.disp_sdate.value = $('[name="disp_sdate"]').val();
        fm.disp_edate.value = $('[name="disp_edate"]').val();

		//公開日時
		fm.open_sdate.value = $('[name="open_sdate"]').val();
        fm.open_edate.value = $('[name="open_edate"]').val();

		//公開
		fm.disp_type.value = $('[name="disp_type"]:checked').val();

		//表示件数
		fm.search_disp_num.value = $('[name="search_disp_num"]').val();

		//ソート
		fm.sort.value = $('[name="sort"]:checked').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});

});
</script>

</body>
</html>
