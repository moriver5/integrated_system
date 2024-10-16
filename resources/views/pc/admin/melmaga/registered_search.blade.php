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
											<td style="padding:5px;width:50px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($registered_search_item as $lines)
													@if( !empty($session['registered_search_item']) && $lines[0] == $session['registered_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['registered_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['registered_search_item_value'] }}" placeholder="半角カンマ区切りで複数可" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" placeholder="半角カンマ区切りで複数可" autofocus>
												@endif
											</td>
											<td style="width:50px;padding:5px;">
												<select name="search_like_type" class="form-control">
												@foreach($registered_search_like_type as $index => $lines)
													@if( !empty($session['registered_search_like_type']) && $index == $session['registered_search_like_type'] )
														<option value="{{ $index }}" selected>{{ $lines[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $lines[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td class="admin_search">推定時間</td>
											<td colspan="3" style="padding:5px;vertical-align:middle;">
												@foreach($registered_specified_time as $time)
													@if( !empty($session['registered_specified_time']) && preg_match("/^(".preg_replace("/,/", "|",$session['registered_specified_time']).")$/", $time) > 0 )
														&nbsp;<input type="checkbox" name="specified_time" value="{{ $time }}" checked>{{ $time }}
													@else
														&nbsp;<input type="checkbox" name="specified_time" value="{{ $time }}">{{ $time }}									
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search">有効/無効</td>
											<td colspan="3" style="padding:5px;">
												@foreach($registered_enable_disable as $index => $line)
													@if( isset($session['registered_enable_flg']) && $index == $session['registered_enable_flg'] )
														&nbsp;&nbsp;<input type="radio" name="enable_flg" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="enable_flg" value="{{ $index }}" checked>{{ $line[1] }}													
													@else
														&nbsp;<input type="radio" name="enable_flg" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td  class="admin_search">ソート</td>
											<td colspan="3" style="padding:5px;">
												@foreach($sort_list as $index => $line)
													@if( isset($session['registered_sort']) && $index == $session['registered_sort'] )
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

		//LIKE検索タイプ
		fm.search_like_type.value = $('[name="search_like_type"]').val();

		//推定時間
		fm.specified_time.value = $('[name="specified_time"]:checked').map(function(){
			return $(this).val();
		}).get();

		//有効/無効
		fm.enable_flg.value = $('[name="enable_flg"]:checked').val();

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
