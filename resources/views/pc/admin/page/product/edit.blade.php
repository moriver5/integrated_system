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

	<!-- Vue.js -->
	<script src="https://cdn.jsdelivr.net/npm/vue"></script>

	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body>
<br />
<div class="container">
    <div class="row">
        <div class="col-md-13 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>商品設定編集</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>

				<div id="app">
					<div class="panel-body">
						<form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/page/product/edit/send">
							{{ csrf_field() }}

							<div class="form-group">
								<label for="tipster" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">予想師</label>
								<div class="col-md-4">
									<select name="tipster" class="form-control">
									@if( count($list_tipster) > 0 )
										@foreach($list_tipster as $lines)
											@if( $lines->id == $db_data->tipster )
											<option value="{{ $lines->id }}" selected>{{ $lines->name }}</option>
											@else
											<option value="{{ $lines->id }}">{{ $lines->name }}</option>
											@endif
										@endforeach
									@endif
									</select>
								</div>						
							</div>

							<div class="form-group">
								<label for="title" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">タイトル</label>
								<div class="col-md-9">
									<input id="title" type="text" class="form-control" name="title" value="{{ $db_data->title }}" autofocus>
								</div>						
							</div>

							<div class="form-group">
								<label for="title" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">コメント</label>
								<div class="col-md-9">
									<textarea id="comment" cols="10" rows="5" class="form-control" name="comment" placeholder="HTMLタグで入力してください" autofocus>{{ $db_data->comment }}</textarea>
								</div>						
							</div>

							<div class="form-group">
								<label for="open_flg" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開</label>
								<div class="col-md-4">
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
								<label for="order" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">表示順</label>
								<div class="col-md-2">
									<select id="order" class="form-control" name="order">
										@foreach($page_order as $num)
											@if( !empty($db_data) && $num == $db_data->order_num )
												<option value='{{$num}}' selected>{{$num}}</option>									
											@else
												<option value='{{$num}}'>{{$num}}</option>										
											@endif	
										@endforeach
									</select>
								</div>						
							</div>

							<div class="form-group">
								<label for="name" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">表示グループ</label>

								<div class="col-md-9">
									@if( !empty($db_data->groups) )
									<input id="group" type="text" class="form-control" name="groups" value="{{ $db_data->groups }}" autofocus placeholder="グループID (複数ある場合は,(半角カンマ)区切り)">
									@else
									<input id="group" type="text" class="form-control" name="groups" value="" autofocus placeholder="グループID (複数ある場合は,(半角カンマ)区切り)">
									@endif
								</div>		 
							</div>

							<div class="form-group">
								<label for="saddle" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">鞍数</label>
								<div class="col-md-4">
									@if( !empty($db_data->saddle) )
									<input id="saddle" type="text" class="form-control" name="saddle" value="{{ $db_data->saddle }}" placeholder="土曜日分　36鞍" autofocus>
									@else
									<input id="saddle" type="text" class="form-control" name="saddle" value="" placeholder="土曜日分　36鞍" autofocus>
									@endif
								</div>						
							</div>

							<div class="form-group">
								<label for="saddle" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">券種</label>
								<div class="col-md-4">
									@if( !empty($db_data->tickets) )
									<input id="tickets" type="text" class="form-control" name="tickets" value="{{ $db_data->tickets }}" placeholder="複勝" autofocus>
									@else
									<input id="tickets" type="text" class="form-control" name="tickets" value="" placeholder="複勝" autofocus>
									@endif
								</div>						
							</div>

							<div class="form-group">
								<label for="quantity" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">数量</label>
								<div class="col-md-4">
									@if( !empty($db_data->title) )
									<input id="quantity" type="text" class="form-control" name="quantity" value="{{ $db_data->quantity }}" autofocus>
									@else
									<input id="quantity" type="text" class="form-control" name="quantity" value="" autofocus>
									@endif
								</div>						
							</div>

							<div class="form-group">
								<label for="money" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">金額</label>

								<div class="col-md-2">
									<input id="money" type="text" class="form-control" name="money" value="{{ $db_data->money }}" autofocus>
								</div>		 
							</div>

							<div class="form-group">
								<label for="point" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">ポイント</label>

								<div class="col-md-2">
									@if( !is_null($db_data->point) )
									<input id="point" type="text" class="form-control" name="point" value="{{ $db_data->point }}" autofocus>
									@else
									<input id="point" type="text" class="form-control" name="point" value="" autofocus>
									@endif
								</div>		 
							</div>

							<div class="form-group">
								<label for="start_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開開始日時</label>

								<div class="col-md-4">
									<input id="start_date" type="text" class="form-control" name="start_date" value="{{ $db_data->start_date }}" placeholder="必須入力">
								</div>
							</div>

							<div class="form-group">
								<label for="end_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開終了日時</label>

								<div class="col-md-4">
									<input id="end_date" type="text" class="form-control" name="end_date" value="{{ $db_data->end_date }}" placeholder="必須入力">
								</div>
							</div>

							<div class="form-group">
								<label for="end_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">完売日時</label>

								<div class="col-md-4">
									<input id="sold_out_date" type="text" class="form-control" name="sold_out_date" value="{{ $db_data->sold_out_date }}" placeholder="必須入力">
								</div>
							</div>

							<div class="form-group">
								<label for="url" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">削除</label>

								<div class="col-md-1">
									<input id="del" type="checkbox" class="form-control" name="del" value="1">
								</div>
							</div>

							<div v-for="(item, index) in lists" class="form-group" style="margin-top:10px;height:55px;">
								<discount-form v-bind:item="item" v-bind:index="index"></discount-form>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-5">
									<button id="push_btn" type="submit" class="btn btn-primary">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</button>
									<button @click="addDiscountForm($event)" class="btn btn-primary">
										&nbsp;&nbsp;&nbsp;割引価格の入力フォームを追加&nbsp;&nbsp;&nbsp;
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
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){

	//URLプレビューボタン押下
	$('#url_preview').on('click', function(){
		//別ウィンドウを開く
		window.open($('[name="url"]').val(), 'url_preview', 'width=1000, height=600');
		return false;
	});

	var prev_win;
	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		var type = $('[name="type"]').val();

		//別ウィンドウを開く
		if( type == 1 ){
			prev_win = window.open('/admin/member/page/create/preview/' + type, 'top_content_preview', 'width=1000, height=900');
		}else{
			prev_win = window.open('/admin/member/page/create/preview/' + type, 'top_content_preview', 'width=690, height=500');
		}

		//別ウィンドウ読み込み後に実行
		prev_win.onload = function(){
			var dom = prev_win.document.getElementById('create_preview');

			//html_bodyを別ウィンドウに反映
			$(dom).html($('[name="html_body"]').val());
		};
		return false;
	});

	//プレビュー機能
	$('.contents').keyup(function(){
		//編集した内容を更新用パラメータに設定
		$('[name="html_body"]').val($('[name="html_body"]').val());

		//プレビュー処理
		if( prev_win ){
			var dom = prev_win.document.getElementById('create_preview');

			$(dom).html($('[name="html_body"]').val());
		}
	});

	//フォーカスされたら消す
	$('[name=start_date]').focusin(function(){
		$('[name=start_date]').attr("placeholder","");
	});

	//フォーカスが外されたら元に戻す
	$('[name=start_date]').focusout(function(){
		$('[name=start_date]').attr("placeholder","必須入力");
	});
	
	$('[name=end_date]').focusin(function(){
		$('[name=end_date]').attr("placeholder","");
	});

	$('[name=end_date]').focusout(function(){
		$('[name=end_date]').attr("placeholder","必須入力");
	});

	$('[name=end_date]').focusin(function(){
		$('[name=end_date]').attr("placeholder","");
	});

	$('[name=end_date]').focusout(function(){
		$('[name=end_date]').attr("placeholder","必須入力");
	});

	$('[name=sold_out_date]').focusin(function(){
		$('[name=sold_out_date]').attr("placeholder","");
	});

	$('[name=sold_out_date]').focusout(function(){
		$('[name=sold_out_date]').attr("placeholder","必須入力");
	});

	$('[name=money]').focusin(function(){
		$('[name=money]').attr("placeholder","");
	});

	$('[name=money]').focusout(function(){
		$('[name=money]').attr("placeholder","例：1000");
	});

	$('[name=point]').focusin(function(){
		$('[name=point]').attr("placeholder","");
	});

	$('[name=point]').focusout(function(){
		$('[name=point]').attr("placeholder","例：100");
	});

	$('[name=url]').focusin(function(){
		$('[name=url]').attr("placeholder","");
	});

	$('[name=url]').focusout(function(){
		$('[name=url]').attr("placeholder","例：https://dev.jra-yosou.net/member/campaign/1");
	});

	$('[name=html_body]').focusin(function(){
		$('textarea[name=html_body]').attr("placeholder","");
	});

	$('[name=html_body]').focusout(function(){
		$('textarea[name=html_body]').attr("placeholder","※TYPEによる入力イメージは、以下のように記述して下さい。\n\n【通常 の場合】\n  < table >\n    < td > この中のhtmlを設定 < /td > \n  < /table >\nの この中のhtmlを設定 を記述。\n<table><td>の部分はユーザーページのhtmlによって変動。\nメインコンテンツ部分を設定して下さい。\n\n【キャンペーン の場合】\n <html> ～ </html>\nを記述。");
	});

	$.datetimepicker.setLocale('ja');

	//公開開始日時
	$('#start_date').datetimepicker();

	//公開終了日時
	$('#end_date').datetimepicker();

	//完売日時
	$('#sold_out_date').datetimepicker();

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

var parent = new Vue({
	el:'#app',
	data:{
		lists:@json($list_discount)
	},
	methods:{
		addDiscountForm: function(event){
			if(event){
				event.preventDefault();
			}
			this.lists.push({title:'', groups:'', hold_pt:'', money:''});
		},
	},
	components: {
		'discount-form':{
			props:['item', 'index'],
			methods:{
				delDiscountForm: function(index, event){
					if(event){
						event.preventDefault();
					}
					parent.lists.splice(index,1);
				}
			},
			template:
				`
					<div class="form-group">
						<label for="end_date" class="col-md-2 control-label" style="top:25px;color:black;font:bold 12px/120% メイリオ,sans-serif;">割引価格@{{index+1}}</label>
						<div class="col-md-5">
							割引タイトル<input type="text" class="form-control" v-bind:name="'title'+(index+1)" v-model="item.title" autofocus>
						</div>
						<div class="col-md-2">
							所属グループ<input type="text" class="form-control" v-bind:name="'groups'+(index+1)" v-model="item.groups" autofocus>
						</div>
						<div class="col-md-1">
							保有PT<input type="text" class="form-control" v-bind:name="'hold_pt'+(index+1)" v-model="item.hold_pt" autofocus>
						</div>
						<div class="col-md-1">
							金額<input type="text" class="form-control" v-bind:name="'money'+(index+1)" v-model="item.money" autofocus>
						</div>
						<div class="col-md-1">
							<br><button v-on:click="delDiscountForm(index, $event)" type="submit" class="btn btn-primary">削除</button>
						</div>
					</div>
				`
		}
	}
});

</script>

</body>
</html>

