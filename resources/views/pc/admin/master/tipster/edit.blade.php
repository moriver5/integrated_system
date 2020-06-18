@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:wheat;"><b>予想師作成</b></div>
                <div class="panel-body">
					<div class="form-group{{ $errors->has('contents') ? ' has-error' : '' }}">
						<div class="col-md-12">
							<table width="100%">
								<tr>
									<td>
										<form id="formImageUpload1" class="form-horizontal" method="POST" action="/admin/member/master/tipster/setting/upload/send">
										{{ csrf_field() }}
										<div class="col-md-10"　id="file_upload_section1" style="width:315px;">
											<b>的中実績</b>
											<div id="drop1" style="text-align:center;width:240px; height:180px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop1', 'white')" ondragover="onDragOver(event, 'drop1', 'wheat')" ondrop="onDrop(event, 'formImageUpload1', 'import_file', '{{csrf_token()}}', '{{ __('messages.dialog_img_upload_end_msg') }}', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id', 'type'], 'post', '10000', '{{ $redirect_url }}', 'result1')">
												<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください</div>
												<center><div id="result1" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;margin:20px;width:240px;"></div></center>
											</div>
										</div>
										<input type="hidden" name="type" value="hit">
										<input type="hidden" name="edit_id" value="{{ $edit_id }}">
										</form>
									</td>
									<td style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
										@if( !empty($db_data->id) )
											<b>
												設定済：80%縮小表示<br>
												画像名：{{ $db_data->id }}_hit.png
											</b><br />
											<a href="/{{ config('const.tipster_images_path') }}/{{ $db_data->id }}_hit.png?ver={{$ver}}" target="_blank"><canvas id="imgpreview1" src="/{{ config('const.tipster_images_path') }}/{{ $db_data->id }}_hit.png?ver={{$ver}}" /></a>
										@else
										<div style="width:400px;text-align:center;"><b>画像未設定</b></div>
										@endif
									</td>
								</tr>
								<tr>
									<td>
										<form id="formImageUpload2" class="form-horizontal" method="POST" action="/admin/member/master/tipster/setting/upload/send">
										{{ csrf_field() }}
										<div class="col-md-10"　id="file_upload_section2" style="width:315px;">
											<b>キャンペーン予想師の紹介</b>
											<div id="drop2" style="text-align:center;width:240px; height:180px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop2', 'white')" ondragover="onDragOver(event, 'drop2', 'wheat')" ondrop="onDrop(event, 'formImageUpload2', 'import_file', '{{csrf_token()}}', '{{ __('messages.dialog_img_upload_end_msg') }}', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id', 'type'], 'post', '10000', '{{ $redirect_url }}', 'result2')">
												<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください</div>
												<center><div id="result2" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;margin:20px;width:240px;"></div></center>
											</div>
										</div>
										<input type="hidden" name="type" value="intro">
										<input type="hidden" name="edit_id" value="{{ $edit_id }}">
										</form>
									</td>
									<td style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
										@if( !empty($db_data->id) )
											<b>
												設定済：80%縮小表示<br>
												画像名：{{ $db_data->id }}_intro.png
											</b><br />
											<a href="/{{ config('const.tipster_images_path') }}/{{ $db_data->id }}_intro.png?ver={{$ver}}" target="_blank"><canvas id="imgpreview2" src="/{{ config('const.tipster_images_path') }}/{{ $db_data->id }}_intro.png?ver={{$ver}}" /></a>
										@else
										<div style="width:400px;text-align:center;"><b>画像未設定</b></div>
										@endif
									</td>
								</tr>
								<tr>
									<td>
										<form id="formImageUpload3" class="form-horizontal" method="POST" action="/admin/member/master/tipster/setting/upload/send">
										{{ csrf_field() }}
										<div class="col-md-10"　id="file_upload_section3" style="width:315px;">
											<b>現在販売中のキャンペーン</b>
											<div id="drop3" style="text-align:center;width:240px; height:180px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop3', 'white')" ondragover="onDragOver(event, 'drop3', 'wheat')" ondrop="onDrop(event, 'formImageUpload3', 'import_file', '{{csrf_token()}}', '{{ __('messages.dialog_img_upload_end_msg') }}', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id', 'type'], 'post', '10000', '{{ $redirect_url }}', 'result3')">
												<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください</div>
												<center><div id="result3" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;margin:20px;width:240px;"></div></center>
											</div>
										</div>
										<input type="hidden" name="type" value="sale">
										<input type="hidden" name="edit_id" value="{{ $edit_id }}">
										</form>
									</td>
									<td style="color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
										@if( !empty($db_data->id) )
											<b>
												設定済：80%縮小表示<br>
												画像名：{{ $db_data->id }}_sale.png
											</b><br />
											<a href="/{{ config('const.tipster_images_path') }}/{{ $db_data->id }}_sale.png?ver={{$ver}}" target="_blank"><canvas id="imgpreview3" src="/{{ config('const.tipster_images_path') }}/{{ $db_data->id }}_sale.png?ver={{$ver}}" /></a>
										@else
										<div style="width:400px;text-align:center;"><b>画像未設定</b></div>
										@endif
									</td>
								</tr>
							</table>
						</div>
					</div>
                    <form id="formTipster" class="form-horizontal" method="POST" action="/admin/member/master/tipster/setting/edit/{{ $db_data->id }}/send">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('contents') ? ' has-error' : '' }}">
                            <div class="col-md-12">
								<table width="100%">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2" style="text-align:center;font-weight:bold;">
											予想師の名前 <input type="text" name="name" value="{{ $db_data->name }}" class="form-control">
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2" style="text-align:center;font-weight:bold;">
											予想師紹介
										</td>
									</tr>
									<tr>
										<td colspan="2" style="text-align:center;font-weight:bold;">
											<textarea id="contents" cols="40" rows="10" class="form-control" style="font:normal 17px/120% 'メイリオ',sans-serif;" name="contents" placeholder="" autofocus>{{ $db_data->contents }}</textarea>
										</td>
									</tr>
								</table>

                                @if ($errors->has('point'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('contents') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('disp_flg') ? ' has-error' : '' }}">
                            <div class="col-md-2">
								<table width="100%">
									<tr>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:150px;">
											有効
										</td>
										<td style="text-align:center;font-weight:bold;width:30px;">
											@if( $db_data->disp_flg )
				                                <input type="checkbox" name="disp_flg" value="1" class="form-control" checked>
											@else
				                                <input type="checkbox" name="disp_flg" value="1" class="form-control">
											@endif
										</td>
									</tr>
								</table>

                                @if ($errors->has('disp_flg'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('disp_flg') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('is_star') ? ' has-error' : '' }}">
                            <div class="col-md-2">
								<table width="100%">
									<tr>
										<td style="padding-top:5px;text-align:left;font-size:17px;font-weight:bold;width:80px;">
											3つ星予想師
										</td>
										<td style="text-align:center;font-weight:bold;width:15px;">
											@if( $db_data->is_star )
				                                <input type="checkbox" name="is_star" value="1" class="form-control" checked>
											@else
				                                <input type="checkbox" name="is_star" value="1" class="form-control">
											@endif
										</td>
									</tr>
								</table>

                                @if ($errors->has('is_star'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('is_star') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-5">
                                <button type="submit" class="btn btn-primary">
                                    更新
                                </button>
                                <a href="/admin/member/master/tipster/setting" class="btn btn-primary">戻る</a>
                            </div>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin/image.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	let banner_url;
	banner_url = $('#imgpreview1').attr('src');
	resizeImage('imgpreview1', banner_url, 0.2);
	
	banner_url = $('#imgpreview2').attr('src');
	resizeImage('imgpreview2', banner_url, 0.2);
	
	banner_url = $('#imgpreview3').attr('src');
	resizeImage('imgpreview3', banner_url, 0.2);

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formTipster', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_setting_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection
