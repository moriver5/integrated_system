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
											<td style="text-align:center;background:wheat;font-weight:bold;width:30px;">検索項目</td>
											<td style="width:100px;padding:5px;width:40px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($page_search_item as $lines)
													@if( !empty($session['search_item']) && $lines[0] == $session['search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索項目の値 -->
												<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" autofocus>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">TYPE</td>
											<td colspan="2" style="padding:5px;">
												@foreach($page_type as $index => $line)
													@if( isset($session['page_type']) && $index == $session['page_type'] )
														&nbsp;&nbsp;<input type="radio" name="page_type" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="page_type" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="page_type" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">閲覧人数表示</td>
											<td colspan="2" style="padding:5px;">
											@if( !empty($view_num) )
												@foreach($view_num as $index => $line)
													@if( isset($session['view_num']) && $index == $session['view_num'] )
														&nbsp;&nbsp;<input type="radio" name="view_num" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="view_num" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="view_num" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">表示件数</td>
											<td colspan="2" style="padding:5px;">
												&nbsp;&nbsp;<select name="search_disp_num" style="padding:3px;">
												@foreach($search_disp_num as $index => $num)
													@if( isset($session['search_disp_num']) && $session['search_disp_num'] == $index )
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
											<td style="text-align:center;background:wheat;font-weight:bold;">ソート</td>
											<td colspan="2" style="padding:5px;">
												@foreach($sort_list as $index => $line)
													@if( isset($session['sort']) && $index == $session['sort'] )
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

		//TYPE
        fm.page_type.value = $('[name="page_type"]:checked').val();
		
		//閲覧人数表示
        fm.view_num.value = $('[name="view_num"]:checked').val();

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
