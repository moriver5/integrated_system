@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>%変換設定</b>
				</div>
				<div class="panel-body">
					<span style="margin-left:12px;color:red;">※顧客依存データは別途、変換キーと変更内容をプログラムに組込む必要があります</span>
                    <form id="formConvert" class="form-horizontal" method="POST" action="/admin/member/master/convert/setting/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									{{ $db_data->links() }}
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:40px;">ID</td>
											<td style="padding:1px 0px;width:60px;">変換キー</td>
											<td style="padding:1px 0px;width:40px;">タイプ</td>
											<td style="padding:1px 0px;width:150px;">変更内容</td>
											<td style="padding:1px 0px;width:130px;">備考</td>
											<td style="padding:1px 2px;width:30px;">
												削除<br /><input type="checkbox" id="del_all" name="del_all" value="1">
											</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="del slt_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td>{{ $lines->id }}<input type="hidden" class="del" name="id[]" value="{{ $lines->id }}"></td>
											<td><input type="text" class="del key_data" id="key{{ $lines->id }}" name="key[]" value="{{ $lines->key }}" size="35" maxlength="{{ config('const.convert_key_max_length') }}"></td>
											<td>
												<select name="type[]" id="type{{ $lines->id }}" class="del type_data" style='height:28px;'>
													@foreach(config('const.list_convert_type') as $index => $value)
														@if( $lines->type == $index )
															<option value='{{ $index }}' selected>{{ $value }}</option>
														@else
															<option value='{{ $index }}'>{{ $value }}</option>
														@endif
													@endforeach
												</select>
											</td>
											<td><input type="text" class="del value_data" id="value{{ $lines->id }}" name="value[]" value="{{ $lines->value }}" size="45" maxlength="{{ config('const.convert_value_max_length') }}"></td>
											<td><input type="text" class="del" id="remarks{{ $lines->id }}" name="remarks[]" value="{{ $lines->memo }}" size="30" maxlength="{{ config('const.convert_memo_max_length') }}"></td>
											<td style="text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
										</tr>
										@endforeach
									</table>
								</div>
								<button type="submit" id="push_update" class="btn btn-primary">更新</button>
								<button type="submit" id="add_key" class="btn btn-primary">変換キー追加</button>
							</div>
						</center>
					</form>
                </div>
            </div>

			<center>
			<div class="panel panel-default">
			<table border="0" cellpadding="5" style="font-size:12px;">
				<tr>
					<td nowrap align="left">
						<font size="2" color="#FF3563"><b>※変換キーは必ず「-%string-」形式にして下さい。</b></font><br>
						<font size="2" color="#FF3563"><b>※TYPEの顧客データ依存とは変換内容が顧客によって変わるデータの事です。<br>　変換内容が一定のものはTYPEを「通常」で、変数名の箇所は空で登録して下さい。</b></font><br>
						<font size="2" color="#FF3563"><b>※TYPEの顧客データ依存を追加する際にはシステムまでご連絡下さい。</b></font><br>
					</td>
				</tr>
			</table>
			<br>
			<table border="1" cellpadding="2" cellspacing="1" style="font-size:12px;">
			<th nowrap style="text-align:center;background:wheat;">変換キー</th>
			<th nowrap style="text-align:center;background:wheat;">変換内容</th>
			<th nowrap style="text-align:center;background:wheat;">TYPE</th>
			<th nowrap style="text-align:center;background:wheat;">備考</th>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%race_name<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">レース名</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">的中実績</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%race_track<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">開催地</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">的中実績</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%ticket<1_1～6_3>-<font color=#FF3563>※1</font></td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">馬券種類</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">的中実績</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%dividend<1_1～6_3>-<font color=#FF3563>※1</font></td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">的中金額</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">的中実績</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%holding_date<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">開催日</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">的中実績</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%voice_name<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">投稿者</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ご利用者の声</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%voice_title<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">タイトル</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ご利用者の声</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%voice_comment<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">コメント</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ご利用者の声</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%voice_posting_date<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">投稿日時</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ご利用者の声</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>

			<tr style=Background-color:#ffffff;color:#000000;>
				<td nowrap align="center" style="font-size:12px;padding:5px;">-%%voice_image<1～6>-</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">添付画像</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ご利用者の声</td>
				<td nowrap align="center" style="font-size:12px;padding:5px;">ランディングページにて使用</td>
			</tr>
			</table>
			<br>
			<table border="0" cellpadding="5" style="font-size:12px;">
				<tr>
					<td nowrap align="left">
						<font size="2" color="#FF3563"><b>※的中、ご利用者の声の変換キーは必ず「-%%string-」形式にして下さい。</b></font><br>
						<font size="2" color="#FF3563"><b>※通常、顧客データ依存の％変換とは特性が異なるので別枠で用意</b></font><br>
						<font size="2" color="#FF3563"><b>※1 例 -%%ticket1_1　-%%ticket1_2　-%%ticket1_3　-%%ticket2_1 等、後方の数字は結果の順番となります</b></font><br>
						<font size="2" color="#FF3563"><b>※%変換を追加したい場合はシステムまでご連絡下さい。</b></font><br>
					</td>
				</tr>
			</table>
			</div>
			</center>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var sub_win;
$(document).ready(function(){
	//グループ管理で削除選択にチェックしたセルの色を変更
	$('.del_group').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#key" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#value" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#remarks" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#key" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#value" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#remarks" + this.id.replace(/del_group/,"")).css("background-color","white");
		}
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formConvert', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
	//キー追加ボタンを押下
	$('#add_key').on('click', function(){
		sub_win = window.open('/admin/member/master/convert/setting/add', 'convert_table', 'width=1000, height=350');
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
		//チェックされたらセルの色を変更
		if( $(this).is(':checked') ){
			$('.del').css("background-color","#F4FA58");
		//チェックが外されたらセルの色を元に戻す
		}else{
			$('.del').css("background-color","white");			
		}
	});
	
	//更新ボタン押下
	$('#push_update').on('click', function() {
		//変換キーに未入力があるか確認
		$('.key_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});

		//変換内容に未入力があるか確認
		$('.value_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});

	});
	
	//変換キーのテキストBOXにカーソルが当たったら
	$('.key_data').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});
	
	//変換内容のテキストBOXにカーソルが当たったら
	$('.value_data').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});

});
</script>

@endsection
