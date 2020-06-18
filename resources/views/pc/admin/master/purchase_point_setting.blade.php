@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><b>倍率設定</b></div>
                <div class="panel-body">
					<div style="margin-left:125px;margin-bottom:10px;color:red;font:normal 12px/120% 'メイリオ',sans-serif;">
						※ 日付の設定なしの場合、設定された倍率が反映されます。<br />
						※ 指定日時を過ぎると通常設定になります。
					</div>
                    <form id="formMagnification" class="form-horizontal" method="POST" action="/admin/member/master/magnification/setting/send">
                        {{ csrf_field() }}
                        <div class="form-group">
							@if( !empty($db_data) )
							<center>
								<table border="1" width="70%">
									<tr>
										<td style="text-align:center;background:wheat;font-weight:bold;">カテゴリ</td>
										<td colspan="3" style="padding:5px;font:normal 12px/120% 'メイリオ',sans-serif;">
										@if( !empty($db_data) )
											@foreach($db_data as $index => $lines)
												@if( $slt_category == $lines->id )
													&nbsp;{{ $lines->name  }}&nbsp;<input type="radio" name="category_name" value="{{ $lines->id }}" checked>&nbsp;&nbsp;
												@else
													&nbsp;{{ $lines->name  }}&nbsp;<input type="radio" name="category_name" value="{{ $lines->id }}">&nbsp;&nbsp;
												@endif
												@if( $index != 0 && $index % 10 == 9 )
													<br />
												@endif
											@endforeach
										@endif
										</td>
									</tr>
									<tr>
										<td style="text-align:center;background:wheat;font-weight:bold;">設定日時</td>
										<td colspan="3" style="padding:5px;"title="test">
											@if( !empty($start_date) )
												&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" size="30" value="{{$start_date}}" placeholder="開始日時">
											@else
												&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" size="30" placeholder="開始日時" title="test">
											@endif
											@if( !empty($end_date) )
												&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" size="30" value="{{$end_date}}" placeholder="終了日時">
											@else
												&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" size="30" placeholder="終了日時">
											@endif
										</td>
									</tr>
								</table>
							</center>
							@endif
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-5">
                                <button type="submit" id="magnification_update" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
			
            <div class="panel panel-default">
                <div class="panel-heading"><b>購入ポイントカテゴリ設定</b></div>
                <div class="panel-body">
                    <form id="formPointCategory" class="form-horizontal" method="POST" action="/admin/member/master/purchase/point/category/setting/send">
                        {{ csrf_field() }}
                        <div class="form-group">
							@if( !empty($db_data) )
							<center>
								<table border="1" width="95%">
									<tr>
										<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
											ID
										</td>
										<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:40px;">
											通常
										</td>
										<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:260px;">
											名前
										</td>
										<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;">
											備考
										</td>
										<td style="padding:2px;text-align:center;background:wheat;font-weight:bold;width:60px;">
											削除 <input type="checkbox" id="del_all" name="del_all" value="1">
										</td>
									</tr>
									@foreach($db_data as $index => $lines)
									<tr class="slt_category" id="slt_category{{ $lines->id }}">
										<td style="text-align:center;font:bold 12px/120% 'メイリオ',sans-serif;">
											<a href="/admin/member/master/purchase/point/category/setting/detail/{{ $lines->id }}" target="_blank">{{ $lines->id }}</a><input type="hidden" name="id[]" value="{{ $lines->id }}">
										</td>
										<td style="text-align:center;font-weight:bold;">
											<!-- 通常設定 -->
											@if( $lines->id == $default_id )
												<input type="radio" name="default_id" value="{{ $lines->id }}" checked>
											@else
												<input type="radio" name="default_id" value="{{ $lines->id }}">
											@endif
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" id="name{{ $lines->id }}" class="required form-control" style="font:normal 12px/120% 'メイリオ',sans-serif;" name="category_name[]" value="{{ $lines->name }}" maxlength="{{ config('const.pt_category_name_max_length') }}" required autofocus>
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" id="remarks{{ $lines->id }}" class="required form-control" style="font:normal 12px/120% 'メイリオ',sans-serif;" name="remarks[]" value="{{ $lines->remarks }}" maxlength="{{ config('const.pt_category_remarks_max_length') }}" required autofocus>
										</td>
										<td style="text-align:center;font-weight:bold;">
											<input type="checkbox" name="del[]" value="{{ $lines->id }}" id="del_category{{ $lines->id }}" class="del del_category">
										</td>
									</tr>
									@endforeach
								</table>
							</center>
							@endif
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
								<button type="submit" id="push_delete" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                <button type="submit" id="add_category" class="btn btn-primary">
                                    ポイントカテゴリ追加
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<!-- jQuery Liblary -->
<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('[name=start_date]').focusin(function(){
		$('[name=start_date]').attr("placeholder","");
	});

	$('[name=start_date]').focusout(function(){
		$('[name=start_date]').attr("placeholder","開始日時");
	});
	
	$('[name=end_date]').focusin(function(){
		$('[name=end_date]').attr("placeholder","");
	});

	$('[name=end_date]').focusout(function(){
		$('[name=end_date]').attr("placeholder","終了日時");
	});
	
	//登録日時-開始日
	$('#start_date').datetimepicker();

	//登録日時-終了日
	$('#end_date').datetimepicker();
	
	$('.del_category').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_category" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
			$("#name" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
			$("#remarks" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_category" + this.id.replace(/del_category/,"")).css("background-color","white");
			$("#name" + this.id.replace(/del_category/,"")).css("background-color","white");
			$("#remarks" + this.id.replace(/del_category/,"")).css("background-color","white");
		}
	});
	
	//ポイントカテゴリ追加画面表示
	$('#add_category').on('click', function(){
		sub_win = window.open('/admin/member/master/purchase/point/category/add', 'category_add', 'width=700, height=350');
		return false;
	});

	//倍率設定の更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMagnification', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
	//ポイントカテゴリ追加ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formPointCategory', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
		//セルの色を変更
		if( $(this).is(':checked') ){
			$(".slt_category").css("background-color","#F4FA58");
			$(".form-control").css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$(".slt_category").css("background-color","white");
			$(".form-control").css("background-color","white");
		}
	});

	//削除ボタン押下
	$('#push_delete').on('click', function() {
		//グループ名に未入力があるか確認
		$('.required').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});
	});

	$('.required').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});

	//ヒント表示
	$('#setdate').tooltip();
	
});
</script>

@endsection
