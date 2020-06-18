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

	<!-- Vue.js -->
	<script src="https://cdn.jsdelivr.net/npm/vue"></script>
</head>
<body>
<br />
<div class="container" id="app">
    <div class="row">
        <div class="col-md-11 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>的中実績編集</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>

                <div class="panel-body">
                    <form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/page/achievement/edit/send">
                        {{ csrf_field() }}


                        <div class="form-group">
                            <label for="priority_id" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">的中実績選択</label>
                            <div class="col-md-2" id="type">
								商品 <input type="radio" @click="clickType(0)" v-model="type" name="type" value="0">
								TOPコンテンツ <input type="radio" @click="clickType(1)" v-model="type" name="type" value="1">
                            </div>	
                            <div class="col-md-4">
								<select v-model="product_id" name="product_id" class="form-control">
									<option v-for="(item, index) in lists"  v-bind:value="item.id">@{{ item.title }}　(@{{ label }}：@{{ item.id }})</option>
								</select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="priority_id" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">優先順位表示ID</label>
                            <div class="col-md-2">
								<input id="priority_id" type="text" class="form-control" name="priority_id" value="{{ $db_data->priority_id }}" autofocus placeholder="必須入力">
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="open_flg" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開</label>
                            <div class="col-md-2">
								<select id="open_flg" class="form-control" name="open_flg">
									@foreach($list_open_flg as $lines)
										@if( $lines[0] == $db_data->open_flg )
											<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>										
										@else
											<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
										@endif
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="race_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">開催日</label>

                            <div class="col-md-3">
								@if( !empty($db_data->race_date) )
									<input id="race_date" type="text" class="form-control" name="race_date" value="{{ $db_data->race_date }}" placeholder="必須入力">
								@else
									<input id="race_date" type="text" class="form-control" name="race_date" placeholder="必須入力">
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="race_name" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">レース名</label>
                            <div class="col-md-6">
								<input id="race_name" type="text" class="form-control" name="race_name" value="{{ $db_data->race_name }}" autofocus placeholder="必須入力">
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="msg1" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">結果</label>

                            <div class="col-md-6">
								@if( !empty($db_data->msg1) )
                                <input id="msg1" type="text" class="form-control" name="msg1" value="{{ $db_data->msg1 }}" size=22 autofocus>
								@else
                                <input id="msg1" type="text" class="form-control" name="msg1" value="" size=22 autofocus>
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="msg2" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;"></label>
                            <div class="col-md-6">
								@if( !empty($db_data->msg2) )
                                <input id="msg2" type="text" class="form-control" name="msg2" value="{{ $db_data->msg2 }}" size=22 autofocus>
								@else
                                <input id="msg2" type="text" class="form-control" name="msg2" value="" size=22 autofocus>
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="msg3" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;"></label>
                            <div class="col-md-6">
								@if( !empty($db_data->msg3) )
                                <input id="msg3" type="text" class="form-control" name="msg3" value="{{ $db_data->msg3 }}" size=22 autofocus>
								@else
                                <input id="msg3" type="text" class="form-control" name="msg3" value="" size=22 autofocus>
								@endif
                            </div>
                        </div>

                        <div class="form-group">
							<label for="type" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">コメント</label>

							<div class="col-md-5">
								<textarea cols="70" rows="4" name="comment" id="comment" class="contents" >{{ $db_data->memo }}</textarea>
                            </div>
						</div>

                        <div class="form-group">
                            <label for="url" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">削除</label>

                            <div class="col-md-1">
                                <input id="del" type="checkbox" class="form-control" name="del" value="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-5">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
                            </div>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
/*
 * 的中実績部分
 */
var parent = new Vue({
	el:'#app',
	data:{
		label:'',
		product_id:{{ $db_data->product_id }},
		type:{{ $db_data->type }},
		lists:[],
		listProduct:@json($list_product_data),
		listTopcontent:@json($list_topcontent_data)
	},
	mounted:function(){
		//選択した商品
		if( this.type == 0 ){
			this.label = '商品ID';
			this.type.checked = true;
			this.product_id.selected = true;
			this.lists = this.listProduct;

		//選択した予想
		}else{
			this.label = '予想ID';
			this.type.checked = true;
			this.product_id.selected = true;
			this.lists = this.listTopcontent;
		}
	},
	methods:{
		//クリックで商品とTOPコンテンツのリストを切換える
		clickType: function(type){
			if( type == 0 ){
				this.label = '商品ID';
				this.lists = this.listProduct;
			}else{
				this.label = 'コンテンツID';
				this.lists = this.listTopcontent;
			}
		},
	},
});

$(document).ready(function(){
	$('input[name=type]').on('click', function(){
		let type = $('input[name=type]:checked').val();
		if( type == 0 ){
			$('#product').css('display', 'block');
			$('#forecast').css('display', 'none');
		}else{
			$('#product').css('display', 'none');
			$('#forecast').css('display', 'block');
		}
	});

	//フォーカスされたら消す
	$('[name=race_date]').focusin(function(){
		$('[name=race_date]').attr("placeholder","");
	});

	//フォーカスが外されたら元に戻す
	$('[name=race_date]').focusout(function(){
		$('[name=race_date]').attr("placeholder","必須入力");
	});

	$.datetimepicker.setLocale('ja');

	//公開開始日時
	$('#race_date').datetimepicker({format:'Y-m-d'});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	//更新ボタンを押下
	$('#push_btn').click(function(){
		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formEdit', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
		
	});
});
</script>

</body>
</html>

