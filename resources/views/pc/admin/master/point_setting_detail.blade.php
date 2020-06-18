@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 14px/140% 'メイリオ',sans-serif;height:"><b>{{ $slt_category }}</b></div>
                <div class="panel-body">
                    <form id="formPointSetting" class="form-horizontal" method="POST" action="/admin/member/master/purchase/point/category/setting/detail/{{ $id }}/send">
                        {{ csrf_field() }}
                        <div class="form-group">
							@if( !empty($db_data) )
							<center>
								<table border="1" width="95%">
									<tr>
										<td style="padding:3px;text-align:center;background:wheat;font-weight:bold;width:4%;">
											ID
										</td>
										<td style="padding:3px;text-align:center;background:wheat;font-weight:bold;width:8%;">
											金額
										</td>
										<td style="padding:3px;text-align:center;background:wheat;font-weight:bold;width:8%;">
											ポイント
										</td>
										<td style="padding:3px;text-align:center;background:wheat;font-weight:bold;width:30%;">
											表示テキスト
										</td>
										<td style="padding:3px;text-align:center;background:wheat;font-weight:bold;width:30%;">
											備考
										</td>
										<td style="padding:3px;text-align:center;background:wheat;font-weight:bold;width:5%;">
											削除 <input type="checkbox" id="del_all" name="del_all" value="1">
										</td>
									</tr>
									@foreach($db_data as $index => $lines)
									<tr class="slt_category" id="slt_category{{ $lines->id }}">
										<td style="text-align:center;font-weight:bold;">
											{{ $lines->id }}<input type="hidden" name="id[]" value="{{ $lines->id }}">
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" id="money{{ $lines->id }}" class="required form-control" style="font:normal 12px/120% 'メイリオ',sans-serif;height:" name="money[]" value="{{ $lines->money }}" required autofocus>
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" id="point{{ $lines->id }}" class="required form-control" style="font:normal 12px/120% 'メイリオ',sans-serif;" name="point[]" value="{{ $lines->point }}" required autofocus>
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" id="disp_msg{{ $lines->id }}" class="form-control" style="font:normal 12px/120% 'メイリオ',sans-serif;" name="disp_msg[]" value="{{ $lines->disp_msg }}" maxlength="{{ config('const.pt_setting_text_max_length') }}" autofocus>
										</td>
										<td style="text-align:center;font-weight:bold;">
			                                <input type="text" id="remarks{{ $lines->id }}" class="form-control" style="font:normal 12px/120% 'メイリオ',sans-serif;" name="remarks[]" value="{{ $lines->remarks }}" maxlength="{{ config('const.pt_setting_remarks_max_length') }}" autofocus>
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
								<button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                <button type="submit" id="add_category" class="btn btn-primary">
                                    ポイント設定追加
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
	
	$('.del_category').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_category" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
			$("#money" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
			$("#point" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
			$("#disp_msg" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
			$("#remarks" + this.id.replace(/del_category/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_category" + this.id.replace(/del_category/,"")).css("background-color","white");
			$("#money" + this.id.replace(/del_category/,"")).css("background-color","white");
			$("#point" + this.id.replace(/del_category/,"")).css("background-color","white");
			$("#disp_msg" + this.id.replace(/del_category/,"")).css("background-color","white");
			$("#remarks" + this.id.replace(/del_category/,"")).css("background-color","white");
		}
	});
	
	//購入ポイント追加画面表示
	$('#add_category').on('click', function(){
		sub_win = window.open('/admin/member/master/purchase/point/category/setting/detail/{{ $id }}/add', 'category_add', 'width=1000, height=350');
		return false;
	});

	//ポイントカテゴリ追加ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formPointSetting', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

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

	//更新ボタン押下
	$('#push_update').on('click', function() {
		//金額・ポイントに未入力があるか確認
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

});
</script>

@endsection
