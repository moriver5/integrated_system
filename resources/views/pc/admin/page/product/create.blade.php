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
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>商品設定</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
				<div id="app">
					<div class="panel-body">
						<form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/page/product/create/send">
							{{ csrf_field() }}

							<div class="form-group">
								<label for="tipster" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">予想師</label>
								<div class="col-md-4">
									<select name="tipster" class="form-control">
									@if( count($list_tipster) > 0 )
										@foreach($list_tipster as $lines)
										<option value="{{ $lines->id }}">{{ $lines->name }}</option>
										@endforeach
									@endif
									</select>
								</div>						
							</div>

							<div class="form-group">
								<label for="title" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">タイトル</label>
								<div class="col-md-9">
									@if( !empty($db_data->title) )
									<input id="title" type="text" class="form-control" name="title" value="{{ $db_data->title }}" autofocus>
									@else
									<input id="title" type="text" class="form-control" name="title" value="" autofocus>
									@endif
								</div>						
							</div>

							<div class="form-group">
								<label for="title" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">コメント</label>
								<div class="col-md-9">
									<textarea id="comment" cols="10" rows="5" class="form-control" name="comment" placeholder="HTMLタグで入力してください" autofocus></textarea>
								</div>						
							</div>

							<div class="form-group">
								<label for="open_flg" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開</label>
								<div class="col-md-4">
									<select id="open_flg" class="form-control" name="open_flg">
										@foreach($list_open_flg as $lines)
											@if( !empty($db_data->open_flg) &&  $db_data->open_flg == $lines[0] )
											<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>										
											@else
											<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
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
									<input id="tickets" type="text" class="form-control" name="tickets" value="{{ $db_data->tickets }}" placeholder="複勝 autofocus>
									@else
									<input id="tickets" type="text" class="form-control" name="tickets" value="" placeholder="複勝" autofocus>
									@endif
								</div>						
							</div>

							<div class="form-group">
								<label for="quantity" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">数量</label>
								<div class="col-md-4">
									@if( !empty($db_data->title) )
									<input id="quantity" type="text" class="form-control" name="quantity" value="{{ $db_data->title }}" autofocus>
									@else
									<input id="quantity" type="text" class="form-control" name="quantity" value="" autofocus>
									@endif
								</div>						
							</div>

							<div class="form-group">
								<label for="money" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">金額</label>

								<div class="col-md-4">
									@if( !empty($db_data->money) )
									<input id="money" type="text" class="form-control" name="money" value="{{ $db_data->groups }}" autofocus>
									@else
									<input id="money" type="text" class="form-control" name="money" value="" autofocus>
									@endif
								</div>		 
							</div>

							<div class="form-group">
								<label for="point" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">ポイント</label>

								<div class="col-md-4">
									@if( !empty($db_data->point) )
									<input id="point" type="text" class="form-control" name="point" value="{{ $db_data->groups }}" autofocus>
									@else
									<input id="point" type="text" class="form-control" name="point" value="" autofocus>
									@endif
								</div>		 
							</div>

							<div class="form-group">
								<label for="start_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開開始日時</label>

								<div class="col-md-4">
									@if( !empty($db_data->start_date) )
										<input id="start_date" type="text" class="form-control" name="start_date" value="{{ $db_data->start_date }}" placeholder="必須入力">
									@else
										<input id="start_date" type="text" class="form-control" name="start_date" placeholder="必須入力">
									@endif
								</div>
							</div>

							<div class="form-group">
								<label for="end_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">公開終了日時</label>

								<div class="col-md-4">
									@if( !empty($db_data->end_date) )
										<input id="end_date" type="text" class="form-control" name="end_date" value="{{ $db_data->end_date }}" placeholder="必須入力">
									@else
										<input id="end_date" type="text" class="form-control" name="end_date" placeholder="必須入力">
									@endif
								</div>
							</div>

							<div class="form-group">
								<label for="end_date" class="col-md-2 control-label" style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">完売日時</label>

								<div class="col-md-4">
									<input id="sold_out_date" type="text" class="form-control" name="sold_out_date" value="" placeholder="必須入力">
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button @click="sendProduct($event)" class="btn btn-primary">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;設定&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</button>
								<button @click="addDiscountForm($event)" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;割引価格の入力フォームを追加&nbsp;&nbsp;&nbsp;
								</button>

								</div>
							</div>

							<hr>
							<div v-for="(item, index) in lists" class="form-group" style="margin-top:10px;height:55px;">
								<discount-form v-bind:item="item" v-bind:index="index"></discount-form>
							</div>
						<input type="hidden" name="page" value="{{ $page }}">
						</form>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/vue-swal"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	var discount = 0;

	//フォーカスが外されたら元に戻す
	$('[name=start_date]').focusout(function(){
		$('[name=start_date]').attr("placeholder","必須入力");
	});

	$('[name=end_date]').focusout(function(){
		$('[name=end_date]').attr("placeholder","必須入力");
	});

	$('[name=sold_out_date]').focusout(function(){
		$('[name=sold_out_date]').attr("placeholder","必須入力");
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
	
	//新規作成ボタンを押下
//	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
//		submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.setting_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
//	});

});

/*
 * Vue.jsでフォーム追加
 */
var parent = new Vue({
	el:'#app',
	data:{
		lists:[]
	},
	methods:{
		addDiscountForm: function(event){
			if(event){
				event.preventDefault();
			}
			this.lists.push({title:'', groups:'', hold_pt:'', money:''});
		},
		sendProduct:function(event){
			if(event){
//				event.preventDefault();
			}
			submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.setting_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
		}
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
						<div class="col-md-3">
							割引タイトル<input type="text" class="form-control" v-bind:name="'title'+(index+1)" v-model="item.title" autofocus>
						</div>
						<div class="col-md-2">
							所属グループ<input type="text" class="form-control" v-bind:name="'groups'+(index+1)" v-model="item.groups" autofocus>
						</div>
						<div class="col-md-1">
							保有PT<input type="text" class="form-control" v-bind:name="'hold_pt'+(index+1)" v-model="item.hold_pt" autofocus>
						</div>
						<div class="col-md-2">
							金額<input type="text" class="form-control" v-bind:name="'money'+(index+1)" v-model="item.money" autofocus>
						</div>
						<div class="col-md-1">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button v-on:click="delDiscountForm(index, $event)" type="submit" class="btn btn-primary">削除</button>
						</div>
					</div>
				`
		}
	}
});

</script>

</body>
</html>

